<?php

namespace App\Controller\Admin;

use App\Entity\Lessons;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class LessonsCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Lessons::class;
  }


  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')
        ->hideOnForm(),
      TextField::new('title'),
      TextEditorField::new('description'),
      MoneyField::new('price', 'Prix')
        ->setCurrency('EUR')
        ->setStoredAsCents(false),
      UrlField::new('video'),
      ImageField::new('icon_1')
        ->setUploadDir('public/images/icons')
        ->setBasePath('/images/icons')
        ->setRequired(false),
      ImageField::new('icon_2')
        ->setUploadDir('public/images/icons')
        ->setBasePath('/images/icons')
        ->setRequired(false),
      ImageField::new('image')
        ->setUploadDir('public/images/lessons')
        ->setBasePath('/images/lessons')
        ->setRequired(false),
      AssociationField::new('course', 'Cours')
        ->setRequired(true)
        ->autocomplete(),
      AssociationField::new('badge', 'Badge')
        ->setRequired(false)
        ->autocomplete(),
      BooleanField::new('highlight', 'Mise en avant')
    ];
  }
}
