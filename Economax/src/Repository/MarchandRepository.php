<?php

namespace App\Repository;

use App\Entity\Marchand;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Marchand>
 *
 * @method Marchand|null find($id, $lockMode = null, $lockVersion = null)
 * @method Marchand|null findOneBy(array $criteria, array $orderBy = null)
 * @method Marchand[]    findAll()
 * @method Marchand[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarchandRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Marchand::class);
    }

    public function save(Marchand $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Marchand $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllBySearch(float|bool|int|string|null $search)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
        ;
        return $qb->getQuery()->getResult();
    }

}
