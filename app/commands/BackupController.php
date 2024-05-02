<?php

namespace app\commands;

use DateTime;
use DateTimeZone;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class BackupController extends Controller
{
    /**
     * @throws Exception
     */
    public function actionCreate(): int
    {
        preg_match('#mysql:host=([^;]+);dbname=(.*)#', Yii::$app->components['db']['dsn'], $dbMatch);
        $db = [
            'host' => $dbMatch[1],
            'user' => 'root',
            'pass' => getenv('MYSQL_ROOT_PASSWORD'),
            'base' => $dbMatch[2]
        ];

        $path = 'tmp/backups';
        $dateTimeNow = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format('Y-m-d_H:i:s');
        $filename = $db['base'] . '_' . $dateTimeNow . '.sql.gz';
        $command = "mysqldump --user={$db['user']} --password={$db['pass']} --host={$db['host']} {$db['base']} | gzip > {$path}/{$filename}";
        exec($command);

        $files = glob($path . '/*');
        if (count($files) > 14) {
            // Храним бэкапы за 2 недели и удаляем старые бэкапы
            usort($files, static function($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            $needDeleteFiles = array_slice($files, 0, -14);
            foreach ($needDeleteFiles as $needDeleteFile) {
                unlink($needDeleteFile);
            }
        }

        return ExitCode::OK;
    }

    public function actionRestore(string $filename): int
    {
        preg_match('#mysql:host=([^;]+);dbname=(.*)#', Yii::$app->components['db']['dsn'], $dbMatch);
        $db = [
            'host' => $dbMatch[1],
            'user' => 'root',
            'pass' => getenv('MYSQL_ROOT_PASSWORD'),
            'base' => $dbMatch[2]
        ];

        $path = 'tmp/backups';
        $command = "gunzip < {$path}/{$filename} | mysql --user={$db['user']} --password={$db['pass']} --host={$db['host']} {$db['base']}";
        exec($command);

        return ExitCode::OK;
    }
}
