<?php

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'ROLE_ADMIN';
    case REVIEWER = 'ROLE_REVIEWER';
    case CONTRIBUTOR = 'ROLE_CONTRIBUTOR';
    case USER = 'ROLE_USER';
}