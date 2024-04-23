<?php

namespace App\Service\Core;

use App\Dto\Core\SearchParamsDto;
use App\Entity\Core\User;
use App\Repository\Core\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class UserService
{
    public const COLUMN_NAMES_IN_ORDER = [
        'id',
        'email',
        'roles',
    ];

    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function getListWithCount(SearchParamsDto $searchParamsDto): array
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->em->getRepository(User::class);
        return [
            'data' => $userRepository->getList($searchParamsDto),
            'recordsFiltered' => $userRepository->getFilteredListCount($searchParamsDto),
            'recordsTotal' => $userRepository->getListCount(),
        ];
    }
}
