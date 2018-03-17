<?php

namespace App\Repository;

use App\Entity\DockerInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DockerInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method DockerInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method DockerInfo[]    findAll()
 * @method DockerInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DockerInfoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DockerInfo::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('d')
            ->where('d.something = :value')->setParameter('value', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
