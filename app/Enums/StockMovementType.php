<?php

namespace App\Enums;

enum StockMovementType: string
{
    case IN = 'IN';
    case OUT = 'OUT';
    case ADJUSTMENT = 'ADJUSTMENT';
    case CHECKING_RESULT = 'CHECKING_RESULT';
    case WI_CONSUMPTION = 'WI_CONSUMPTION';

    public function getLabel(): string
    {
        return match($this) {
            self::IN => 'Stock In',
            self::OUT => 'Stock Out',
            self::ADJUSTMENT => 'Manual Adjustment',
            self::CHECKING_RESULT => 'Checking Result',
            self::WI_CONSUMPTION => 'Work Instruction Consumption',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::IN => 'green',
            self::OUT => 'red',
            self::ADJUSTMENT => 'yellow',
            self::CHECKING_RESULT => 'purple',
            self::WI_CONSUMPTION => 'orange',
        };
    }

    public function isPositive(): bool
    {
        return match($this) {
            self::IN=> true,
            self::OUT, self::WI_CONSUMPTION=> false,
            self::ADJUSTMENT, self::CHECKING_RESULT => false, // neutral or depends on quantity
        };
    }
}
