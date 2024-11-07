<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Validator\Constraints\Regex;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Clients')
            ->setEntityLabelInSingular('Client')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des Clients')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le Client')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un Nouveau Client');;
    }



    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('codeClient', 'Code Client')
            ->setRequired(false) // Le champ n'est pas obligatoire
            ->hideOnIndex() // Masquer le champ sur la page d'index
            ->setFormTypeOptions([
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z][0-9]{8}$/',
                        'message' => 'Le code client doit commencer par une lettre suivie de 8 chiffres. Ex : C41027544',
                    ])
                ]
            ]),
            EmailField::new('email', 'Email')->setRequired(true),
            TextField::new('password', 'Mot de passe')->hideOnDetail()->hideOnIndex() // Utilisez TextField pour afficher le mot de passe
            ->setRequired(true) // Rendre le champ requis
            ->setHelp('Veuillez entrer un mot de passe sécurisé.') // Message d'aide
            ->setFormTypeOption('attr', ['type' => 'password']),
            TextField::new('nomResponsable', 'Nom du Responsable')->setRequired(true),

            BooleanField::new('isVerified', 'Est vérifié')->setRequired(false)->hideOnForm(),
            ChoiceField::new('roles', 'Rôles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN',
                'Super Administrateur' => 'ROLE_SUPERADMIN',
            ])
                ->setRequired(true)
                ->allowMultipleChoices(),
            TextField::new('adresse', 'Adresse')->setRequired(false)->hideOnIndex(),
            TextField::new('telephoneMobile', 'Téléphone Mobile')->setRequired(false),
            TextField::new('nomSociete', 'Nom de la Société')->setRequired(false),
            TextField::new('numeroSiret', 'Numéro SIRET')->setRequired(false)->hideOnIndex(),
            TextField::new('facade', 'Facade')->setRequired(false)->hideOnIndex()->hideOnForm(),
            TextField::new('pays', 'Pays')->setRequired(false)->hideOnIndex()->hideOnForm(),
            TextField::new('codePostal', 'Code Postal')->setRequired(false)->hideOnIndex()->hideOnForm(),
            TextField::new('ville', 'Ville')->setRequired(false)->hideOnIndex()->hideOnForm(),
            TextField::new('iban', 'IBAN')->setRequired(false)->hideOnIndex()->hideOnForm(),
            TextField::new('bic', 'BIC')->setRequired(false)->hideOnIndex()->hideOnForm(),
            // Champs liés aux quotas, commandes, et bonus (adaptation selon votre logique)
            AssociationField::new('quotas', 'Attribuer un Quota')->setRequired(false),
            TextField::new('totalBonus', 'Total Bonus')->setRequired(false)->setDisabled(true)->hideOnForm(),
            IntegerField::new('sim5Usage', 'Utilisation SIM 5')->setRequired(false)->hideOnIndex()->setDisabled(true)->hideOnForm(),
            IntegerField::new('sim10Usage', 'Utilisation SIM 10')->setRequired(false)->hideOnIndex()->setDisabled(true)->hideOnForm(),
            IntegerField::new('sim15Usage', 'Utilisation SIM 15')->setRequired(false)->hideOnIndex()->setDisabled(true)->hideOnForm(),
            IntegerField::new('sim20Usage', 'Utilisation SIM 20')->setRequired(false)->hideOnIndex()->setDisabled(true)->hideOnForm(),
        ];
    }
}
