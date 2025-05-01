<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class UserAvatarType
 *
 * Form for managing the user's avatar image.
 * This form allows the user to upload or modify their avatar image.
 *
 */
class UserAvatarType extends AbstractType
{
  /**
   * Builds the form for uploading or modifying the user avatar image.
   *
   * @param FormBuilderInterface $builder The form builder.
   * @param array $options The options configured for the form.
   *
   * @return void
   */
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->setAttributes([
        'enctype' => 'multipart/form-data',
        'method' => 'POST'
      ])

      ->add('image', FileType::class, [
        'label' => 'Ajouter/modifier une image',
        'data_class' => null,
        'required' => false,
        'mapped' => false,
        'attr' => [
          'id' => 'inputImageHolder',
          'data-preview' => 'holderAvatar',
          'class' => 'input-image btn-outline',
        ]
      ])

      ->add('save', SubmitType::class, [
        'label' => 'Enregistrer',
        'attr' => [
          'class' => 'btn'
        ]
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
    ]);
  }
}
