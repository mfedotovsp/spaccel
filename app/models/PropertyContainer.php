<?php


namespace app\models;

use yii\base\Exception;
use app\models\interfaces\PropertyContainerInterface;
use yii\base\Model;

/**
 * Класс для реализации паттерна проектирования "Контейнер свойств"
 *
 * Class PropertyContainer
 * @package app\models
 *
 * @property array $propertyContainer
 */
class PropertyContainer extends Model implements PropertyContainerInterface
{

    /**
     * @var array
     */
    private $propertyContainer = [];


    /**
     * @param $propertyName
     * @param $value
     * @return void
     */
    public function addProperty($propertyName, $value): void
    {
        $this->propertyContainer[$propertyName] = $value;
    }


    /**
     * @param $propertyName
     * @return void
     */
    public function deleteProperty($propertyName): void
    {
        unset($this->propertyContainer[$propertyName]);
    }


    /**
     * @param $propertyName
     * @return mixed
     */
    public function getProperty($propertyName)
    {
        return $this->propertyContainer[$propertyName] ?: null;
    }


    /**
     * @param $propertyName
     * @param $value
     * @return void
     * @throws Exception
     */
    public function setProperty($propertyName, $value): void
    {
        if (!isset($this->propertyContainer[$propertyName])) {
            throw new Exception("Property $propertyName not found");
        }

        $this->propertyContainer[$propertyName] = $value;
    }
}