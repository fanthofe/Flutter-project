<?php

namespace App\Repository;

use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chat>
 *
 * @method Chat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chat[]    findAll()
 * @method Chat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chat::class);
    }

    public function add(Chat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Chat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Finds and returns an array of Chat objects for a single user.
     *
     * @param int $id The user's ID for whom to find chats.
     * @return Chat[] An array of Chat objects.
     */
    public function findChatsForOneUser($id): array
    {
        $qb = $this->createQueryBuilder('c')
            ->select('u.id as user_id')
            ->addSelect('c.id as chat_id, c.updatedAt as chat_updatedAt')
            ->addSelect('cu.id as participant_id, cu.firstName as participant_firstName, cu.lastName as participant_lastName, cu.profilPicture as participant_profilPicture')
            ->addSelect('cm.id as message_id, cm.content as message_content, cm.createdAt')
            ->distinct()
            ->leftJoin('c.user', 'cu')
            ->leftJoin('c.chatMessages', 'cm')
            ->leftJoin('c.user', 'u')
            
            ->where('u.id = :id')
            ->andWhere('cu.id != :id')
            ->andWhere('c.status = 1')
            ->andWhere('cm.id = (
                SELECT MAX(cm2.id) 
                FROM App\Entity\ChatMessage cm2
                WHERE cm2.chat = c.id
            )')
            ->setParameter('id', $id)
            ->groupBy('c.id', 'cu.id', 'cm.id')
            ->orderBy('c.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $qb;
    }

    /**
     * Finds and returns a single Chat object between two users, if it exists.
     *
     * @param int $user The ID of the first user.
     * @param int $otherUser The ID of the other user.
     * @return Chat|null A Chat object or null if no chat is found.
     */
    public function findChatBetweenTwoUsers($user, $otherUser): ?Chat
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.user', 'u')
            ->leftJoin('c.user', 'ou')
            ->where('u.id = :user')
            ->andWhere('ou.id = :otherUser')
            ->andWhere('c.status = 1')
            ->setParameter('user', $user)
            ->setParameter('otherUser', $otherUser)
            ->getQuery()
            ->getOneOrNullResult();

        return $qb;
    }

    /**
     * Count chats
     */
    public function countAllChats(): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return Chat[] Returns an array of Chat objects
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

//    public function findOneBySomeField($value): ?Chat
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
