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

/**
 * Class CoursesCrudController
 *
 * This controller is part of the EasyAdminBundle and manages the CRUD interface
 * for the Courses entity within the admin dashboard.
 *
 * It defines how course records are listed, created, and edited through the admin UI.
 * Fields include title, description, image, theme, price, badge, and a highlight flag.
 *
 * @package App\Controller\Admin
 */
class CoursesCrudController extends AbstractCrudController
{
  /**
   * Returns the fully qualified class name of the managed entity.
   *
   * @return string
   */
  public static function getEntityFqcn(): string
  {
    return Courses::class;
  }

  /**
   * Configures the CRUD interface behavior.
   *
   * Sets the pagination to display 10 items per page.
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
   * Configures the fields to be displayed and edited in the CRUD interface.
   *
   * Includes fields for title, description, image upload, theme association,
   * price in euros, optional badge, and a boolean highlight flag. Some fields
   * are hidden from the form (e.g., ID).
   *
   * @param string $pageName
   * @return iterable
   */
  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')->hideOnForm(),
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
      //     ->setTargetFieldName('title')
      //     ->hideOnForm(),
    ];
  }
}
