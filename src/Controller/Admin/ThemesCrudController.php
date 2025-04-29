<?php

namespace App\Controller\Admin;

use App\Entity\Themes;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class ThemesCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Themes::class;
  }


  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setPaginatorPageSize(10);
  }

  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')
        ->hideOnForm(),
      TextField::new('name', 'Nom'),
      TextField::new('title'),
      TextEditorField::new('description', 'Description'),
      BooleanField::new('highlight', 'Mise en avant'),
      ImageField::new('image', 'Image')
        ->setUploadDir('public/images/themes')
        ->setBasePath('/images/themes')
        ->setRequired(false),
    ];
  }
}
