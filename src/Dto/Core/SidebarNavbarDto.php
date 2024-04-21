<?php

namespace App\Dto\Core;

class SidebarNavbarDto extends AbstractNavbarDto
{
    public function __construct(
        private readonly string $path,
        private readonly string $title,
        private readonly bool $isActive,
        private readonly string $class,
        private readonly array $children
    ) {
        parent::__construct($this->path, $this->title);
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getChildren(): array
    {
        return $this->children;
    }
}
