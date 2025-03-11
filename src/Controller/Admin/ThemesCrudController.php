<?php

namespace App\Controller\Admin;

use App\Entity\Themes;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class ThemesCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Themes::class;
  }


  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')
        ->hideOnForm(),
      TextField::new('name'),
      TextField::new('title'),
      TextEditorField::new('description'),
      // SlugField::new('slug')
      //   ->setTargetFieldName('name')
      //   ->hideOnForm(),
      ImgeField::new('image')
        ->setUploadDir('public/images/themes')
        ->setBasePath('/images/themes')
        ->setRequired(false),
    ];
  }
}
