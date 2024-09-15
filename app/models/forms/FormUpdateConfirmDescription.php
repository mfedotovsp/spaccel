<?php

namespace app\models\forms;

use app\models\ConfirmDescription;
use app\models\ConfirmFile;
use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ConfirmSource;
use app\models\interfaces\ConfirmationInterface;
use app\models\ProblemVariant;
use app\models\StageExpertise;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Форма создания описания подтверждения для учебного варианта
 *
 * Class FormCreateConfirmDescription
 * @package app\models\forms
 *
 * @property ConfirmSegment|ConfirmProblem|ConfirmGcp|ConfirmMvp $confirm           Подтверждение гипотезы
 * @property ConfirmDescription $description                                        Описание подтверждения
 * @property ProblemVariant[] $problemVariants                                      Выявленные проблемы
 * @property ConfirmSource[] $confirmSources                                        Источники информации
 * @property array $selectSources                                                   Выбранные источники
 * @property $files                                                                 Поле для загрузки файлов
 * @property $oldFiles                                                              Файлы, которые были загружены ранее
 * @property int $typeConfirm                                                       Тип подтверждения
 */
class FormUpdateConfirmDescription extends Model
{
    public $confirm;
    public $description;
    public $problemVariants;
    public $confirmSources;
    public $selectSources;
    public $files;
    public $oldFiles;
    public $typeConfirm;

    public function __construct(ConfirmationInterface $confirm, int $type, $config = [])
    {
        $this->confirm = $confirm;
        $this->description = $confirm->confirmDescription;
        $this->typeConfirm = $type;
        $this->files = [];
        foreach ($this->description->confirmSources as $confirmSource) {
            $this->selectSources[] = $confirmSource->type;
            $this->confirmSources[$confirmSource->type] = $confirmSource;
            $this->oldFiles[$confirmSource->type] = $confirmSource->files;
        }

        if ($type === StageExpertise::CONFIRM_SEGMENT) {
            $this->problemVariants = $this->confirm->problemVariants;
        }

        parent::__construct($config);
    }

    /**
     * @return FormCreateConfirmDescription|null
     */
    public function update(): ?FormUpdateConfirmDescription
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->description->save();
            foreach ($this->description->confirmSources as $oldSource) {
                if (!in_array($oldSource->getType(), $this->selectSources, false)) {
                    FileHelper::removeDirectory($this->getPathFiles($oldSource->getId()));
                    ConfirmFile::deleteAll(['source_id' => $oldSource->getId()]);
                    $oldSource->delete();
                }
            }

            foreach ($this->confirmSources as $confirmSource) {
                $confirmSource->setDescriptionId($this->description->getId());
                $confirmSource->save();
                if (!empty($this->files[$confirmSource->getType()])) {
                    $this->uploadFiles($confirmSource->getId(), $this->files[$confirmSource->getType()]);
                }
            }
            if ($this->typeConfirm === StageExpertise::CONFIRM_SEGMENT) {
                foreach ($this->description->problemVariants as $oldVariants) {
                    if (!in_array($oldVariants->getId(), array_column($this->problemVariants, 'id'), false)) {
                        $oldVariants->delete();
                    }
                }
                foreach ($this->problemVariants as $problemVariant) {
                    $problemVariant->setDescriptionId($this->description->getId());
                    $problemVariant->setConfirmId($this->confirm->getId());
                    $problemVariant->save();
                }
            }

            $transaction->commit();

            return $this;
        } catch (\Exception $exception) {
            $transaction->rollBack();
            return null;
        }
    }

    /**
     * @param int $sourceId
     * @return string
     */
    private function getPathFiles(int $sourceId): string
    {
        $project = $this->confirm->hypothesis->project;
        return UPLOAD.'/user-'.$project->user->getId().'/project-'.$project->getId().'/confirm_files/type-'.
            $this->description->getType().'/source-'.$sourceId.'/';
    }

    /**
     * Загрузка файлов
     *
     * @param int $sourceId
     * @param $files
     * @return void
     * @throws Exception
     * @throws NotFoundHttpException
     */
    private function uploadFiles(int $sourceId, $files): void
    {
        if (!is_dir($path = $this->getPathFiles($sourceId))) {
            FileHelper::createDirectory($path);
        }

        if($this->validate()) {

            foreach ($files as $file) {
                $filename = Yii::$app->getSecurity()->generateRandomString(15);

                try{

                    $file->saveAs($path . $filename . '.' . $file->extension);

                    $newFiles = new ConfirmFile();
                    $newFiles->setFileName($file);
                    $newFiles->setServerFile($filename . '.' . $file->extension);
                    $newFiles->setSourceId($sourceId);
                    $newFiles->save(false);

                } catch (\Exception $e){

                    throw new NotFoundHttpException('Невозможно загрузить файл!');
                }
            }
        }
    }

    /**
     * @param array $params
     * @return boolean
     */
    public function setParams(array $params): bool
    {
        if ($post = $params['FormUpdateConfirmDescription']) {
            $this->description->setDescription($post['description']['description']);
            if (!empty($post['selectSources'])) {
                $this->selectSources = $post['selectSources'];
                foreach ($this->selectSources as $selectSource) {
                    if (!empty($post['confirmSources'][$selectSource])) {
                        if (!empty($this->confirmSources[$selectSource])) {
                            $this->confirmSources[$selectSource]->setComment($post['confirmSources'][$selectSource]['comment']);

                        } else {
                            $this->confirmSources[] = (new ConfirmSource())
                                ->setType((int)$selectSource)
                                ->setComment($post['confirmSources'][$selectSource]['comment']);
                        }

                        $this->files[$selectSource] = UploadedFile::getInstances($this, 'files['.$selectSource.']');
                    }
                }

                foreach ($this->description->confirmSources as $oldSource) {
                    if (!in_array($oldSource->getType(), $this->selectSources, false)) {
                        unset($this->confirmSources[$oldSource->getType()], $this->files[$oldSource->getType()]);
                    }
                }
            }
            if (!empty($post['problemVariants'])) {
                $problemVariants = [];
                foreach ($post['problemVariants'] as $problemVariant) {
                    if (empty($problemVariant['id'])) {
                        $problemVariants[] = (new ProblemVariant())
                            ->setDescription($problemVariant['description']);
                    } else {
                        foreach ($this->problemVariants as $variant) {
                            if ($variant->getId() === (int)$problemVariant['id']) {
                                $variant->setDescription($problemVariant['description']);
                                $problemVariants[] = $variant;
                            }
                        }
                    }
                }
                $this->problemVariants = $problemVariants;
            }
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['description', 'descriptionValid'],
            ['confirmSources', 'confirmSourcesValid'],
            ['problemVariants', 'problemVariantsValid'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'selectSources' => 'Выберите источники информации',
        ];
    }

    public function descriptionValid(): void
    {
        if (trim($this->description->description) === '') {
            $this->addError('description', 'Добавьте имеющуюся информацию');
        }
    }

    public function confirmSourcesValid(): void
    {
        foreach ($this->confirmSources as $source) {
            if (empty(trim($source->getComment()))) {
                $this->addError(
                    'confirmSources',
                    'Добавьте комментарий для источника информации "' .
                    ConfirmSource::dataSelect()[$source->getType()] . '"'
                );
            }
        }
    }

    public function problemVariantsValid(): void
    {
        if ($this->typeConfirm === StageExpertise::CONFIRM_SEGMENT) {
            foreach ($this->problemVariants as $problemVariant) {
                if (empty(trim($problemVariant->getDescription()))) {
                    $this->addError(
                        'problemVariants',
                        'Заполните описание всех выявленных проблем'
                    );
                    break;
                }
            }
        }
    }

    /**
     * @return ConfirmationInterface
     */
    public function getConfirm(): ConfirmationInterface
    {
        return $this->confirm;
    }

    /**
     * @param ConfirmGcp|ConfirmMvp|ConfirmProblem|ConfirmSegment $confirm
     */
    public function setConfirm($confirm): void
    {
        $this->confirm = $confirm;
    }

    /**
     * @return ConfirmDescription
     */
    public function getDescription(): ConfirmDescription
    {
        return $this->description;
    }

    /**
     * @param ConfirmDescription $description
     */
    public function setDescription(ConfirmDescription $description): void
    {
        $this->description = $description;
    }

    /**
     * @return ProblemVariant[]
     */
    public function getProblemVariants(): array
    {
        return $this->problemVariants;
    }

    /**
     * @param ProblemVariant[] $problemVariants
     */
    public function setProblemVariants(array $problemVariants): void
    {
        $this->problemVariants = $problemVariants;
    }

    /**
     * @return ConfirmSource[]
     */
    public function getConfirmSources(): array
    {
        return $this->confirmSources;
    }

    /**
     * @param ConfirmSource[] $confirmSources
     */
    public function setConfirmSources(array $confirmSources): void
    {
        $this->confirmSources = $confirmSources;
    }

    /**
     * @return array
     */
    public function getSelectSources(): array
    {
        return $this->selectSources;
    }

    /**
     * @param array $selectSources
     */
    public function setSelectSources(array $selectSources): void
    {
        $this->selectSources = $selectSources;
    }
}
