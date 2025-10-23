<?php

namespace App\Enums;

use App\Attributes\Description;

enum MeasureUnitEnum: string
{
    use HasEnums;

    #[Description('Piece')]
    case PIECE = 'piece';

    #[Description('Pound')]
    case POUND = 'lb';

    #[Description('Ounce')]
    case OUNCE = 'oz';

    #[Description('Gram')]
    case GRAM = 'g';

    #[Description('Kilogram')]
    case KILOGRAM = 'kg';

    #[Description('Liter')]
    case LITER = 'l';

    #[Description('Milliliter')]
    case MILLILITER = 'ml';

    #[Description('Other')]
    case OTHER = 'other';
}
