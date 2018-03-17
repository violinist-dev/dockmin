<?php

namespace App\Repository;

use App\Entity\ServerCredential;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ServerCredential|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServerCredential|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServerCredential[]    findAll()
 * @method ServerCredential[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServerCredentialRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ServerCredential::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
