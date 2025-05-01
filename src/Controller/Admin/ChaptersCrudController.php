<?php

namespace App\Controller\Admin;

use App\Entity\Chapters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

/**
 * Class ChaptersCrudController
 *
 * This controller is part of the EasyAdminBundle and manages the CRUD interface
 * for the Chapters entity in the admin dashboard.
 *
 * It defines how chapter records are displayed and edited, including fields such
 * as title, content, associated lesson, video link, and image. The controller also
 * manages timestamps and pagination settings.
 *
 * @package App\Controller\Admin
 */
class ChaptersCrudController extends AbstractCrudController
{
  /**
   * Returns the fully qualified class name of the managed entity.
   *
   * @return string
   */
  public static function getEntityFqcn(): string
  {
    return Chapters::class;
  }

  /**
   * Configures the CRUD behavior for this controller.
   *
   * Sets the pagination page size to 10 entries per page.
   *
   * @param Crud $crud
   * @return Crud
   */
  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setPaginatorPageSize(10);
  }

  /**
   * Configures the fields to be displayed or editable in the CRUD interface.
   *
   * Includes fields for title, content, associated lesson, video link, image,
   * and timestamps. Some fields are hidden on forms.
   *
   * @param string $pageName
   * @return iterable
   */
  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')->hideOnForm(),
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
      DateField::new('created_at', 'Date de création')->hideOnForm(),
      DateField::new('updated_at', 'Date de modification')->hideOnForm(),
    ];
  }
}
