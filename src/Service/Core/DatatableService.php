<?php

namespace App\Service\Core;

readonly class DatatableService
{
    public function getHeaders(string $prefix, array $columnNamesInOrder): array
    {
        return array_map(function (string $columnName) use ($prefix) {
            return "$prefix.$columnName";
        }, $columnNamesInOrder);
    }

    public function convertSqlResultToDatatableResult(int $draw, array $sqlResult)
    {
        return [
            'data' => array_map(function (array $dbRow) {
                return array_values($dbRow);
            }, $sqlResult['data']),
            'draw' => $draw,
            'recordsFiltered' => $sqlResult['recordsFiltered'],
            'recordsTotal' => $sqlResult['recordsTotal'],
        ];
    }
}