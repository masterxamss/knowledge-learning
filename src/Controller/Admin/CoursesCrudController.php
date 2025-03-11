<?php

namespace App\Controller\Admin;

use App\Entity\Courses;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;

class CoursesCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Courses::class;
  }


  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')
        ->hideOnForm(),
      TextField::new('title'),
      TextEditorField::new('description'),
      ImageField::new('image')
        ->setUploadDir('public/images/courses')
        ->setBasePath('/images/courses')
        ->setRequired(false),
      AssociationField::new('theme', 'Théme')
        ->setRequired(true)
        ->autocomplete(),
      MoneyField::new('price', 'Prix')->setCurrency('EUR'),
      // SlugField::new('slug')
      //   ->setTargetFieldName('title')
      //   ->hideOnForm(),
    ];
  }
}
