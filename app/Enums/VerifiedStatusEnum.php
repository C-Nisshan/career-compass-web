<?php

namespace App\Enums;

enum VerifiedStatusEnum: string
{
    case Pending = 'pending';
    case Rejected = 'rejected';
    case Approved = 'approved';
}