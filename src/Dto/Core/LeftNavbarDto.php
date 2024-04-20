<?php

namespace App\Dto\Core;

class LeftNavbarDto
{
    public function __construct(
        private readonly string $path,
        private readonly string $title
    ) {
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
