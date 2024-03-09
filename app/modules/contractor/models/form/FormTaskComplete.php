<?php

namespace app\modules\contractor\models\form;

use app\models\ContractorTaskFiles;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Форма завершения задания
 *
 * @property string $comment
 * @property $files
 * @property int $taskId
 */
class FormTaskComplete extends Model
{
    public $comment;
    public $files;
    public $taskId;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['comment'], 'required'],
            ['taskId', 'integer'],
            ['comment', 'string', 'max' => 2000],
            [['files'], 'file', 'extensions' => 'png, jpg, odt, xlsx, txt, doc, docx, pdf, otf, odp, pps, ppsx, ppt, pptx, opf, csv, xls', 'maxFiles' => 10],
        ];
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return int
     */
    public function getTaskId(): int
    {
        return $this->taskId;
    }

    /**
     * @param int $taskId
     */
    public function setTaskId(int $taskId): void
    {
        $this->taskId = $taskId;
    }

    /**
     * Загрузка файлов
     *
     * @return void
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function uploadPresentFiles(): void
    {
        $this->files = UploadedFile::getInstances($this, 'files');

        if ($this->files && $this->getTaskId()) {
            $path = UPLOAD.'/user-'.Yii::$app->user->getId().'/tasks/task-'.$this->getTaskId().'/files/';
            if (!is_dir($path)) {
                FileHelper::createDirectory($path);
            }

            if($this->validate()){

                foreach($this->files as $file){

                    $filename = Yii::$app->getSecurity()->generateRandomString(15);

                    try{

                        $file->saveAs($path . $filename . '.' . $file->extension);

                        $preFiles = new ContractorTaskFiles();
                        $preFiles->setFileName($file);
                        $preFiles->setServerFile($filename . '.' . $file->extension);
                        $preFiles->setTaskId($this->getTaskId());
                        $preFiles->save(false);

                    }catch (\Exception $e){

                        throw new NotFoundHttpException('Невозможно загрузить файл!');
                    }
                }
            }
        }
    }
}