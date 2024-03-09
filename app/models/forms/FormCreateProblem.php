<?php


namespace app\models\forms;

use app\models\ConfirmSegment;
use app\models\ExpectedResultsInterviewConfirmProblem;
use app\models\Problems;
use app\models\Segments;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yii\web\NotFoundHttpException;

/**
 * Форма создания гипотезы проблемы
 *
 * Class FormCreateProblem
 * @package app\models\forms
 *
 * @property ExpectedResultsInterviewConfirmProblem $_expectedResultsInterview              Вопросы для проверки и ответы на них (интервью с ожидаемыми результатами)
 * @property string $description                                                            Описание проблемы
 * @property int $indicator_positive_passage                                                Показатель прохождения теста
 * @property int $basic_confirm_id                                                          Идентификатор записи в таб. confirm_segment
 * @property CacheForm $_cacheManager                                                       Менеджер кэширования
 * @property string $cachePath                                                              Путь к файлу кэша
 * @property int|null $contractor_id                                                        Идентификатор исполнителя, создавшего ГПС (если null - ГПС создан проектантом)
 * @property int|null $task_id                                                              Идентификатор задания исполнителя, по которому создан ГПС (если null - ГПС создан проектантом)
 */
class FormCreateProblem extends Model
{

    public $_expectedResultsInterview;
    public $description;
    public $indicator_positive_passage;
    public $basic_confirm_id;
    public $contractor_id;
    public $task_id;
    public $_cacheManager;
    public $cachePath;


    /**
     * FormCreateProblem constructor.
     *
     * @param Segments $preliminaryHypothesis
     * @param array $config
     */
    public function __construct(Segments $preliminaryHypothesis, array $config = [])
    {
        if (!$preliminaryHypothesis->getDeletedAt()) {
            $this->setExpectedResultsInterview();
            $this->setCacheManager();
            $this->setCachePathForm(self::getCachePath($preliminaryHypothesis));
            $cacheName = 'formCreateHypothesisCache';
            if ($cache = $this->getCacheManager()->getCache($this->getCachePathForm(), $cacheName)) {
                $className = explode('\\', self::class)[3];
                foreach ($cache[$className] as $key => $value) {
                    $this[$key] = $value;
                }
            }
        }

        parent::__construct($config);
    }


    /**
     * Получить путь к кэшу формы
     * @param Segments $preliminaryHypothesis
     * @return string
     */
    public static function getCachePath(Segments $preliminaryHypothesis): string
    {
        return '../runtime/cache/forms/user-'.Yii::$app->user->getId().'/projects/project-'.$preliminaryHypothesis->project->getId().'/segments/segment-'.$preliminaryHypothesis->getId().'/problems/formCreate/';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['description'], 'trim'],
            [['description'], 'string', 'max' => 2000],
            [['basic_confirm_id', 'indicator_positive_passage'], 'integer'],
            [['contractor_id', 'task_id'], 'safe'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'description' => 'Описание гипотезы проблемы сегмента',
            'indicator_positive_passage' => 'Показатель прохождения теста',
        ];
    }


    /**
     * @return Problems
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function create(): Problems
    {
        /**
         * @var Problems $last_model
         */
        $last_model = Problems::find(false)->andWhere(['basic_confirm_id' => $this->getBasicConfirmId()])->orderBy(['id' => SORT_DESC])->one();
        $confirmSegment = ConfirmSegment::findOne($this->getBasicConfirmId());

        $problem = new Problems();
        $problem->setProjectId($confirmSegment->hypothesis->getProjectId());
        $problem->setSegmentId($confirmSegment->getSegmentId());
        $problem->setBasicConfirmId($this->getBasicConfirmId());
        $problem->setDescription($this->getDescription());
        $problem->setIndicatorPositivePassage($this->getIndicatorPositivePassage());
        $last_model_number = $last_model ? explode(' ',$last_model->getTitle())[1] : 0;
        $problem->setTitle('ГПС ' . ($last_model_number + 1));

        $className = explode('\\', self::class)[3];
        $expectedResults = $_POST[$className]['_expectedResultsInterview'];

        if ($this->getContractorId()) {
            $problem->setContractorId($this->getContractorId());
        }
        if ($this->getTaskId()) {
            $problem->setTaskId($this->getTaskId());
        }

        if ($problem->save()) {
            $this->saveExpectedResultsInterview($expectedResults, $problem->getId());
            $this->getCacheManager()->deleteCache($this->getCachePathForm()); // Удаление кэша формы создания
            return $problem;
        }
        throw new NotFoundHttpException('Ошибка. Не удалось сохранить новую проблему');
    }


    /**
     * @param $query
     * @param int $problemId
     */
    private function saveExpectedResultsInterview($query, int $problemId): void
    {
        foreach ($query as $k => $q) {
            $newExpectedResultsInterview[$k] = new ExpectedResultsInterviewConfirmProblem();
            $newExpectedResultsInterview[$k]->setQuestion($q['question']);
            $newExpectedResultsInterview[$k]->setAnswer($q['answer']);
            $newExpectedResultsInterview[$k]->setProblemId($problemId);
            $newExpectedResultsInterview[$k]->save();
        }
    }

    /**
     * @return ExpectedResultsInterviewConfirmProblem
     */
    public function getExpectedResultsInterview(): ExpectedResultsInterviewConfirmProblem
    {
        return $this->_expectedResultsInterview;
    }

    /**
     *
     */
    public function setExpectedResultsInterview(): void
    {
        $this->_expectedResultsInterview = new ExpectedResultsInterviewConfirmProblem();
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
    public function getIndicatorPositivePassage(): int
    {
        return $this->indicator_positive_passage;
    }

    /**
     * @param int $indicator_positive_passage
     */
    public function setIndicatorPositivePassage(int $indicator_positive_passage): void
    {
        $this->indicator_positive_passage = $indicator_positive_passage;
    }

    /**
     * @return int
     */
    public function getBasicConfirmId(): int
    {
        return $this->basic_confirm_id;
    }

    /**
     * @param int $basic_confirm_id
     */
    public function setBasicConfirmId(int $basic_confirm_id): void
    {
        $this->basic_confirm_id = $basic_confirm_id;
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

    /**
     * @return int|null
     */
    public function getContractorId(): ?int
    {
        return $this->contractor_id;
    }

    /**
     * @param int $contractor_id
     */
    public function setContractorId(int $contractor_id): void
    {
        $this->contractor_id = $contractor_id;
    }

    /**
     * @return int|null
     */
    public function getTaskId(): ?int
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

}