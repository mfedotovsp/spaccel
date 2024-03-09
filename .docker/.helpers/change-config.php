<?php

const PATH_TO_DB_CONFIG = 'config/db.php';

$config = file_get_contents(PATH_TO_DB_CONFIG);

$config = preg_replace('=host\=localhost=', 'host=mysql', $config);
$config = preg_replace('=dbname\=yii2basic=', 'dbname=' . getenv('MYSQL_DATABASE'), $config);

$config = preg_replace("='username' \=> 'root'=", "'username' => '" . getenv('MYSQL_USER') . "'", $config);
$config = preg_replace("='password' \=> ''=", "'password' => '" . getenv('MYSQL_PASSWORD') . "'", $config);

file_put_contents(PATH_TO_DB_CONFIG, $config);

// ---------------------------------------------------------------------------------------------------------------------

const PATH_TO_WEB_CONFIG = 'config/web.php';

$config = file_get_contents(PATH_TO_WEB_CONFIG);

// Uncommenting the "urlManager" component
$config = preg_replace("=/\*\s+?(')=", '$1', $config);
$config = preg_replace("=\s+?\*/=", '', $config);

file_put_contents(PATH_TO_WEB_CONFIG, $config);
