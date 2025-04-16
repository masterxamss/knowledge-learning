<?php

namespace App\Controller\Admin;

use App\Entity\Chapters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class ChaptersCrudController extends AbstractCrudController
{
  public static function getEntityFqcn(): string
  {
    return Chapters::class;
  }


  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')
        ->hideOnForm(),
      TextField::new('title', 'Titre'),
      TextEditorField::new('content', 'Contenu'),
      AssociationField::new('lessonId', 'Leçon')
        ->setRequired(true)
        ->autocomplete(),
      UrlField::new('video', 'Lien video'),
      ImageField::new('image', 'Image')
        ->setUploadDir('public/images/icons')
        ->setBasePath('/images/icons')
        ->setRequired(false),
      DateField::new('created_at', 'Date de création')
        ->hideOnForm(),
      DateField::new('updated_at', 'Date de modification')
        ->hideOnForm(),
    ];
  }
}
