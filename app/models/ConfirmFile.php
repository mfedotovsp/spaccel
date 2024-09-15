<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс хранит информацию о загруженных файлах в описании подтверждения гипотезы
 *
 * Class ConfirmFile
 * @package app\models
 *
 * @property int $id                                Идентификатор записи
 * @property int $source_id                         Идентификатор источника информации
 * @property string $file_name                      Имя загруженного файла
 * @property string $server_file                    Сгенерированное имя файла на сервере
 *
 * @property ConfirmSource $source                  Источник информации
 */
class ConfirmFile extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'confirm_files';
    }


    /**
     * Получить объект источника информации
     * подтверждения гипотезы
     *
     * @return ActiveQuery
     */
    public function getSource(): ActiveQuery
    {
        return $this->hasOne(ConfirmSource::class, ['id' => 'source_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['source_id', 'file_name'], 'required'],
            [['source_id'], 'integer'],
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
            'source_id' => 'Source ID',
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
    public function getSourceId(): int
    {
        return $this->source_id;
    }

    /**
     * @param int $sourceId
     */
    public function setSourceId(int $sourceId): void
    {
        $this->source_id = $sourceId;
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
