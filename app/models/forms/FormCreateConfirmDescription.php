<?php

namespace app\models\forms;

use app\models\ConfirmDescription;
use app\models\ConfirmFile;
use app\models\ConfirmGcp;
use app\models\ConfirmMvp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ConfirmSource;
use app\models\Gcps;
use app\models\interfaces\ConfirmationInterface;
use app\models\Mvps;
use app\models\Problems;
use app\models\ProblemVariant;
use app\models\Segments;
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
 * @property CacheForm $_cacheManager                                               Менеджер кэширования
 * @property string $cachePath                                                      Путь к файлу кэша
 * @property $files                                                                 Поле для загрузки файлов
 * @property int $typeConfirm                                                       Тип подтверждения
 */
class FormCreateConfirmDescription extends Model
{
    public const CACHE_NAME = 'formCreateConfirmDCache';

    public $confirm;
    public $description;
    public $problemVariants;
    public $confirmSources;
    public $selectSources;
    public $files;
    public $_cacheManager;
    public $cachePath;
    public $typeConfirm;



    //TODO: Далее:
    // - Сделать гипотезу подтвержденной после сохранения формы и разрешить экспертизу - СДЕЛАЛ!
    // - После сохранения формы сделать редирект на следующий этап - СДЕЛАЛ!
    // - сделать заполнение модели из кэша в контструкторе - СДЕЛАЛ!
    // - добавить форму с вариантами проблем - СДЕЛАЛ!
    // - сделать страницу просмотра / редактирования подтверждения
    // - Вывести выявленные проблемы на этапе генерации проблем сегмента
    // - добавить везде логику с новыми условиями

    public function __construct(ConfirmationInterface $confirm, int $type, $config = [])
    {
        $this->confirm = $confirm;
        $this->description = new ConfirmDescription();

        $this->setCacheManager();
        $this->setCachePathForm(self::getCachePath($confirm->hypothesis));
        if ($cache = $this->getCacheManager()->getCache($this->getCachePathForm(), self::CACHE_NAME)) {
            $className = explode('\\', self::class)[3];
            foreach ($cache[$className] as $key => $value) {
                if ($key === 'description') {
                    $this->description = new ConfirmDescription();
                    foreach ($value as $index => $item) {
                        $this->description->$index = $item;
                    }
                }
                elseif ($key === 'confirmSources') {
                    foreach ($value as $k => $item) {
                        if (!empty($item['comment'])) {
                            $confirmSources = new ConfirmSource();
                            $confirmSources->setComment($item['comment']);
                            $confirmSources->setType((int)$item['type']);
                            $this->confirmSources[$k] = $confirmSources;
                        }
                    }
                }
                elseif ($key === 'problemVariants') {
                    foreach ($value as $k => $item) {
                        if (!empty($item['description'])) {
                            $problemVariants = new ProblemVariant();
                            $problemVariants->setDescription($item['description']);
                            $this->problemVariants[$k] = $problemVariants;
                        }
                    }
                }
                else {
                    $this[$key] = $value;
                }
            }
        }

        $this->typeConfirm = $type;
        $this->files = [];
        if (!$this->selectSources) {
            $this->selectSources = [];
        }
        if (!$this->confirmSources) {
            $this->confirmSources = [];
        }

        $this->confirm->setCountRespond(1);
        $this->confirm->setCountPositive(1);
        $this->confirm->setExistDesc(true);
        if ($type === StageExpertise::CONFIRM_SEGMENT) {
            $this->confirm->setGreetingInterview('-');
            $this->confirm->setViewInterview('-');
            $this->confirm->setReasonInterview('-');
        }

        parent::__construct($config);
    }

    /**
     * Получить путь к кэшу формы
     * @param Segments|Problems|Gcps|Mvps $hypothesis
     * @return string
     */
    public static function getCachePath($hypothesis): string
    {
        $project = $hypothesis->project;
        $user = $project->user;
        $path = '../runtime/cache/forms/user-'.$user->getId().'/projects/project-'.$project->getId();

        switch ($hypothesis->getNameOfClass()) {
            case Segments::class:
                $path .= '/segments/segment-'.$hypothesis->getId().'/confirm/formCreateConfirmD/';

                break;
            case Problems::class:
                $segment = $hypothesis->segment;
                $path .= '/segments/segment-'.$segment->getId().'/problems/problem-'.$hypothesis->getId().
                    '/confirm/formCreateConfirmD/';

                break;
            case Gcps::class:
                $problem = $hypothesis->problem;
                $segment = $hypothesis->segment;
                $path .= '/segments/segment-'.$segment->getId() .'/problems/problem-'.$problem->getId().
                    '/gcps/gcp-'.$hypothesis->getId().'/confirm/formCreateConfirmD/';

                break;
            case Mvps::class:
                $gcp = $hypothesis->gcp;
                $problem = $hypothesis->problem;
                $segment = $hypothesis->segment;
                $path .= '/segments/segment-'.$segment->getId(). '/problems/problem-'.$problem->getId().
                    '/gcps/gcp-'.$gcp->getId().'/mvps/mvp-'.$hypothesis->getId().'/confirm/formCreateConfirmD/';

                break;
            default:
                $path .= '/segments/segment-'.$hypothesis->getId().'/confirm/formCreateConfirmD/';
        }

        return $path;
    }

    /**
     * @return FormCreateConfirmDescription|null
     */
    public function create(): ?FormCreateConfirmDescription
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->confirm->save();
            $this->description->setHypothesisId($this->confirm->hypothesis->getId());
            $this->description->setType($this->typeConfirm);
            $this->description->setConfirmId($this->confirm->getId());
            $this->description->save();
            foreach ($this->confirmSources as $confirmSource) {
                $confirmSource->setDescriptionId($this->description->getId());
                $confirmSource->save();
                if (!empty($this->files[$confirmSource->getType()])) {
                    $this->uploadFiles($confirmSource->getId(), $this->files[$confirmSource->getType()]);
                }
            }
            if ($this->typeConfirm === StageExpertise::CONFIRM_SEGMENT) {
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
        $project = $this->confirm->hypothesis->project;
        $path = UPLOAD.'/user-'.$project->user->getId().'/project-'.$project->getId().'/confirm_files/type-'.
            $this->description->getType().'/source-'.$sourceId.'/';

        if (!is_dir($path)) {
            FileHelper::createDirectory($path);
        }

        if($this->validate()){

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
        if ($post = $params['FormCreateConfirmDescription']) {
            $this->description->setDescription($post['description']['description']);
            if (!empty($post['selectSources'])) {
                foreach ($post['selectSources'] as $selectSource) {
                    if (!empty($post['confirmSources'][$selectSource])) {
                        $this->confirmSources[] = (new ConfirmSource())
                            ->setType((int)$selectSource)
                            ->setComment($post['confirmSources'][$selectSource]['comment']);

                        $this->files[$selectSource] = UploadedFile::getInstances($this, 'files['.$selectSource.']');
                    }
                }
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

    /**
     * @return CacheForm
     */
    public function getCacheManager(): CacheForm
    {
        return $this->_cacheManager;
    }

    /**
     * @return void
     */
    public function setCacheManager(): void
    {
        $this->_cacheManager = new CacheForm();
    }

    /**
     * @return string
     */
    public function getCachePathForm(): string
    {
        return $this->cachePath;
    }

    /**
     * @param string $cachePath
     */
    public function setCachePathForm(string $cachePath): void
    {
        $this->cachePath = $cachePath;
    }
}
