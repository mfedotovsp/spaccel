<?php

namespace app\models\interfaces;


interface PropertyContainerInterface
{

    /**
     * @param $propertyName
     * @param $value
     * @return mixed
     */
    public function addProperty($propertyName, $value);


    /**
     * @param $propertyName
     * @return mixed
     */
    public function deleteProperty($propertyName);


    /**
     * @param $propertyName
     * @return mixed
     */
    public function getProperty($propertyName);


    /**
     * @param $propertyName
     * @param $value
     * @return mixed
     */
    public function setProperty($propertyName, $value);
}