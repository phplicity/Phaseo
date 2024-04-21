<?php

namespace App\Dto\Core;

class BreadcrumbDto extends AbstractNavbarDto
{
    public function __construct(
        private readonly string $path,
        private readonly string $title,
        private readonly bool $isActive
    ) {
        parent::__construct($this->path, $this->title);
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }
}
