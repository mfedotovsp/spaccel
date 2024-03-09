<?php

// внутри контейнера mysql дать права пользователям выполнять действия из любого пространства
// docker exec -it spaccel-mysql-1 bash
// ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY '123456789';
// ALTER USER 'user_spaccel'@'%' IDENTIFIED WITH mysql_native_password BY '12345678';
// FLUSH PRIVILEGES;

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=mysql;dbname='.getenv('MYSQL_DATABASE'),
    'username' => getenv('MYSQL_USER'),
    'password' => getenv('MYSQL_PASSWORD'),
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
