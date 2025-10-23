<?php

namespace App\Enums;

use App\Attributes\Description;

enum OrderTypeEnum: string
{
    use HasEnums;

    #[Description('Dine In')]
    case DINE_IN = 'dine_in';

    #[Description('Takeaway')]
    case TAKEAWAY = 'takeaway';

    #[Description('Delivery')]
    case DELIVERY = 'delivery';

    #[Description('Other')]
    case OTHER = 'other';
}
