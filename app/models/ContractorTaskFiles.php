<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс хранит информацию о загруженных файлах в заданиях исполнителей
 *
 * Class PreFiles
 * @package app\models
 *
 * @property int $id                                Идентификатор записи
 * @property int $task_id                           Идентификатор задания
 * @property string $file_name                      Имя загруженного файла
 * @property string $server_file                    Сгенерированное имя файла на сервере
 *
 * @property ContractorTasks $task                  Задание
 */
class ContractorTaskFiles extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_task_files';
    }


    /**
     * Получить объект проекта
     * @return ActiveQuery
     */
    public function getTask(): ActiveQuery
    {
        return $this->hasOne(ContractorTasks::class, ['id' => 'task_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['task_id', 'file_name'], 'required'],
            [['task_id'], 'integer'],
            [['file_name', 'server_file'], 'string', 'max' => 255],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'file_name' => 'File Name',
        ];
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->task_id;
    }

    /**
     * @param int $task_id
     */
    public function setTaskId(int $task_id): void
    {
        $this->task_id = $task_id;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->file_name;
    }

    /**
     * @param string $file_name
     */
    public function setFileName(string $file_name): void
    {
        $this->file_name = $file_name;
    }

    /**
     * @return string
     */
    public function getServerFile(): string
    {
        return $this->server_file;
    }

    /**
     * @param string $server_file
     */
    public function setServerFile(string $server_file): void
    {
        $this->server_file = $server_file;
    }
}
