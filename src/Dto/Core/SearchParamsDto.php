<?php

namespace App\Dto\Core;

use Symfony\Component\HttpFoundation\Request;

class SearchParamsDto
{
    public function __construct(
        private readonly Request $rq,
        private readonly array $columns
    ) {
    }

    public function getPage(): int
    {
        return (int) $this->rq->get('start', 0);
    }

    public function getLimit(): int
    {
        return (int) $this->rq->get('length', 20);
    }

    public function getSearchText(): string
    {
        $searchArray = $this->rq->get('search', []);

        if (empty($searchArray)) {
            return '';
        }

        return $searchArray['value'] ?? '';
    }

    public function getOrderColumn(): string
    {
        $orderArray = $this->rq->get('order', []);

        if (empty($orderArray)) {
            return '';
        }

        return isset($orderArray['column']) && isset($this->columns[$orderArray['column']])
            ? $this->columns[$orderArray['column']]
            : ''
        ;
    }

    public function getIsDescendingOrder(): bool
    {
        $orderArray = $this->rq->get('order', []);

        if (empty($orderArray)) {
            return '';
        }

        return isset($orderArray['dir']) && $orderArray['dir'] == 'desc';
    }
}
