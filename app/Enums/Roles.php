<?php

declare(strict_types=1);

namespace App\Enums;

enum Roles: string
{
    case Admin = 'admin';
    case Employee = 'employee';

    public function labels(): string
    {
        return match ($this) {
            self::Admin => 'Amministratore',
            self::Employee => 'Developer',
        };
    }
}
