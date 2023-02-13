<?php

namespace App\Tests\Enums;

enum Roles: string
{
    case ADMIN = 'ADMIN';
    case REVIEWER = 'REVIEWER';
    case CONTRIBUTOR = 'CONTRIBUTOR';
    case USER = 'USER';
}