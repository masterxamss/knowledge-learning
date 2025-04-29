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

class UserCrudController extends AbstractCrudController
{
  private UserPasswordHasherInterface $passwordHasher;

  // Injeção de dependência do hasher
  public function __construct(UserPasswordHasherInterface $passwordHasher)
  {
    $this->passwordHasher = $passwordHasher;
  }

  public static function getEntityFqcn(): string
  {
    return User::class;
  }

  public function configureCrud(Crud $crud): Crud
  {
    return $crud
      ->setPaginatorPageSize(10);
  }

  public function configureFields(string $pageName): iterable
  {
    $fields = [
      IdField::new('id')
        ->hideOnForm(),
      TextField::new('firstName', 'Prénom')
        ->setRequired(true),
      TextField::new('lastName', 'Nom')
        ->setRequired(true),
      TextField::new('password', 'Mot de passe')
        ->setFormType(PasswordType::class)
        ->setRequired(true)
        ->onlyWhenCreating(),
      EmailField::new('email', 'Email')
        ->setRequired(true),
      BooleanField::new('active', 'Actif')
        ->hideOnForm(),
      BooleanField::new('isVerified', 'Email verifie')
        ->hideOnForm(),
      ChoiceField::new('roles', 'Roles')
        ->setChoices([
          'Admin' => 'ROLE_ADMIN',
          'User' => 'ROLE_USER',
        ])
        ->renderExpanded()
        ->allowMultipleChoices(),
      //->onlyOnForms(),
      DateField::new('createdAt', 'Date de creation')
        ->hideOnForm(),
      DateField::new('updatedAt', 'Date de modification')
        ->hideOnForm(),
    ];

    // Durante a edição, desabilita o campo para impedir modificações
    if ($pageName === 'edit') {
      $fields[3] = TextField::new('password', 'Mot de passe')
        ->setFormType(PasswordType::class)
        ->setRequired(false)
        ->setDisabled(true);  // Torna o campo desabilitado na edição
    }

    return $fields;
  }

  public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
  {
    // Se o usuário estiver sendo criado, faz o hash da senha
    if ($entityInstance instanceof User) {
      if ($entityInstance->getPassword()) {
        // Faz o hash da senha
        $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
        $entityInstance->setPassword($hashedPassword);
      }
    }

    // Chama o método persist do pai para salvar o usuário
    parent::persistEntity($entityManager, $entityInstance);
  }
}
