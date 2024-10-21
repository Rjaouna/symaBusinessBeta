<?php

namespace App\Controller;

use App\Entity\Banner;
use App\Form\BannerType;
use App\Repository\BannerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
#[Route('/banner')]
final class BannerController extends AbstractController
{
    #[Route(name: 'app_banner_index', methods: ['GET'])]
    public function index(BannerRepository $bannerRepository): Response
    {
        // Récupérer toutes les bannières
        $banners = $bannerRepository->findAll();

        // Retourner la vue avec les bannières et le nombre de bannières
        return $this->render('banner/index.html.twig', [
            'banners' => $banners,
            'bannerCount' => count($banners), // Passer le nombre de bannières à Twig
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/banner/new', name: 'app_banner_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $banner = new Banner();
        $form = $this->createForm(BannerType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de fichier
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Renommer le fichier avec un nom unique
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Déplacer le fichier dans le répertoire configuré
                try {
                    $imageFile->move(
                        $this->getParameter('banners_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer les erreurs liées au téléchargement
                    $this->addFlash('danger', 'Erreur lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_banner_new');
                }

                // Stocker le nom de fichier dans l'entité `Banner`
                $banner->setImage($newFilename);
            }

            // Enregistrer les données en base
            $entityManager->persist($banner);
            $entityManager->flush();

            return $this->redirectToRoute('app_banner_index');
        }

        return $this->render('banner/new.html.twig', [
            'banner' => $banner,
            'form' => $form->createView(),
        ]);
    }



    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_banner_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Banner $banner, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BannerType::class, $banner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_banner_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('banner/edit.html.twig', [
            'banner' => $banner,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_banner_delete', methods: ['POST'])]
    public function delete(Request $request, Banner $banner, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $banner->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($banner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_banner_index', [], Response::HTTP_SEE_OTHER);
    }
}
