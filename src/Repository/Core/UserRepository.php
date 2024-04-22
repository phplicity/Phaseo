<?php

namespace App\Repository\Core;

use App\Dto\Core\SearchParamsDto;
use App\Entity\Core\User;
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

    public function getList(SearchParamsDto $searchParamsDto): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('u.id', 'u.email', 'u.roles')
            ->from(User::class, 'u')
            ->setMaxResults($searchParamsDto->getLimit())
            ->setFirstResult($searchParamsDto->getPage())
        ;

        if (!empty($searchParamsDto->getSearchText())) {
            $qb
                ->andWhere('u.id = :id OR u.email LIKE :email')
                ->setParameter(':id', $searchParamsDto->getSearchText())
                ->setParameter(':email', '%' . $searchParamsDto->getSearchText() . '%')
            ;
        }

        if (!empty($searchParamsDto->getOrderColumn())) {
            $type = $searchParamsDto->getIsDescendingOrder() ? 'DESC' : 'ASC';
            $qb->orderBy('u.' . $searchParamsDto->getOrderColumn(), $type);
        } else {
            $qb->orderBy('u.id', 'ASC');
        }

        return $qb->getQuery()->getArrayResult();
    }
}
