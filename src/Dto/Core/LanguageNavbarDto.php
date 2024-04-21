<?php

namespace App\Dto\Core;

class LanguageNavbarDto extends AbstractNavbarDto
{
    public const LANGUAGE_CODE_ENGLISH_ROUTE = 'en';
    public const LANGUAGE_CODE_ENGLISH = 'us';
    public const LANGUAGE_CODE_HUNGARIAN = 'hu';

    public function __construct(
        private readonly string $path,
        private readonly string $title,
        private readonly bool $isActive,
        private readonly string $languageCode
    ) {
        parent::__construct($this->path, $this->title);
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public  function getLanguageCode(): string
    {
        return $this->languageCode;
    }
}
