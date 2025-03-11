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
          'placeholder' => 'user_form.email.placeholder',
          'class' => 'form-control'
        ],
        'constraints' => [
          new Assert\NotBlank(['message' => 'constraints.not_blank']),
          new Assert\Email([
            'message' => 'constraints.email',
            'mode' => 'strict',
          ]),
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
        'constraints' => [
          new Assert\NotBlank(['message' => 'constraints.not_blank']),
          new Assert\Length([
            'min' => 2,
            'minMessage' => 'constraints.min_length',
            'max' => 250,
            'maxMessage' => 'constraints.max_length',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'constraints.regex',
          ]),
        ],
        'required' => false
      ])
      // LAST NAME 👤
      ->add('last_name', TextType::class, [
        'attr' => [
          'placeholder' => 'user_form.last_name.placeholder',
          'class' => 'form-control'
        ],
        'constraints' => [
          new Assert\NotBlank(['message' => 'constraints.not_blank']),
          new Assert\Length([
            'min' => 2,
            'minMessage' => 'constraints.min_length',
            'max' => 250,
            'maxMessage' => 'constraints.max_length',
          ]),
          new Assert\Regex([
            'pattern' => '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/',
            'message' => 'constraints.regex',
          ]),
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
        'constraints' => [
          new NotBlank([
            'message' => 'constraints.not_blank',
          ]),
          new Assert\Length([
            'min' => 8,
            'minMessage' => 'constraints.min_length',
            'max' => 250,
            'maxMessage' => 'constraints.max_length',
          ]),
          new Assert\Regex([
            'pattern' => '/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/',
            'message' => 'constraints.password',
          ]),
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

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
      'translation_domain' => 'forms',
    ]);
  }
}
