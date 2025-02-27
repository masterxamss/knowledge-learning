<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Constraints\PasswordMatch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      //  EMAIL 📧
      ->add('email', EmailType::class, [
        'attr' => [
          'placeholder' => 'Email',
          'class' => 'form-control'
        ],
        'constraints' => [
          new Assert\NotBlank(['message' => 'L\'email ne peut pas être vide.']),
          new Assert\Email([
            'message' => 'L\'email « {{ value }} » n\'est pas valide.',
            'mode' => 'strict',
          ]),
        ],
        'required' => false
      ])
      // FIRST NAME 👤
      ->add('first_name', TextType::class, [
        'label' => false,
        'attr' => [
          'placeholder' => 'form.user.first_name',
          'class' => 'form-control'
        ],
        'constraints' => [
          new Assert\NotBlank(['message' => 'Ce champ ne peut pas être vide.']),
          new Assert\Length([
            'min' => 2,
            'minMessage' => 'Le prenom doit comporter au moins {{ limit }} caractères.',
            'max' => 250,
            'maxMessage' => 'Le prenom ne peut pas dépasser {{ limit }} caractères.',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'Le prenom ne doit contenir que des lettres et des espaces.',
          ]),
        ],
        'required' => false
      ])
      // LAST NAME 👤
      ->add('last_name', TextType::class, [
        'attr' => [
          'placeholder' => 'Nom',
          'class' => 'form-control'
        ],
        'constraints' => [
          new Assert\NotBlank(['message' => 'Ce champ ne peut pas être vide.']),
          new Assert\Length([
            'min' => 2,
            'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
            'max' => 250,
            'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'Le nom ne doit contenir que des lettres et des espaces.',
          ]),
        ],
        'required' => false
      ])
      // PASSWORD 🔑
      ->add('password', PasswordType::class, [
        'attr' => [
          'placeholder' => 'Mot de passe',
          'class' => 'form-control',
          'autocomplete' => 'new-password'
        ],
        'constraints' => [
          new NotBlank([
            'message' => 'Ce champ est obligatoire',
          ]),
          new Assert\Length([
            'min' => 8,
            'minMessage' => 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
            'max' => 250,
            'maxMessage' => 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.',
          ]),
          new Assert\Regex([
            'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
            'message' => 'Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractère spécial.',
          ]),
        ],
        'required' => false
      ])
      // PASSWORD CONFIRM 🔑
      ->add('confirmPassword', PasswordType::class, [
        'attr' => [
          'placeholder' => 'Confirmer le mot de passe',
          'class' => 'form-control'
        ],
        'mapped' => false,
        'constraints' => [
          new NotBlank([
            'message' => 'Ce champ est obligatoire',
          ]),
          new PasswordMatch(),
        ],
        'required' => false
      ])
      // AGREE TERMS 📝
      ->add('agreeTerms', CheckboxType::class, [
        'mapped' => false,
        'label' => 'J\'accepte les conditions générales',
        'constraints' => [
          new IsTrue([
            'message' => 'Vous devez accepter nos conditions générales',
          ]),
        ],
        'required' => false
      ])
      // SUBMIT
      ->add('submit', SubmitType::class, [
        'attr' => [
          'class' => 'btn'
        ],
        'label' => 'S\'inscrire'
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
