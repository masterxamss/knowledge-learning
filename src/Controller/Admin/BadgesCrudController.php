<?php

namespace App\Controller\Admin;

use App\Entity\Badges;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

/**
 * Class BadgesCrudController
 *
 * This controller is responsible for managing the CRUD interface of the Badges entity
 * in the EasyAdmin dashboard. It defines the fields, labels, and pagination settings
 * used in the admin UI.
 *
 * @package App\Controller\Admin
 */
class BadgesCrudController extends AbstractCrudController
{
  /**
   * Returns the fully qualified class name (FQCN) of the entity managed by this controller.
   *
   * @return string The FQCN of the Badges entity.
   */
  public static function getEntityFqcn(): string
  {
    return Badges::class;
  }

  /**
   * Configures general settings for the CRUD interface.
   *
   * For example, it sets the default page size for pagination.
   *
   * @param Crud $crud The CRUD configuration object.
   *
   * @return Crud The modified CRUD configuration.
   */
  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setPaginatorPageSize(10);
  }

  /**
   * Configures the fields displayed on various CRUD pages (list, detail, form).
   *
   * @param string $pageName The current page name (e.g., 'index', 'edit', 'new').
   *
   * @return iterable An iterable list of field definitions.
   */
  public function configureFields(string $pageName): iterable
  {
    return [
      IdField::new('id')
        ->hideOnForm(),

      TextField::new('name', 'Nom')
        ->setRequired(true),

      TextField::new('cssClass', 'Classe CSS'),

      DateField::new('createdAt', 'Date de création')
        ->hideOnForm(),

      DateField::new('updatedAt', 'Date de mise à jour')
        ->hideOnForm(),
    ];
  }
}
