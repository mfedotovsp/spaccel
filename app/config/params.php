<?php

return [
    //'bsVersion' => '4.x', // this will set globally `bsVersion` to Bootstrap 4.x for all Krajee Extensions
    'bsDependencyEnabled' => false,
    'supportEmail' => getenv('SUPPORT_EMAIL'), // автоматическая отправка почты с данного емайл
    'secretKeyExpire' => 60 * 60,                    // время хранения секретного ключа
    'emailActivation' => true,                       // подтверждение нового пользователя по email
];
