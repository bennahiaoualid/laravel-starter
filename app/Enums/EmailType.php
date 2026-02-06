<?php

namespace App\Enums;

enum EmailType: string
{
    case MISS_CALCULATION = 'miss_calculation';
    case PARTY_BALANCE_MISMATCH = 'party_balance_mismatch';
    case INVESTOR_BALANCE_MISMATCH = 'investor_balance_mismatch';
}
