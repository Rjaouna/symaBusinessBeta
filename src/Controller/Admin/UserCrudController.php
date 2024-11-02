<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

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
            TextField::new('codeClient', 'Code Client')->setRequired(false)->hideOnIndex(),
            EmailField::new('email', 'Email')->setRequired(true),
            ChoiceField::new('roles', 'Rôles')
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Super Administrateur' => 'ROLE_SUPERADMIN',
                ])
                ->setRequired(true)
                ->allowMultipleChoices(),
            BooleanField::new('isVerified', 'Est vérifié')->setRequired(false),
            TextField::new('nomResponsable', 'Nom du Responsable')->setRequired(true),
            TextField::new('telephoneFixe', 'Téléphone Fixe')->setRequired(false),
            TextField::new('telephoneMobile', 'Téléphone Mobile')->setRequired(false),
            TextField::new('nomSociete', 'Nom de la Société')->setRequired(false),
            TextField::new('formeJuridique', 'Forme Juridique')->setRequired(false)->hideOnIndex(),
            TextField::new('numeroRegistreCommerce', 'Numéro Registre de Commerce')->setRequired(false)->hideOnIndex(),
            TextField::new('numeroSiret', 'Numéro SIRET')->setRequired(false)->hideOnIndex(),
            TextField::new('numeroRCS', 'Numéro RCS')->setRequired(false)->hideOnIndex(),
            TextField::new('codeAPE', 'Code APE')->setRequired(false)->hideOnIndex(),
            TextField::new('facade', 'Facade')->setRequired(false)->hideOnIndex(),
            TextField::new('kbis', 'KBIS')->setRequired(false)->hideOnIndex(),
            TextField::new('adresse', 'Adresse')->setRequired(false)->hideOnIndex(),
            TextField::new('pays', 'Pays')->setRequired(false)->hideOnIndex(),
            TextField::new('codePostal', 'Code Postal')->setRequired(false)->hideOnIndex(),
            TextField::new('ville', 'Ville')->setRequired(false)->hideOnIndex(),
            TextField::new('iban', 'IBAN')->setRequired(false)->hideOnIndex(),
            TextField::new('bic', 'BIC')->setRequired(false)->hideOnIndex(),
            // Champs liés aux quotas, commandes, et bonus (adaptation selon votre logique)
            AssociationField::new('quotas', 'Quotas')->setRequired(false),
            TextField::new('totalBonus', 'Total Bonus')->setRequired(false)->setDisabled(true),
            IntegerField::new('sim5Usage', 'Utilisation SIM 5')->setRequired(false)->hideOnIndex()->setDisabled(true),
            IntegerField::new('sim10Usage', 'Utilisation SIM 10')->setRequired(false)->hideOnIndex()->setDisabled(true),
            IntegerField::new('sim15Usage', 'Utilisation SIM 15')->setRequired(false)->hideOnIndex()->setDisabled(true),
            IntegerField::new('sim20Usage', 'Utilisation SIM 20')->setRequired(false)->hideOnIndex()->setDisabled(true),
        ];
    }
}
