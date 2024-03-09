<?php

namespace app\models;

class SizesWishList
{
    public const LARGE = 135; // Крупное предприятие
    public const MIDDLE = 253; // Среднее предприятие
    public const SMALL = 739; // Малое предприятие

    public const LABEL_LARGE = 'Крупное предприятие';
    public const LABEL_MIDDLE = 'Среднее предприятие';
    public const LABEL_SMALL = 'Малое предприятие';

    /**
     * @return string[]
     */
    public static function getList(): array
    {
        return [
            self::LARGE => self::LABEL_LARGE,
            self::MIDDLE => self::LABEL_MIDDLE,
            self::SMALL => self::LABEL_SMALL,
        ];
    }
}