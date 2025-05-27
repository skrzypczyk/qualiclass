<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\Program;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Program>
 */
class ProgramRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
    }

    public function findAllForUserAndLeads(User $user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.owner', 'u') // relation entre Module et User
            ->where('u = :user')
            ->orWhere('u IN (:ledUsers)')
            ->setParameter('user', $user)
            ->setParameter('ledUsers', $user->getUsers());

        return $qb->getQuery()->getResult();
    }


    public function userCanAccessModule(User $user, Program $program): bool
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->leftJoin('p.owner', 'u')
            ->where('p = :program')
            ->andWhere('u = :user OR u IN (:ledUsers)')
            ->setParameter('program', $program)
            ->setParameter('user', $user)
            ->setParameter('ledUsers', $user->getUsers());

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    //    /**
    //     * @return Program[] Returns an array of Program objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Program
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
