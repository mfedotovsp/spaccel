<?php

namespace app\models;

use app\models\forms\CacheForm;
use app\models\traits\SoftDeleteModelTrait;
use Exception;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Класс хранит информацию в бд о проведении интервью с респондентом
 * на этапе подтверждения гипотезы сегмента
 *
 * Class InterviewConfirmSegment
 * @package app\models
 *
 * @property int $id                                    Идентификатор записи
 * @property int $respond_id                            Идентификатор респондента из таб. responds_segment
 * @property string $interview_file                     Имя файла, с которым он был загружен
 * @property string $server_file                        Сгенерированное имя прикрепленного файла на сервере
 * @property int $status                                Значимость гипотезы для респондента
 * @property int $created_at                            Дата создания
 * @property int $updated_at                            Дата редактирования
 * @property $loadFile                                  Поле для загрузки файла
 * @property CacheForm $_cacheManager                   Менеджер кэширования
 * @property string $result                             Варианты проблем
 * @property int|null $deleted_at                       Дата удаления
 *
 * @property RespondsSegment $respond                   Респондент
 */
class InterviewConfirmSegment extends ActiveRecord
{
    use SoftDeleteModelTrait;

    public $loadFile;
    public $_cacheManager;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'interview_confirm_segment';
    }

    /**
     * InterviewConfirmSegment constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setCacheManager();
        parent::__construct($config);
    }

    /**
     * Получить объект респондента
     *
     * @return ActiveQuery
     */
    public function getRespond(): ActiveQuery
    {
        return $this->hasOne(RespondsSegment::class, ['id' => 'respond_id']);
    }

    /**
     * @return string
     */
    public function getPathFile(): string
    {
        $respond = $this->respond;
        $confirm = $respond->confirm;
        $segment = $confirm->segment;
        $project = $segment->project;
        $user = $project->user;
        return UPLOAD.'/user-'.$user->getId().'/project-'.$project->getId().'/segments/segment-'.$segment->getId()
            .'/interviews/respond-'.$respond->getId().'/';
    }

    /**
     * @param RespondsSegment $respond
     * @return string
     */
    public static function getCachePath(RespondsSegment $respond): string
    {
        $confirm = $respond->confirm;
        $segment = $confirm->segment;
        $project = $segment->project;
        $user = $project->user;
        return '../runtime/cache/forms/user-'.$user->getId(). '/projects/project-'.$project->getId().
            '/segments/segment-'.$segment->getId().'/confirm/interviews/respond-'.$respond->getId().'/';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['respond_id', 'result', 'status'], 'required'],
            [['respond_id', 'status'], 'integer'],
            [['interview_file', 'server_file'], 'string', 'max' => 255],
            [['result'], 'string', 'max' => 2000],
            [['loadFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, odt, txt, doc, docx, pdf, xlsx, otf, odp, pps, ppsx, ppt, pptx, opf, csv, xls',],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'interview_file' => 'Файл',
            'result' => 'Варианты проблем',
            'status' => 'Данный респондент является представителем сегмента?',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class
        ];
    }

    public function init()
    {

        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->respond->confirm->segment->project->touch('updated_at');
            $this->respond->confirm->segment->project->user->touch('updated_at');
        });

        $this->on(self::EVENT_AFTER_UPDATE, function (){
            $this->respond->confirm->segment->project->touch('updated_at');
            $this->respond->confirm->segment->project->user->touch('updated_at');
        });

        parent::init();
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function create(): bool
    {
        if ($this->validate() && $this->save()) {

            $this->setLoadFile(UploadedFile::getInstance($this, 'loadFile'));

            if ($this->getLoadFile() && $this->uploadFileInterview()) {
                $this->setInterviewFile($this->getLoadFile());
                $this->save(false);
            }

            return true;
        }
        throw new NotFoundHttpException('Ошибка. Не удалось сохранить интервью');
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function updateInterview(): bool
    {
        if ($this->validate() && $this->save()) {

            $this->setLoadFile(UploadedFile::getInstance($this, 'loadFile'));

            if ($this->getLoadFile() && $this->uploadFileInterview()) {
                $this->setInterviewFile($this->getLoadFile());
                $this->save(false);
            }

            return true;
        }
        throw new NotFoundHttpException('Ошибка. Не удалось обновить данные интервью');
    }

    /**
     * @return bool
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    private function uploadFileInterview(): bool
    {
        $path = $this->getPathFile();
        if (!is_dir($path)) {
            FileHelper::createDirectory($path);
        }

        if ($this->validate()) {

            $filename=Yii::$app->getSecurity()->generateRandomString(15);
            try{

                $this->getLoadFile()->saveAs($path . $filename . '.' . $this->getLoadFile()->extension);
                $this->setServerFile($filename . '.' . $this->getLoadFile()->extension);

            }catch (Exception $e){

                throw new NotFoundHttpException('Невозможно загрузить файл!');
            }

            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setRespondId(int $id): void
    {
        $this->respond_id = $id;
    }

    /**
     * @return int
     */
    public function getRespondId(): int
    {
        return $this->respond_id;
    }

    /**
     * @return string|null
     */
    public function getInterviewFile(): ?string
    {
        return $this->interview_file;
    }

    /**
     * @param string $interview_file
     */
    public function setInterviewFile(string $interview_file): void
    {
        $this->interview_file = $interview_file;
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
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    /**
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    /**
     * @return mixed
     */
    public function getLoadFile()
    {
        return $this->loadFile;
    }

    /**
     * @param mixed $loadFile
     */
    public function setLoadFile($loadFile): void
    {
        $this->loadFile = $loadFile;
    }

    /**
     * @return CacheForm
     */
    public function getCacheManager(): CacheForm
    {
        return $this->_cacheManager;
    }

    /**
     *
     */
    public function setCacheManager(): void
    {
        $this->_cacheManager = new CacheForm();
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     */
    public function setResult(string $result): void
    {
        $this->result = $result;
    }

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
    }

    /**
     * @param int $deleted_at
     */
    public function setDeletedAt(int $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }
}
