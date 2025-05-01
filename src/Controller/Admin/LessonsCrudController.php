<?php

namespace App\Controller\Admin;

use App\Entity\Lessons;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

/**
 * Class LessonsCrudController
 *
 * This controller manages the CRUD operations for the "Lessons" entity in the administration panel.
 * It allows the creation, editing, and deletion of lessons through the EasyAdmin interface.
 *
 * @package App\Controller\Admin
 */
class LessonsCrudController extends AbstractCrudController
{
  /**
   * Returns the fully qualified class name of the entity this controller handles.
   *
   * @return string The entity class name
   */
  public static function getEntityFqcn(): string
  {
    return Lessons::class;
  }

  /**
   * Configures the EasyAdmin CRUD options, such as pagination settings.
   *
   * @param Crud $crud The CRUD configuration object
   * @return Crud The configured CRUD object
   */
  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setPaginatorPageSize(10);  // Sets the number of records per page in the list view
  }

  /**
   * Configures the fields to be displayed in the CRUD views (form, list, edit, etc.).
   *
   * @param string $pageName The current page name (e.g., 'index', 'edit')
   * @return iterable A list of fields to be displayed in the CRUD views
   */
  public function configureFields(string $pageName): iterable
  {
    return [
      // Hides the 'id' field on the form view but includes it in other views like index and show
      IdField::new('id')
        ->hideOnForm(),

      // Displays the 'title' field as a text input
      TextField::new('title'),

      // Displays the 'description' field as a rich text editor
      TextEditorField::new('description'),

      // Displays the 'price' field as a monetary field in EUR with cents stored as integers
      MoneyField::new('price', 'Price')
        ->setCurrency('EUR')
        ->setStoredAsCents(false),

      // Displays the 'image' field as an image upload with a specific upload directory and base path
      ImageField::new('image')
        ->setUploadDir('public/images/lessons')
        ->setBasePath('/images/lessons')
        ->setRequired(false),

      // Displays the 'course' field as an association (related to a 'Course' entity)
      AssociationField::new('course', 'Course')
        ->setRequired(true)
        ->autocomplete(),

      // Displays the 'badge' field as an optional association (related to a 'Badge' entity)
      AssociationField::new('badge', 'Badge')
        ->setRequired(false)
        ->autocomplete(),

      // Displays the 'highlight' field as a boolean field
      BooleanField::new('highlight', 'Highlight')
    ];
  }
}
