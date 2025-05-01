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

/**
 * Class ThemesCrudController
 *
 * This controller is responsible for managing the CRUD operations of the "Themes" entity in the administration panel.
 * It provides functionalities to create, read, update, and delete themes via the EasyAdmin interface.
 * The controller configures the fields displayed in the admin panel for the "Themes" entity.
 *
 * @package App\Controller\Admin
 */
class ThemesCrudController extends AbstractCrudController
{
  /**
   * Returns the fully qualified class name of the entity this controller handles.
   *
   * @return string The entity class name
   */
  public static function getEntityFqcn(): string
  {
    return Themes::class;
  }

  /**
   * Configures the EasyAdmin CRUD settings for the "Themes" entity, including pagination settings.
   *
   * @param Crud $crud The CRUD configuration object
   * @return Crud The configured CRUD object
   */
  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      // Sets the number of items per page in the paginator
      ->setPaginatorPageSize(10);
  }

  /**
   * Configures the fields to be displayed for the "Themes" entity in the EasyAdmin interface.
   * This includes the fields for creating and editing themes as well as their visibility.
   *
   * @param string $pageName The name of the page (e.g., 'new', 'edit', 'detail', 'index')
   * @return iterable The list of fields to be displayed
   */
  public function configureFields(string $pageName): iterable
  {
    return [
      // ID field, hidden on form views
      IdField::new('id')
        ->hideOnForm(),

      // Name field, displayed with the label 'Nom'
      TextField::new('name', 'Nom'),

      // Title field
      TextField::new('title'),

      // Description field, displayed as a rich text editor
      TextEditorField::new('description', 'Description'),

      // Highlight field, displayed as a boolean checkbox
      BooleanField::new('highlight', 'Mise en avant'),

      // Image field, allowing the upload of images with specified directory and path
      ImageField::new('image', 'Image')
        ->setUploadDir('public/images/themes')
        ->setBasePath('/images/themes')
        ->setRequired(false),
    ];
  }
}
