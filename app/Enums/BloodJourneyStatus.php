<?php

namespace App\Enums;

enum BloodJourneyStatus: string
{
    case MATCHED = 'matched';
    case ACCEPTED = 'accepted';
    case DONATED = 'donated';
    case VERIFIED = 'verified';
    case DELIVERED = 'delivered';
    case DISCARDED = 'discarded';
}
