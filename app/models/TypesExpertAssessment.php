<?php


namespace app\models;

/**
 * Класс для получения типа экспертной оценки для вывода нужной формы экспертизы
 */
class TypesExpertAssessment
{

    public const ASSESSMENT_TECHNOLOGICAL_LEVEL = 101;
    public const ASSESSMENT_CONSUMER_SETTINGS = 202;

    private static $listTechnologicalLevel = array(1, 2, 3, 4);
    private static $listConsumerSettings = array(5, 6);

    /**
     * Получить тип экспертной оценки
     * по типу деятельности эксперта в экспертизе
     * @param $key
     * @return bool|int
     */
    public static function getValue($key)
    {
        if (in_array($key, self::$listTechnologicalLevel, false)) {

            return self::ASSESSMENT_TECHNOLOGICAL_LEVEL;

        }

        if (in_array($key, self::$listConsumerSettings, false)) {

            return self::ASSESSMENT_CONSUMER_SETTINGS;

        }

        return false;
    }

}