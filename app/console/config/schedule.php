<?php

/**
 * @var \omnilight\scheduling\Schedule $schedule
 */

// Для работы нужно настроить локальный файл crontab: crontab -e
// Добавить в него строку: * * * * * cd ./server && docker compose exec php-fpm php ./yii schedule/run --scheduleFile=./console/config/schedule.php 1>> /dev/null 2>&1

//Время указывается в нулевом часовом поясе, т.е. -3 часа от времени по Москве

$schedule->command('backup/create')->dailyAt('21:00');
