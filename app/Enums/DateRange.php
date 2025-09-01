<?php

namespace App\Enums;

enum DateRange: string
{
    case WEEK = 'week';
    case FORTNIGHT = 'fortnight';
    case MONTH = 'month';
    case YEAR = 'year';
}
