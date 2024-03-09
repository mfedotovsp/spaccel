<?php


namespace app\models\forms;

use app\models\ConfirmProblem;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use app\models\Projects;
use app\models\Segments;
use app\models\Problems;
use app\models\Gcps;
use yii\web\NotFoundHttpException;

/**
 * Форма создания гипотезы ценностного предложения
 *
 * Class FormCreateGcp
 * @package app\models\forms
 *
 * @property string $good                           Формулировка перспективного продукта (товара / услуги)
 * @property string $benefit                        Какую выгоду дает использование данного продукта потребителю (представителю сегмента)
 * @property string $contrast                       По сравнению с каким продуктом заявлена выгода (с чем сравнивается)
 * @property int $basic_confirm_id                  Идентификатор записи в таб. confirm_problem
 * @property CacheForm $_cacheManager               Менеджер кэширования
 * @property string $cachePath                      Путь к файлу кэша
 * @property int|null $contractor_id                Идентификатор исполнителя, создавшего ГЦП (если null - ГЦП создано проектантом)
 * @property int|null $task_id                      Идентификатор задания исполнителя, по которому создано ГЦП (если null - ГЦП создано проектантом)
 */
class FormCreateGcp extends Model
{

    public $good;
    public $benefit;
    public $contrast;
    public $basic_confirm_id;
    public $contractor_id;
    public $task_id;
    public $_cacheManager;
    public $cachePath;


    /**
     * FormCreateGcp constructor.
     * @param Problems $preliminaryHypothesis
     * @param array $config
     */
    public function __construct(Problems $preliminaryHypothesis, array $config = [])
    {
        $this->setCacheManager();
        $this->setCachePathForm(self::getCachePath($preliminaryHypothesis));
        $cacheName = 'formCreateHypothesisCache';
        if ($cache = $this->getCacheManager()->getCache($this->getCachePathForm(), $cacheName)) {
            $className = explode('\\', self::class)[3];
            foreach ($cache[$className] as $key => $value) {
                $this[$key] = $value;
            }
        }

        parent::__construct($config);
    }


    /**
     * @param Problems $preliminaryHypothesis
     * @return string
     */
    public static function getCachePath(Problems $preliminaryHypothesis): string
    {
        $segment = $preliminaryHypothesis->segment;
        $project = $preliminaryHypothesis->project;
        return '../runtime/cache/forms/user-'.Yii::$app->user->getId().'/projects/project-'.$project->getId(). '/segments/segment-'.$segment->getId()
            .'/problems/problem-'.$preliminaryHypothesis->getId().'/gcps/formCreate/';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['good', 'benefit', 'contrast'], 'trim'],
            [['good', 'contrast'], 'string', 'max' => 255],
            [['benefit'], 'string', 'max' => 500],
            [['basic_confirm_id'], 'integer'],
            [['contractor_id', 'task_id'], 'safe'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'good' => 'Формулировка перспективного продукта',
            'benefit' => 'Какую выгоду дает использование данного продукта потребителю',
            'contrast' => 'По сравнению с каким продуктом заявлена выгода (с чем сравнивается)',
        ];
    }


    /**
     * @return Gcps
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function create(): Gcps
    {
        /** @var Gcps $last_model */
        $last_model = Gcps::find(false)->andWhere(['basic_confirm_id' => $this->getBasicConfirmId()])->orderBy(['id' => SORT_DESC])->one();
        $confirmProblem = ConfirmProblem::findOne($this->getBasicConfirmId());
        $problem = Problems::findOne($confirmProblem->getProblemId());
        $segment = Segments::findOne($problem->getSegmentId());
        $project = Projects::findOne($problem->getProjectId());

        $gcp = new Gcps();
        $gcp->setProjectId($project->getId());
        $gcp->setSegmentId($segment->getId());
        $gcp->setProblemId($problem->getId());
        $gcp->setBasicConfirmId($this->getBasicConfirmId());
        $last_model_number = $last_model ? explode(' ',$last_model->getTitle())[1] : 0;
        $gcp->setTitle('ГЦП ' . ($last_model_number + 1));

        $gcp->description = 'Наш продукт ' . mb_strtolower($this->getGood()) . ' ';
        $gcp->description .= 'помогает ' . mb_strtolower($segment->getName()) . ', ';
        $gcp->description .= 'который хочет удовлетворить проблему ' . mb_strtolower($problem->getDescription()) . ', ';
        $gcp->description .= 'избавиться от проблемы(или снизить её) и позволяет получить выгоду в виде, ' . mb_strtolower($this->getBenefit()) . ', ';
        $gcp->description .= 'в отличии от ' . mb_strtolower($this->getContrast()) . '.';

        if ($this->getContractorId()) {
            $gcp->setContractorId($this->getContractorId());
        }
        if ($this->getTaskId()) {
            $gcp->setTaskId($this->getTaskId());
        }

        if ($gcp->save()){
            $this->getCacheManager()->deleteCache($this->getCachePathForm()); // Удаление кэша формы создания
            return $gcp;
        }
        throw new NotFoundHttpException('Ошибка. Не удалось сохранить новое ценностное предложение');
    }

    /**
     * @return string
     */
    public function getGood(): string
    {
        return $this->good;
    }

    /**
     * @param string $good
     */
    public function setGood(string $good): void
    {
        $this->good = $good;
    }

    /**
     * @return string
     */
    public function getBenefit(): string
    {
        return $this->benefit;
    }

    /**
     * @param string $benefit
     */
    public function setBenefit(string $benefit): void
    {
        $this->benefit = $benefit;
    }

    /**
     * @return string
     */
    public function getContrast(): string
    {
        return $this->contrast;
    }

    /**
     * @param string $contrast
     */
    public function setContrast(string $contrast): void
    {
        $this->contrast = $contrast;
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