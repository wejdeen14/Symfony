<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    public function findByRole(){
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_MEMBER"%')
            ->getQuery()
            ->getResult();
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
    public function Search($search): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT name,id FROM user u
            WHERE u.name LIKE :search and u.roles LIKE :role
            ORDER BY u.name ASC
            ';
            $searchTerm = '%' . $search . '%';

        $resultSet = $conn->executeQuery($sql, ['role' => '%"ROLE_MEMBER"%','search' => $searchTerm]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function friends($id, EntityManagerInterface $entityManager): array {
        $qb = $entityManager->createQueryBuilder();
    
        $qb->select('u.name, u.id,a.id')
            ->from('App\Entity\User', 'u')
            ->join('App\Entity\Amis', 'a', 'WITH', 'a.amis = u.id')
            ->where($qb->expr()->eq('a.status', ':status'))
            ->andWhere($qb->expr()->eq('a.utilisateur', ':id'))
            ->orderBy('u.name', 'ASC');
    
        $qb->setParameter('status', 'invite');
        $qb->setParameter('id', $id);
    
        $query = $qb->getQuery();
        $friends = $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    
        return $friends;
    }

    public function ListeFriends():array{
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT *
        FROM user u
        JOIN amis a ON a.utilisateur_id = u.id
        WHERE u.roles LIKE :role AND a.status = :status';
        $resultSet = $conn->executeQuery($sql, ['role' => '%"ROLE_MEMBER"%','Amis'=>'invite']);
        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function ListeContenus($id):array{
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        SELECT c.text,a.name
        FROM contenu c
        JOIN amis a ON a.amis_id = c.user_id
        WHERE a.utilisateur_id = :id';
        $resultSet = $conn->executeQuery($sql, ['id'=>$id]);
        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
    
    

}
