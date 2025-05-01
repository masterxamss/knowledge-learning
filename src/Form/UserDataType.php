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

/**
 * Class UserDataType
 *
 * Form for managing user data including personal information, address, and links.
 * This form allows users to edit their first name, last name, title, email, description,
 * and additional information such as address and social media links.
 *
 */
class UserDataType extends AbstractType
{
  /**
   * Builds the form for managing user data.
   *
   * @param FormBuilderInterface $builder The form builder.
   * @param array $options The options configured for the form.
   *
   * @return void
   */
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      // First Name 🙋
      ->add('first_name', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.first_name.placeholder',
          'class' => 'form-control'
        ],
        'label' => false,
        'required' => false,
        'empty_data' => '',
        'constraints' => [
          new Assert\NotBlank([
            'message' => 'constraints.not_blank',
          ]),
          new Assert\Length([
            'min' => 3,
            'minMessage' => 'constraints.min_length',
            'max' => 250,
            'maxMessage' => 'constraints.max_length',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      // Last Name 🙋
      ->add('last_name', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.last_name.placeholder',
          'class' => 'form-control'
        ],
        'label' => false,
        'required' => false,
        'empty_data' => '',
        'constraints' => [
          new Assert\NotBlank(['message' => 'constarints.not_blank']),
          new Assert\Length([
            'min' => 3,
            'minMessage' => 'constraints.min_length',
            'max' => 250,
            'maxMessage' => 'constraints.max_length',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      // Title 🧑 💻
      ->add('title', TextType::class, [
        'label' => 'user_form.title.label',
        'attr' => [
          'placeholder' => 'user_form.title.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'constraints' => [
          new Assert\Length([
            'min' => 3,
            'minMessage' => 'constraints.min_length',
            'max' => 250,
            'maxMessage' => 'constraints.max_length',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      // Email 📧
      ->add('email', EmailType::class, [
        'attr' => [
          'placeholder' => 'user_form.email.placeholder',
          'readonly' => true,
          'class' => 'form-control',
        ],
        'label' => false,
        'required' => false,
        'empty_data' => '',
        'constraints' => [
          new Assert\NotBlank(['message' => 'constraints.not_blank']),
          new Assert\Email([
            'message' => 'constraints.email',
            'mode' => 'strict',
          ]),
        ],
      ])

      // Description 📝
      ->add('description', TextareaType::class, [
        'attr' => [
          'placeholder' => 'user_form.description.placeholder',
          'class' => 'form-control',
          'rows' => 15,
          'cols' => 150,
          'style' => 'resize: none;'
        ],
        'label' => 'user_form.description.label',
        'required' => false,
        'constraints' => [
          new Assert\Length([
            'min' => 10,
            'minMessage' => 'constraints.min_length',
            'max' => 500,
            'maxMessage' => 'constraints.max_length',
          ]),
          new Assert\Regex([
            'pattern' => "/^[a-zA-ZÀ-ÿ0-9.,!?()\-'\"ºª\s]+$/",
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      // Submit 🚀
      ->add('submit', SubmitType::class, [
        'attr' => [
          'class' => 'btn'
        ],
        'label' => 'user_form.submit.label'
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
          'placeholder' => 'user_form.street.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => "/^[a-zA-ZÀ-ÿ0-9\s.,'’-]+$/",
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      ->add('complement', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.complement.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÿ0-9\s.,ºª#-]{1,100}$/',
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      ->add('city', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.city.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => "/^[a-zA-ZÀ-ÿ'’\- ]+$/",
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      ->add('zipcode', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.zip_code.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => "/^\d{5}$/",
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      ->add('country', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.country.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => "/^[a-zA-ZÀ-ÿ\s'-]+$/",
            'message' => 'constraints.regex',
          ])
        ],
      ])

      ->add('state', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.state.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^[a-zA-Z ]+$/',
            'message' => 'constraints.regex',
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
          'placeholder' => 'user_form.website.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w\.-]*)*\/?$/',
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      ->add('twitter', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.twitter.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => 'user_form.twitter.label',
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\/[^\s]*)?$/',
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      ->add('facebook', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.facebook.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => 'user_form.facebook.label',
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\/[^\s]*)?$/',
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      ->add('linkedin', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.linkedin.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => 'user_form.linkedin.label',
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\/[^\s]*)?$/',
            'message' => 'constraints.regex',
          ]),
        ],
      ])

      ->add('youtube', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.youtube.placeholder',
          'class' => 'form-control'
        ],
        'required' => false,
        'label' => 'user_form.youtube.label',
        'constraints' => [
          new Assert\Regex([
            'pattern' => '/^(https?:\/\/)?(www\.)?[a-zA-Z0-9-]+(\.[a-zA-Z]{2,})(\/[^\s]*)?$/',
            'message' => 'constraints.regex',
          ]),
        ],
      ])
    ;
  }

  /**
   * Configures options for the UserDataType form.
   *
   * @param OptionsResolver $resolver The options resolver.
   *
   * @return void
   */
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
      'translation_domain' => 'forms',
    ]);
  }
}
