<?php

namespace App\Repository;

use App\Entity\Deal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Deal>
 *
 * @method Deal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deal[]    findAll()
 * @method Deal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DealRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deal::class);
    }

    public function save(Deal $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Deal $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // récupére les deals lister par nombre de commentaires décroissant
    // en mettant en avant uniquement les deals de moins de 1 semaine.
    public function findAllByComment() : array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d')
            ->leftJoin('d.comments', 'c')
            ->addSelect('COUNT(c) AS HIDDEN nbComments')
            ->groupBy('d')
            ->orderBy('nbComments', 'DESC')
            ->where('d.createdAt > :date')
            ->setParameter('date', new \DateTime('-1 week'))
        ;

        return $qb->getQuery()->getResult();
    }

    // récupére les deals hot (plus de 100°), triés par date de publication décroissante.
    public function findAllHot() : array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d')
            ->where('d.temperature > :temperature')
            ->setParameter('temperature', 100)
            ->orderBy('d.createdAt', 'DESC')
        ;

        return $qb->getQuery()->getResult();
    }
}
