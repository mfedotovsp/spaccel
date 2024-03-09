<?php

namespace app\modules\expert\models\form;

use app\models\MessageFiles;
use app\modules\expert\models\MessageExpert;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Yii;

/**
 * Форма создания сообщения между экспертом и другими пользователями
 *
 * Class FormCreateMessageExpert
 * @package app\modules\expert\models\form
 *
 * @property string $description                        Текст сообщения
 * @property int $conversation_id                       Идентификатор беседы
 * @property int $sender_id                             Идентификатор отправителя
 * @property int $adressee_id                           Идентификатор получателя
 *
 * @property $message_files                             Прикрепленные файлы
 * @property int $category                              Категория к которой относится беседа
 * @property int $message_id                            Идентификатор созданного сообщения
 * @property string $server_file                        Сгенерированное имя прикрепленного файла для хранения на сервере
 */
class FormCreateMessageExpert extends Model
{

    public $description;
    public $conversation_id;
    public $sender_id;
    public $adressee_id;

    public $message_files;
    public $category = MessageFiles::CATEGORY_EXPERT;
    public $message_id;
    public $server_file;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['description'], 'filter', 'filter' => 'trim'],
            [['description'], 'string', 'max' => 4000],
            [['server_file'], 'string', 'max' => 255],
            [['conversation_id','sender_id', 'adressee_id', 'message_id', 'category'], 'integer'],
            [['message_files'], 'file', 'extensions' => 'png, jpg, odt, xlsx, txt, doc, docx, pdf, otf, odp, pps, ppsx, ppt, pptx, opf, csv, xls', 'maxFiles' => 10],
        ];
    }

    /**
     * @return MessageExpert
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function create(): MessageExpert
    {
        $model = new MessageExpert();
        $model->setDescription($this->getDescription());
        $model->setConversationId($this->getConversationId());
        $model->setSenderId($this->getSenderId());
        $model->setAdresseeId($this->getAdresseeId());
        if ($model->save()) {

            //Загрузка презентационных файлов
            $this->setMessageId($model->getId());
            $this->setMessageFiles(UploadedFile::getInstances($this, 'message_files'));
            if ($this->getMessageFiles()) {
                $this->uploadMessageFiles();
            }

            return $model;
        }

        throw new NotFoundHttpException('Ошибка. Сообщение не отправлено');
    }

    /**
     * @return void
     * @throws NotFoundHttpException
     * @throws Exception
     */
    private function uploadMessageFiles(): void
    {
        $path = UPLOAD.'/user-'.$this->getSenderId().'/messages/category-'.$this->getCategory().'/message-'.$this->getMessageId().'/';
        if (!is_dir($path)) {
            FileHelper::createDirectory($path);
        }

        if($this->validate()){

            foreach($this->getMessageFiles() as $file){

                $filename = Yii::$app->getSecurity()->generateRandomString(15);

                try{

                    $file->saveAs($path . $filename . '.' . $file->extension);

                    $messageFile = new MessageFiles();
                    $messageFile->setFileName($file);
                    $messageFile->setServerFile($filename . '.' . $file->extension);
                    $messageFile->setMessageId($this->getMessageId());
                    $messageFile->setCategory($this->getCategory());
                    $messageFile->save(false);

                }catch (Exception $e){

                    throw new NotFoundHttpException('Невозможно загрузить файл!');
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getConversationId(): int
    {
        return $this->conversation_id;
    }

    /**
     * @param int $conversation_id
     */
    public function setConversationId(int $conversation_id): void
    {
        $this->conversation_id = $conversation_id;
    }

    /**
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->sender_id;
    }

    /**
     * @param int $sender_id
     */
    public function setSenderId(int $sender_id): void
    {
        $this->sender_id = $sender_id;
    }

    /**
     * @return int
     */
    public function getAdresseeId(): int
    {
        return $this->adressee_id;
    }

    /**
     * @param int $adressee_id
     */
    public function setAdresseeId(int $adressee_id): void
    {
        $this->adressee_id = $adressee_id;
    }

    /**
     * @return mixed
     */
    public function getMessageFiles()
    {
        return $this->message_files;
    }

    /**
     * @param mixed $message_files
     */
    public function setMessageFiles($message_files): void
    {
        $this->message_files = $message_files;
    }

    /**
     * @return int
     */
    public function getCategory(): int
    {
        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory(int $category): void
    {
        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getMessageId(): int
    {
        return $this->message_id;
    }

    /**
     * @param int $message_id
     */
    public function setMessageId(int $message_id): void
    {
        $this->message_id = $message_id;
    }
}