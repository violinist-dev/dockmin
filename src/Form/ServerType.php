<?php

namespace App\Form;

use App\Entity\Server;

use App\Repository\ServerCredentialRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ServerType.
 *
 * @package App\Form
 */
class ServerType extends AbstractType
{

    private $tokenStorage;

    /**
     * ServerType constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $this->tokenStorage->getToken()->getUser();

        $server_credentials_query_builder = function (ServerCredentialRepository $er) use ($user) {
            return $er->createQueryBuilder('sc')
                ->andWhere('sc.owner = :owner')
                ->setParameter('owner', $user->getId())
                ->addOrderBy('sc.name');
        };

        $builder
            ->add('ip')
            ->add('port')
            ->add('name')
            ->add('serverCredential', null, ['query_builder' => $server_credentials_query_builder])
            ->add('active')
            ->add('save', SubmitType::class, ['label' => 'Save']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Server::class,
        ]);
    }
}
