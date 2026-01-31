<?php

namespace App\Enums;

enum ReceiptStatus: string
{
    case NEEDS_REVIEW = 'needs_review';
    case CONFIRMED = 'confirmed';
    case UPLOADED = 'uploaded';
}
