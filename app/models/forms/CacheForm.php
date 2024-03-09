<?php


namespace app\models\forms;

use Yii;
use yii\base\ErrorException;
use yii\helpers\FileHelper;

/**
 * Менеджер для работы с кэшем
 *
 * Class CacheForm
 * @package app\models\forms
 */
class CacheForm
{

    /**
     * @param $path
     * @param $name
     */
    public function setCache($path, $name): void
    {
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        $data = $_POST; //Массив, в который будем записывать в кэш
        $cache->cachePath = $path;
        $key = $name; //Формируем ключ
        $cache->set($key, $data, 3600*24*30); //Создаем файл кэша на 30дней
    }


    /**
     * Получить данные кэша
     *
     * @param $path
     * @param $name
     * @return mixed
     */
    public function getCache($path, $name)
    {
        $cache = Yii::$app->cache;
        $cache->cachePath = $path;
        return $cache->get($name);
    }


    /**
     * Удалить данные кэша
     *
     * @param $path
     * @throws ErrorException
     */
    public function deleteCache($path): void
    {
        if (file_exists($path)) {
            FileHelper::removeDirectory($path);
        }
    }

}