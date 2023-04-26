<?php

namespace App\Enums;

enum BeReviewerApplicationStatus: string
{
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case REFUSED = 'REFUSED';
}