<?php


namespace app\models;


/**
 * Типы экспертов
 * Class ExpertType
 * @package app\models
 */
abstract class ExpertType
{

    public const EXPERT_IN_SCIENTIFIC_FIELD = 1;
    public const DEVELOPER_CONSTRUCTOR = 2;
    public const DEVELOPER_PROGRAMMER = 3;
    public const INDUSTRY_MARKETING_SPECIALIST = 4;
    public const SPECIALIST_IN_INTELLECTUAL_PROPERTY_REGISTRATION = 5;
    public const COMMUNICATIONS_SPECIALIST = 6;


    /**
     * @var array
     */
    private static $listTypes = [
        1 => 'Ученый-эксперт в научной сфере',
        2 => 'Разработчик-конструктор',
        3 => 'Разработчик-программист',
        4 => 'Отраслевой специалист по маркетингу',
        5 => 'Специалист по регистрации ИС',
        6 => 'Специалист по коммуникациям'
    ];


    /**
     * @param User|null $expert
     * @param string|null $strKeys
     * @return array
     */
    public static function getListTypes(User $expert = null, string $strKeys = null): array
    {

        $list = array();

        if ($expert && !$strKeys) {

            $keys = self::getValue($expert->expertInfo->getType());
            foreach ($keys as $key) {
                $list[$key] = self::$listTypes[$key];
            }

            return $list;

        }

        if (!$expert && $strKeys) {

            $keys = self::getValue($strKeys);
            foreach ($keys as $key) {
                $list[$key] = self::$listTypes[$key];
            }

            return $list;
        }

        return self::$listTypes;
    }


    /**
     * @param $value string
     * @return false|int|string
     */
    public static function getKey(string $value)
    {
        return array_search($value, self::$listTypes, false);
    }


    /**
     * @param string $types
     * @return array
     */
    public static function getValue(string $types): array
    {
        return explode('|', $types);
    }


    /**
     * @param string $types
     * @return string
     */
    public static function getContent(string $types): string
    {
        $array = array();
        foreach (self::getValue($types) as $value) {
            $array[] = self::getListTypes()[$value];
        }
        return implode(', ', $array);
    }
}