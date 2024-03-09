<?php

namespace app\models;

class TypesProductionWishList
{
    public const MASS_PRODUCTION = 1678835; // Массовое производство
    public const LARGE_PRODUCTION = 345653; // Крупносерийное производство
    public const MID_PRODUCTION = 289453; // Среднесерийное производство
    public const SMALL_PRODUCTION = 223253; // Мелкосерийное производство
    public const SINGLE_PRODUCTION = 885434; // Единичное производство

    public const LABEL_MASS_PRODUCTION = 'Массовое производство';
    public const LABEL_LARGE_PRODUCTION = 'Крупносерийное производство';
    public const LABEL_MID_PRODUCTION = 'Среднесерийное производство';
    public const LABEL_SMALL_PRODUCTION = 'Мелкосерийное производство';
    public const LABEL_SINGLE_PRODUCTION = 'Единичное производство';

    /**
     * @return string[]
     */
    public static function getList(): array
    {
        return [
            self::MASS_PRODUCTION => self::LABEL_MASS_PRODUCTION,
            self::LARGE_PRODUCTION => self::LABEL_LARGE_PRODUCTION,
            self::MID_PRODUCTION => self::LABEL_MID_PRODUCTION,
            self::SMALL_PRODUCTION => self::LABEL_SMALL_PRODUCTION,
            self::SINGLE_PRODUCTION => self::LABEL_SINGLE_PRODUCTION
        ];
    }
}