<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * Get all cities name from users
     * @return array
     */
    public function getAllCities(): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.city')
            ->distinct()
            ->where('u.city IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count users by city
     * @return array
     */
    public function countUsersByCity(): array
    {
        $cities = $this->getAllCities();
        $citiesCount = [];

        foreach ($cities as $city) {
            $citiesCount[$city['city']] = $this->createQueryBuilder('u')
                ->select('count(u.id)')
                ->where('u.city = :city')
                ->setParameter('city', $city['city'])
                ->getQuery()
                ->getSingleScalarResult();
        }

        return $citiesCount;
    }

    /**
     * Count suspended users
    */
    public function countSuspendedUsers(): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.status = 0')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get all cities zip from users
     * @return array
     */
    public function getAllCitiesZip(): array
    {
        return $this->createQueryBuilder('u')
            ->select('u.zip')
            ->distinct()
            ->where('u.zip IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Count all users
     */
    public function countAllUsers(): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count all parents
     */
    public function countAllParents(): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.parent = 1')
            ->getQuery()
            ->getSingleScalarResult();
    }



//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
