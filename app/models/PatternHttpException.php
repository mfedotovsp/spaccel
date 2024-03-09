<?php

namespace app\models;

use yii\web\HttpException;

class PatternHttpException
{
    /**
     * @return void
     * @throws HttpException
     */
    public static function noAccess(): void
    {
        throw new HttpException(200, 'У Вас нет доступа по данному адресу');
    }

    /**
     * @return void
     * @throws HttpException
     */
    public static function noData(): void
    {
        throw new HttpException(200, 'Отсутствуют данные на сервере');
    }
}