<?php

namespace App\Dto\Author\Request;

use ApiPlatform\Metadata\ApiProperty;

readonly class AuthorPostDto
{
    public function __construct(
        #[ApiProperty(readable: false, writable: true)]
        public string  $lastName,
        #[ApiProperty(readable: false, writable: true)]
        public string  $firstName,
        #[ApiProperty(readable: false, writable: true)]
        public ?string $pseudonym = null
    ) {}
}