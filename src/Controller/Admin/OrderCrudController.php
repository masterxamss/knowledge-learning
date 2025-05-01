<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

/**
 * Class OrderCrudController
 *
 * This controller manages the CRUD operations for the "Order" entity in the administration panel.
 * It allows the creation, editing, and deletion of orders through the EasyAdmin interface.
 * The controller is responsible for configuring the display settings of the "Order" entity within the admin dashboard.
 *
 * @package App\Controller\Admin
 */
class OrderCrudController extends AbstractCrudController
{
  /**
   * Returns the fully qualified class name of the entity this controller handles.
   *
   * @return string The entity class name
   */
  public static function getEntityFqcn(): string
  {
    return Order::class;
  }

  /**
   * Configures the EasyAdmin CRUD options for the "Order" entity, such as entity labels,
   * search fields, and pagination settings.
   *
   * @param Crud $crud The CRUD configuration object
   * @return Crud The configured CRUD object
   */
  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      // Sets the singular and plural labels for the entity in the admin interface
      ->setEntityLabelInSingular('Order')
      ->setEntityLabelInPlural('Orders')

      // Defines the fields to be used for search functionality
      ->setSearchFields(['paymentStatus', 'id'])

      // Sets the number of pages to display in the paginator
      ->setPaginatorRangeSize(10);
  }
}
