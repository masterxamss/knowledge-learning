<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class UserCrudController
 *
 * This controller manages the CRUD operations for the "User" entity in the administration panel.
 * It provides functionalities to create, read, update, and delete users through the EasyAdmin interface.
 * The controller also handles password hashing when creating a user, ensuring that passwords are securely stored.
 *
 * @package App\Controller\Admin
 */
class UserCrudController extends AbstractCrudController
{
  /**
   * @var UserPasswordHasherInterface The service for hashing user passwords
   */
  private UserPasswordHasherInterface $passwordHasher;

  /**
   * UserCrudController constructor.
   * 
   * @param UserPasswordHasherInterface $passwordHasher The service used to hash passwords
   */
  public function __construct(UserPasswordHasherInterface $passwordHasher)
  {
    $this->passwordHasher = $passwordHasher;
  }

  /**
   * Returns the fully qualified class name of the entity this controller handles.
   *
   * @return string The entity class name
   */
  public static function getEntityFqcn(): string
  {
    return User::class;
  }

  /**
   * Configures the EasyAdmin CRUD settings for the "User" entity, including pagination settings.
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
   * Configures the fields to be displayed for the "User" entity in the EasyAdmin interface.
   * This includes fields for creating and editing users, as well as field visibility settings.
   *
   * @param string $pageName The name of the page (e.g., 'new', 'edit', 'detail', 'index')
   * @return iterable The list of fields to be displayed
   */
  public function configureFields(string $pageName): iterable
  {
    $fields = [
      // ID field, hidden on form views
      IdField::new('id')
        ->hideOnForm(),

      // First name field
      TextField::new('firstName', 'Prénom')
        ->setRequired(true),

      // Last name field
      TextField::new('lastName', 'Nom')
        ->setRequired(true),

      // Password field, only visible when creating a user
      TextField::new('password', 'Mot de passe')
        ->setFormType(PasswordType::class)
        ->setRequired(true)
        ->onlyWhenCreating(),

      // Email field
      EmailField::new('email', 'Email')
        ->setRequired(true),

      // Active field, hidden on form views
      BooleanField::new('active', 'Actif')
        ->hideOnForm(),

      // Verified email field, hidden on form views
      BooleanField::new('isVerified', 'Email verifie')
        ->hideOnForm(),

      // Roles field, allowing the selection of multiple roles
      ChoiceField::new('roles', 'Roles')
        ->setChoices([
          'Admin' => 'ROLE_ADMIN',
          'User' => 'ROLE_USER',
        ])
        ->renderExpanded()
        ->allowMultipleChoices(),

      // Created at field, hidden on form views
      DateField::new('createdAt', 'Date de creation')
        ->hideOnForm(),

      // Updated at field, hidden on form views
      DateField::new('updatedAt', 'Date de modification')
        ->hideOnForm(),
    ];

    // During editing, disables the password field to prevent modification
    if ($pageName === 'edit') {
      $fields[3] = TextField::new('password', 'Mot de passe')
        ->setFormType(PasswordType::class)
        ->setRequired(false)
        ->setDisabled(true);  // Disables the field on the edit page
    }

    return $fields;
  }

  /**
   * Persists the entity into the database. This method hashes the password before saving the user entity.
   *
   * @param EntityManagerInterface $entityManager The entity manager to persist the entity
   * @param object $entityInstance The entity instance to be persisted
   */
  public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
  {
    // If the entity instance is a User, hash the password before persisting
    if ($entityInstance instanceof User) {
      if ($entityInstance->getPassword()) {
        // Hashes the user's password
        $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
        $entityInstance->setPassword($hashedPassword);
      }
    }

    // Calls the parent method to persist the user entity
    parent::persistEntity($entityManager, $entityInstance);
  }
}
