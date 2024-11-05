<?php

namespace App\Controller\Admin;

use App\Entity\EmailSettings;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class EmailSettingsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return EmailSettings::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW) // Désactive le bouton "Ajouter"
            ->disable(Action::DELETE); // Désactive le bouton "Supprimer"
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
