<?php

namespace App\Service\Core;

use Symfony\Component\Security\Core\User\UserInterface;

readonly class DatatableService
{
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
                return array_merge(array_values($dbRow), $this->getButtonsByRoles($user));
            }, $sqlResult['data']),
            'draw' => $draw,
            'recordsFiltered' => $sqlResult['recordsFiltered'],
            'recordsTotal' => $sqlResult['recordsTotal'],
        ];
    }

    private function getButtonsByRoles(UserInterface $user): array
    {
        // TODO: kell egy HTML renderelés, amely visszaadja a gombokat html-ként
        return [
            'create',
            'edit',
            'delete',
            'restore',
        ];
    }
}