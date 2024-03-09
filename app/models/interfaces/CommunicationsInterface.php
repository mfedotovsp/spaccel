<?php


namespace app\models\interfaces;


interface CommunicationsInterface
{

    /**
     * Получить id коммуникации
     * @return int
     */
    public function getId(): int;

    /**
     * Получить id отправителя коммуникации
     * @return int
     */
    public function getSenderId(): int;


    /**
     * Получить id получателя коммуникации
     * @return int
     */
    public function getAdresseeId(): int;


    /**
     * Установить параметр
     * прочтения коммуникации
     * @return void
     */
    public function setStatusRead(): void;

}