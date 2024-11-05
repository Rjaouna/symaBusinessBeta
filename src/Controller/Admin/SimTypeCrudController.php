<?php

namespace App\Controller\Admin;

use App\Entity\SimType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SimTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SimType::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Types de Sim')
            ->setEntityLabelInSingular('Type de Sim')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste de types Sim')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le type')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un nouveau type');;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom'),
            TextField::new('code'),
            TextField::new('prix'),
        ];
    }
}
