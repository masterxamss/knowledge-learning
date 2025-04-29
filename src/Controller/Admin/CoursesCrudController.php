<?php

namespace App\Controller\Admin;

use App\Entity\Courses;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class CoursesCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Courses::class;
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
      TextField::new('title', 'Titre'),
      TextEditorField::new('description', 'Description'),
      ImageField::new('image')
        ->setUploadDir('public/images/courses')
        ->setBasePath('/images/courses')
        ->setRequired(false),
      AssociationField::new('theme', 'Théme')
        ->setRequired(true)
        ->autocomplete(),
      MoneyField::new('price', 'Prix')
        ->setCurrency('EUR')
        ->setStoredAsCents(false),
      AssociationField::new('badge', 'Badge')
        ->setRequired(false)
        ->autocomplete(),
      BooleanField::new('highlight', 'Mise en avant'),
      // SlugField::new('slug')
      //   ->setTargetFieldName('title')
      //   ->hideOnForm(),
    ];
  }
}
