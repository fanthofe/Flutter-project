<?php

namespace App\Repository;

use App\Entity\ChatMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChatMessage>
 *
 * @method ChatMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatMessage[]    findAll()
 * @method ChatMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatMessage::class);
    }

    public function add(ChatMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ChatMessage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Count all messages
     * @return int
     */
    public function countAllMessages() : int
    {
        return $this->createQueryBuilder('cm')
            ->select('count(cm.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count all messages from parents
     * @return int
     */
    public function countAllMessagesParent(): int
    {
        return $this->createQueryBuilder('cm')
            ->select('count(cm.id)')
            ->leftJoin('cm.author', 'u')
            ->where('u.parent = :isParent')
            ->setParameter('isParent', 1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count all messages from gardians
     * @return int
     */
    public function countAllMessagesGardian(): int
    {
        return $this->createQueryBuilder('cm')
            ->select('count(cm.id)')
            ->leftJoin('cm.author', 'u')
            ->where('u.parent = :isParent')
            ->setParameter('isParent', 0)
            ->getQuery()
            ->getSingleScalarResult();
    }
    

//    /**
//     * @return ChatMessage[] Returns an array of ChatMessage objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ChatMessage
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
