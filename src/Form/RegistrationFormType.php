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

/**
 * Class RegistrationFormType
 *
 * Form for user registration.
 * This form allows input of basic information such as email, name, password, etc.
 *
 */
class RegistrationFormType extends AbstractType
{
  /**
   * Configures the fields of the form.
   *
   * @param FormBuilderInterface $builder The form builder.
   * @param array $options The options configured for the form.
   *
   * @return void
   */
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      //  EMAIL 📧
      ->add('email', EmailType::class, [
        'attr' => [
          'placeholder' => 'user_form.email.placeholder',
          'class' => 'form-control'
        ],
        'required' => false
      ])
      // FIRST NAME 👤
      ->add('first_name', TextType::class, [
        'label' => false,
        'attr' => [
          'placeholder' => 'user_form.first_name.placeholder',
          'class' => 'form-control'
        ],
        'required' => false
      ])
      // LAST NAME 👤
      ->add('last_name', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.last_name.placeholder',
          'class' => 'form-control'
        ],
        'required' => false
      ])
      // PASSWORD 🔑
      ->add('password', PasswordType::class, [
        'attr' => [
          'placeholder' => 'register_form.password.placeholder',
          'class' => 'form-control',
          'autocomplete' => 'new-password'
        ],
        'required' => false
      ])
      // PASSWORD CONFIRM 🔑
      ->add('confirmPassword', PasswordType::class, [
        'attr' => [
          'placeholder' => 'register_form.password_confirmation.placeholder',
          'class' => 'form-control'
        ],
        'mapped' => false,
        'constraints' => [
          new NotBlank([
            'message' => 'constraints.not_blank',
          ]),
          new PasswordMatch(),
        ],
        'required' => false
      ])
      // AGREE TERMS 📝
      ->add('agreeTerms', CheckboxType::class, [
        'mapped' => false,
        'label' => 'register_form.agreement.label',
        'constraints' => [
          new IsTrue([
            'message' => 'constraints.agreement',
          ]),
        ],
        'required' => false
      ])
      // SUBMIT
      ->add('submit', SubmitType::class, [
        'attr' => [
          'class' => 'btn'
        ],
        'label' => 'register_form.submit.label'
      ])
    ;
  }

  /**
   * Configures the options for the form.
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
