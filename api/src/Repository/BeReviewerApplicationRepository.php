<?php

namespace App\Repository;

use App\Entity\BeReviewerApplication;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BeReviewerApplication>
 *
 * @method BeReviewerApplication|null find($id, $lockMode = null, $lockVersion = null)
 * @method BeReviewerApplication|null findOneBy(array $criteria, array $orderBy = null)
 * @method BeReviewerApplication[]    findAll()
 * @method BeReviewerApplication[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BeReviewerApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BeReviewerApplication::class);
    }

    public function save(BeReviewerApplication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BeReviewerApplication $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return BeReviewerApplication[] Returns an array of BeReviewerApplication objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BeReviewerApplication
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
