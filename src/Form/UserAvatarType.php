<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserAvatarType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->setAttributes([
        'enctype' => 'multipart/form-data',
        'method' => 'POST'
      ])

      ->add('image', FileType::class, [
        'data_class' => null,
        'required' => false,
        'mapped' => false,
        'attr' => [
          'id' => 'inputImageHolder',
          'data-preview' => 'holderAvatar',
          'class' => 'input-image',
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

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}
