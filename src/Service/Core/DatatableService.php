<?php

namespace App\Service\Core;

use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

readonly class DatatableService
{
    public function __construct(
        private Environment $twig
    )
    {}

    public function getHeaders(string $prefix, array $columnNamesInOrder): array
    {
        return array_merge(
            array_map(function (string $columnName) use ($prefix) {
                return "$prefix.$columnName";
            }, $columnNamesInOrder),
            ["$prefix.actions"]
        );
    }

    public function convertSqlResultToDatatableResult(UserInterface $user, int $draw, array $sqlResult): array
    {
        return [
            'data' => array_map(function (array $dbRow) use ($user) {
                return array_merge(array_values($dbRow), [$this->getButtonsByRoles($user, $dbRow['id'])]);
            }, $sqlResult['data']),
            'draw' => $draw,
            'recordsFiltered' => $sqlResult['recordsFiltered'],
            'recordsTotal' => $sqlResult['recordsTotal'],
        ];
    }

    private function getButtonsByRoles(UserInterface $user, int $userId): string
    {
        $buttons = [
            'edit',
            'delete',
        ];

        return $this->twig->render('core/users/users_list_buttons.html.twig', ['buttons' => $buttons, 'userId' => $userId]);
    }
}