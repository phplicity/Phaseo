<?php

namespace App\Repository\Core\User;

use App\Dto\Core\SearchParamsDto;
use App\Entity\Core\User;
use App\Service\Core\UserService;
use Doctrine\ORM\QueryBuilder;

class UserListHelper
{
    public function getBaseQueryBuilder(QueryBuilder $qb, bool $isQueryBuilderForCount, ?SearchParamsDto $searchParamsDto): QueryBuilder
    {
        if ($isQueryBuilderForCount) {
            return $qb
                ->select($qb->expr()->count('u'))
                ->from(User::class, 'u')
            ;
        }

        return $qb
            ->select(array_map(
                function (string $columnName) { return "u.$columnName"; },
                UserService::COLUMN_NAMES_IN_ORDER)
            )
            ->from(User::class, 'u')
            ->setMaxResults($searchParamsDto->getLimit())
            ->setFirstResult($searchParamsDto->getPage())
        ;
    }

    public function getSearchParamsForQueryBuilder(QueryBuilder $qb, SearchParamsDto $searchParamsDto): QueryBuilder
    {
        if (!empty($searchParamsDto->getSearchText())) {
            return $qb
                ->andWhere('u.id = :id OR u.email LIKE :email')
                ->setParameter(':id', $searchParamsDto->getSearchText())
                ->setParameter(':email', '%' . $searchParamsDto->getSearchText() . '%')
            ;
        }

        return $qb;
    }

    public function getOrderForQueryBuilder(QueryBuilder $qb, SearchParamsDto $searchParamsDto): QueryBuilder
    {
        if (!empty($searchParamsDto->getOrderColumn())) {
            $type = $searchParamsDto->getIsDescendingOrder() ? 'DESC' : 'ASC';
            return $qb->orderBy('u.' . $searchParamsDto->getOrderColumn(), $type);
        }

        return $qb->orderBy('u.id', 'ASC');
    }
}
