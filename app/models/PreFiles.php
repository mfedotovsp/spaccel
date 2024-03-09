<?php

namespace app\models;

use app\models\traits\SoftDeleteModelTrait;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс хранит информацию о загруженных презентационных файлах на этапе создания проекта
 *
 * Class PreFiles
 * @package app\models
 *
 * @property int $id                                Идентификатор записи
 * @property int $project_id                        Идентификатор проекта
 * @property string $file_name                      Имя загруженного файла
 * @property string $server_file                    Сгенерированное имя файла на сервере
 * @property int|null $deleted_at                   Дата удаления
 *
 * @property Projects $project                      Проект
 */
class PreFiles extends ActiveRecord
{
    use SoftDeleteModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'pre_files';
    }


    /**
     * Получить объект проекта
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['project_id', 'file_name'], 'required'],
            [['project_id'], 'integer'],
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
            'project_id' => 'Project ID',
            'file_name' => 'File Name',
        ];
    }


    public function init()
    {

        $this->on(self::EVENT_AFTER_DELETE, function (){
            $this->project->touch('updated_at');
            $this->project->user->touch('updated_at');
        });

        parent::init();
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
    public function getProjectId(): int
    {
        return $this->project_id;
    }

    /**
     * @param int $project_id
     */
    public function setProjectId(int $project_id): void
    {
        $this->project_id = $project_id;
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

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
    }
}
