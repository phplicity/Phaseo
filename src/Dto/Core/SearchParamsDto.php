<?php

namespace App\Dto\Core;

use Symfony\Component\HttpFoundation\Request;

class SearchParamsDto
{
    public function __construct(
        private readonly Request $rq
    ) {
    }

    public function getPage(): int
    {
        return (int) $this->rq->get('page', 0);
    }

    public function getLimit(): int
    {
        return (int) $this->rq->get('limit', 20);
    }

    public function getSearchText(): string
    {
        return $this->rq->get('search_text', '');
    }

    public function getOrderColumn(): string
    {
        return $this->rq->get('order_column', '');
    }

    public function getIsDescendingOrder(): bool
    {
        return (bool) $this->rq->get('is_descending_order', false);
    }
}
