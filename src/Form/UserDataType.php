<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;

class UserDataType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      // First Name 🙋
      ->add('first_name', TextType::class, [
        'attr' => [
          'placeholder' => 'Prenom',
          'class' => 'form-control'
        ],
        'label' => false,
        'required' => false,
        'empty_data' => '',
        'constraints' => [
          new Assert\NotBlank(['message' => 'Ce champ ne peut pas être vide.']),
          new Assert\Length([
            'min' => 3,
            'minMessage' => 'Le prenom doit comporter au moins {{ limit }} caractères.',
            'max' => 250,
            'maxMessage' => 'Le prenom ne peut pas dépasser {{ limit }} caractères.',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'Le nom ne doit contenir que des lettres et des espaces.',
          ]),
        ],
      ])

      // Last Name 🙋
      ->add('last_name', TextType::class, [
        'attr' => [
          'placeholder' => 'Nom',
          'class' => 'form-control'
        ],
        'label' => false,
        'required' => false,
        'empty_data' => '',
        'constraints' => [
          new Assert\NotBlank(['message' => 'Ce champ ne peut pas être vide.']),
          new Assert\Length([
            'min' => 3,
            'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
            'max' => 250,
            'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'Le nom ne doit contenir que des lettres et des espaces.',
          ]),
        ],
      ])

      // Title 🧑 💻
      ->add('title', TextType::class, [
        'attr' => [
          'placeholder' => 'Titre',
          'class' => 'form-control'
        ],
        'label' => false,
        'required' => false,
        'constraints' => [
          new Assert\Length([
            'min' => 3,
            'minMessage' => 'Le nom doit comporter au moins {{ limit }} caractères.',
            'max' => 250,
            'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'Le nom ne doit contenir que des lettres et des espaces.',
          ]),
        ],
      ])

      // Email 📧
      ->add('email', EmailType::class, [
        'attr' => [
          'placeholder' => 'Email',
          'class' => 'form-control'
        ],
        'label' => false,
        'required' => false,
        'empty_data' => '',
        'constraints' => [
          new Assert\NotBlank(['message' => 'L\'email ne peut pas être vide.']),
          new Assert\Email([
            'message' => 'L\'email « {{ value }} » n\'est pas valide.',
            'mode' => 'strict',
          ]),
        ],
      ])

      // Description 📝
      ->add('description', TextareaType::class, [
        'attr' => [
          'placeholder' => 'Description',
          'class' => 'form-control',
          'rows' => 15,
          'cols' => 150,
          'style' => 'resize: none;'
        ],
        'label' => false,
        'required' => false,
        'constraints' => [
          new Assert\Length([
            'min' => 3,
            'minMessage' => 'La description doit comporter au moins {{ limit }} caractères.',
            'max' => 500,
            'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-Z0-9 ]+$/',
            'message' => 'La description ne doit contenir que des lettres, des chiffres et des espaces.',
          ]),
        ],
      ])

      // Submit 🚀
      ->add('submit', SubmitType::class, [
        'attr' => [
          'class' => 'btn'
        ],
        'label' => 'Enregistrer'
      ])
    ;

    $builder
      // Address 🏠
      ->add('address', TextType::class, [
        'label' => false,
        'compound' => true
      ])

      ->get('address')

      ->add('street', TextType::class, [
        'attr' => [
          'placeholder' => 'Rue',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => "/^[a-zA-ZÀ-ÿ0-9\s.,'’-]+$/",
            'message' => 'Saisir une adresse valide',
          ]),
        ],
      ])

      ->add('complement', TextType::class, [
        'attr' => [
          'placeholder' => 'Complément d\'adresse',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^[a-zA-Z0-9 ]+$/',
            'message' => 'Ce champ ne doit contenir que des lettres, des chiffres et des espaces.',
          ]),
        ],
      ])

      ->add('city', TextType::class, [
        'attr' => [
          'placeholder' => 'Ville',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => "/^[a-zA-ZÀ-ÿ'’\- ]+$/",
            'message' => 'Ce champ ne doit contenir que des lettres, des chiffres et des espaces.',
          ]),
        ],
      ])

      ->add('zipcode', TextType::class, [
        'attr' => [
          'placeholder' => 'Code postal',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => "/^\d{5}$/",
            'message' => 'Ce champ ne doit contenir que des chiffres.',
          ]),
        ],
      ])

      ->add('country', TextType::class, [
        'attr' => [
          'placeholder' => 'Pays',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => "/^[a-zA-ZÀ-ÿ\s'-]+$/",
            'message' => 'Champ non valide',
          ])
        ],
      ])

      ->add('state', TextType::class, [
        'attr' => [
          'placeholder' => 'Region',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^[a-zA-Z ]+$/',
            'message' => 'Ce champ ne doit contenir que des lettres et des espaces.',
          ])
        ],
      ])
    ;

    $builder
      // Links 🌐
      ->add('links', TextType::class, [
        'label' => false,
        'compound' => true
      ])

      ->get('links')

      ->add('website', TextType::class, [
        'attr' => [
          'placeholder' => 'Site web',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\.-]*)*\/?$/',
            'message' => 'Ce champ doit contenir une URL valide.',
          ]),
        ],
      ])

      ->add('twitter', TextType::class, [
        'attr' => [
          'placeholder' => 'Twitter',
          'class' => 'form-control'
        ],
        'required' => false,

        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\/[^\s]*)?$/',
            'message' => 'Ce champ doit contenir une URL valide.',
          ]),
        ],
      ])

      ->add('facebook', TextType::class, [
        'attr' => [
          'placeholder' => 'Facebook',
          'class' => 'form-control'
        ],
        'required' => false,

        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\/[^\s]*)?$/',
            'message' => 'Ce champ doit contenir une URL valide.',
          ]),
        ],
      ])

      ->add('linkedin', TextType::class, [
        'attr' => [
          'placeholder' => 'LinkedIn',
          'class' => 'form-control'
        ],
        'required' => false,

        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\/[^\s]*)?$/',
            'message' => 'Ce champ doit contenir une URL valide.',
          ]),
        ],
      ])

      ->add('youtube', TextType::class, [
        'attr' => [
          'placeholder' => 'YouTube',
          'class' => 'form-control'
        ],
        'required' => false,

        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\/[^\s]*)?$/',
            'message' => 'Ce champ doit contenir une URL valide.',
          ]),
        ],
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
