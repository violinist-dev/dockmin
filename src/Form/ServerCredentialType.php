<?php

namespace App\Form;

use App\Entity\ServerCredential;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ServerCredentialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('username', TextType::class, ['mapped' => false, 'label' => 'Server Username'])
            ->add('key', TextareaType::class, ['mapped' => false, 'required' => false, 'label' => 'Server Key (content or path to key)'])
            ->add('password', PasswordType::class, ['mapped' => false, 'required' => false, 'label' => 'Server Password (if no key)'])
            ->add('currentPassword', PasswordType::class, ['mapped' => false, 'required' => false, 'label' => 'You current Dockmin password', 'constraints' => new UserPassword()])
            ->add('active')
            ->add('save', SubmitType::class, ['label' => 'Save'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ServerCredential::class,
        ]);
    }
}
