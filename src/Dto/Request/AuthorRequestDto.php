<?php

namespace App\Dto\Request;

use ApiPlatform\Metadata\ApiProperty;

readonly class AuthorRequestDto
{
    public function __construct(

        #[ApiProperty(readable: false, writable: true)]
        public string  $lastName,

        #[ApiProperty(readable: false, writable: true)]
        public string  $firstName,

        #[ApiProperty(readable: false, writable: true)]
        public ?string $pseudonym = null
    ) {}

    public static function fromRequest(array $requestData): self
    {
        return new self(
            lastName: $requestData['lastName'] ?? '',
            firstName: $requestData['firstName'] ?? '',
            pseudonym: $requestData['pseudonym'] ?? null
        );
    }
}