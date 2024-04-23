<?php

namespace App\Repository\Core\User;

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
    public function __construct(ManagerRegistry $registry, private readonly UserListHelper $userListHelper)
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
        $qb = $this->userListHelper->getBaseQueryBuilder($qb, false, $searchParamsDto);
        $qb = $this->userListHelper->getSearchParamsForQueryBuilder($qb, $searchParamsDto);
        $qb = $this->userListHelper->getOrderForQueryBuilder($qb, $searchParamsDto);

        return $qb->getQuery()->getArrayResult();
    }

    public function getListCount(): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb = $this->userListHelper->getBaseQueryBuilder($qb, true, null);

        return (int) $qb->getQuery()->getScalarResult()[0][1];
    }

    public function getFilteredListCount(SearchParamsDto $searchParamsDto): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb = $this->userListHelper->getBaseQueryBuilder($qb, true, $searchParamsDto);
        $qb = $this->userListHelper->getSearchParamsForQueryBuilder($qb, $searchParamsDto);

        return (int) $qb->getQuery()->getScalarResult()[0][1];
    }
}
