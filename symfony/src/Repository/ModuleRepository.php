<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Module>
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    public function findAllForUserAndLeads(User $user): array
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.owner', 'u') // relation entre Module et User
            ->where('u = :user')
            ->orWhere('u IN (:ledUsers)')
            ->setParameter('user', $user)
            ->setParameter('ledUsers', $user->getUsers());

        return $qb->getQuery()->getResult();
    }

    public function userCanAccessModule(User $user, Module $module): bool
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->leftJoin('m.owner', 'u')
            ->where('m = :module')
            ->andWhere('u = :user OR u IN (:ledUsers)')
            ->setParameter('module', $module)
            ->setParameter('user', $user)
            ->setParameter('ledUsers', $user->getUsers());

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }


    //    /**
    //     * @return Module[] Returns an array of Module objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Module
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
