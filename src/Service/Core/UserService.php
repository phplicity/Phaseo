<?php

namespace App\Service\Core;

use App\Dto\Core\SearchParamsDto;
use App\Entity\Core\User;
use Doctrine\ORM\EntityManagerInterface;

readonly class UserService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function getList(SearchParamsDto $searchParamsDto): array
    {
        $userRepository = $this->em->getRepository(User::class);
        return $userRepository->getList($searchParamsDto);
    }
}