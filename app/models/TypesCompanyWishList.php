<?php

namespace app\models;

class TypesCompanyWishList
{
    public const INDUSTRIAL = 135; // Промышленное производство
    public const AGRICULTURAL = 253; // Сельскохозяйственное производство
    public const RETAIL_TRADE = 739; // Розничная торговля
    public const WHOLESALE = 490; // Оптовая торговля

    public const LABEL_INDUSTRIAL = 'Промышленное производство';
    public const LABEL_AGRICULTURAL = 'Сельскохозяйственное производство';
    public const LABEL_RETAIL_TRADE = 'Розничная торговля';
    public const LABEL_WHOLESALE = 'Оптовая торговля';

    /**
     * @return string[]
     */
    public static function getList(): array
    {
        return [
            self::INDUSTRIAL => self::LABEL_INDUSTRIAL,
            self::AGRICULTURAL => self::LABEL_AGRICULTURAL,
            self::RETAIL_TRADE => self::LABEL_RETAIL_TRADE,
            self::WHOLESALE => self::LABEL_WHOLESALE
        ];
    }
}