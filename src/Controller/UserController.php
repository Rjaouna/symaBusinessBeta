<?php

namespace App\Controller;

use App\Entity\Commercial;
use App\Entity\User;
use App\Form\UserType;
use App\Form\CommercialType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Repository\QuotaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[IsGranted('ROLE_ADMIN')]
#[Route('/app/utilisateurs')]
final class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les utilisateurs avec un type différent de Commercial et Revendeur
        $users = $entityManager->createQuery(
            'SELECT u
        FROM App\Entity\User u
        WHERE u.type NOT IN (:excludedTypes)'
        )
            ->setParameter('excludedTypes', ['Commercial', 'Revendeur'])
            ->getResult();

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/revendeurs', name: 'app_user_revendeurs', methods: ['GET'])]
    public function revendeurs(UserRepository $userRepository): Response
    {
        // Récupérer les utilisateurs avec un type différent de Commercial et Revendeur
        $users = $userRepository->findBy(['type' => 'Revendeur']);

        return $this->render('user/revendeurs.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/commeciaux', name: 'app_user_commerciaux', methods: ['GET'])]
    public function commerciaux(UserRepository $userRepository): Response
    {
        // Récupérer les utilisateurs avec un type différent de Commercial et Revendeur
        $users = $userRepository->findBy(['type' => 'Commercial']);

        return $this->render('user/commerciaux.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/switch-role', name: 'switch_role', methods: ['POST'])]
    public function switchRole(Request $request): JsonResponse
    {
        $role = $request->request->get('role');
        $user = $this->getUser();

        // Check if user is authenticated
        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Check if role is valid
        if (!in_array($role, ['ROLE_USER', 'ROLE_ADMIN'], true)) {
            return new JsonResponse(['error' => 'Invalid role'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Check if user has permission to switch to this role
        if (!in_array($role, $user->getRoles(), true)) {
            return new JsonResponse(['error' => 'Unauthorized role switch'], JsonResponse::HTTP_FORBIDDEN);
        }

        // Update user's active role
        $user->setActiveRole($role);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Role switched successfully']);
    }



    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/nouveau/commercial', name: 'app_user_new_commercial', methods: ['GET', 'POST'])]
    public function newcommercial(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer,
        QuotaRepository $quota
    ): Response {
        // Créer une nouvelle instance de User
        $user = new User();
        $quota = $quota->find(1);

        // Créer le formulaire CommercialType
        $form = $this->createForm(CommercialType::class, $user);
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Générer un mot de passe aléatoire
            $plainPassword = $this->generateRandomPassword();

            // Encoder le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plainPassword
            );
            $user->setPassword($hashedPassword);

            // Assigner le rôle "ROLE_COMMERCIAL" à l'utilisateur
            $user->setTotalBonus(10); // Assigner le bonus
            $user->setSim5Usage(0); // Assigner le bonus
            $user->setSim10Usage(0); // Assigner le bonus
            $user->setSim15Usage(0); // Assigner le bonus
            $user->setSim20Usage(0); // Assigner le bonus
            
            if ($quota) {
                $user->setQuotas($quota); // Assigner le quota récupéré
            } else {
                // Gérer le cas où le quota avec l'ID 1 n'existe pas (optionnel)
                $this->addFlash('error', 'Le quota demandé n\'existe pas.');
            }
            if ($form->getData()->isCommercial()) {
                $user->setRoles(['ROLE_COMMERCIAL']);
                $user->setType('Commercial');
                $commercial = new Commercial();
                $commercial->setNom($form->getData()->getNomResponsable());
                $commercial->setEmail($form->getData()->getEmail());
                $entityManager->persist($commercial);
                $entityManager->flush();
            } else {
                $user->setRoles(['ROLE_USER']);
                $user->setType('Revendeur');
                // Enregistrer l'utilisateur en base de données
                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
            }


            

            // Envoyer le mot de passe par e-mail
            $this->sendPasswordEmail($mailer, $user, $plainPassword);

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Commercial créé avec succès. Le mot de passe a été envoyé par e-mail.');

            // Rediriger vers la liste des utilisateurs
            return $this->redirectToRoute('app_user_commerciaux', [], Response::HTTP_SEE_OTHER);

        }

        // Rendre le formulaire dans le template
        return $this->render('commercial/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Fonction pour générer un mot de passe aléatoire
    private function generateRandomPassword(int $length = 12): string
    {
        // Génère un mot de passe aléatoire sécurisé
        return bin2hex(random_bytes($length / 2));
    }

    // Fonction pour envoyer l'e-mail avec le mot de passe
    private function sendPasswordEmail(MailerInterface $mailer, User $user, string $plainPassword): void
    {
        try {
            $email = (new Email())
                ->from('contact@cartemenu.fr')
                ->to($user->getEmail())
                ->subject('Bienvenue chez Sym Boost')
                ->html($this->renderView('commercial/emails/welcome.html.twig', [
                    'user' => $user,
                'password' => $plainPassword,
            ]));

            $mailer->send($email);
        } catch (\Exception $e) {
            // Log l'erreur ou prendre une autre action appropriée
            $this->addFlash('warning', 'Utilisateur créé, mais l\'envoi de l\'e-mail a échoué.');
        }
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if (in_array('ROLE_COMMERCIAL', $user->getRoles())) {
                return $this->redirectToRoute('app_user_commerciaux', [], Response::HTTP_SEE_OTHER);
            } else if (in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_user_revendeurs', [], Response::HTTP_SEE_OTHER);

        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
