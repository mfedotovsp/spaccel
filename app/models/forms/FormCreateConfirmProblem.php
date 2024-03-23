<?php


namespace app\models\forms;

use app\models\ConfirmProblem;
use app\models\Problems;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

/**
 * Форма создания подтверждения гипотезы проблемы сегмента
 *
 * Class FormCreateConfirmProblem
 * @package app\models\forms
 *
 * @property string $need_consumer          Потребность потребителя сегмента
 */
class FormCreateConfirmProblem extends FormCreateConfirm
{

    public $need_consumer;


    /**
     * FormCreateConfirmProblem constructor.
     * @param Problems $hypothesis
     * @param array $config
     */
    public function __construct(Problems $hypothesis, array $config = [])
    {
        $this->setCreatorResponds();
        $this->setCreatorNewResponds();
        $this->setCacheManager();
        $this->setCachePathForm(self::getCachePath($hypothesis));
        if ($cache = $this->getCacheManager()->getCache($this->getCachePathForm(), self::CACHE_NAME)) {
            $className = explode('\\', self::class)[3];
            foreach ($cache[$className] as $key => $value) {
                $this[$key] = $value;
            }
        }

        parent::__construct($config);
    }


    /**
     * Получить путь к кэшу формы
     * @param Problems $hypothesis
     * @return string
     */
    public static function getCachePath(Problems $hypothesis): string
    {
        $segment = $hypothesis->segment;
        $project = $hypothesis->project;
        $user = $project->user;
        return '../runtime/cache/forms/user-'.$user->getId().'/projects/project-'.$project->getId().
            '/segments/segment-'.$segment->getId().'/problems/problem-'.$hypothesis->getId().'/confirm/formCreateConfirm/';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['hypothesis_id', 'count_positive', 'need_consumer'], 'required'],
            [['count_respond', 'add_count_respond', 'hypothesis_id'], 'integer'],
            [['need_consumer'], 'trim'],
            [['need_consumer'], 'string', 'max' => 255],
            [['count_respond', 'count_positive', 'add_count_respond'], 'integer', 'integerOnly' => TRUE, 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'count_respond' => 'Количество респондентов',
            'add_count_respond' => 'Добавить новых респондентов',
            'count_positive' => 'Необходимое количество респондентов, подтверждающих проблему',
            'need_consumer' => 'Потребность потребителя',
        ];
    }


    /**
     * @return ConfirmProblem
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function create(): ConfirmProblem
    {
        $model = new ConfirmProblem();
        $model->setProblemId($this->getHypothesisId());
        $model->setNeedConsumer($this->getNeedConsumer());
        $model->setCountRespond(array_sum([$this->getCountRespond(), $this->getAddCountRespond()]));
        $model->setCountPositive($this->getCountPositive());

        if ($model->save()) {
            // Создание респондентов для программы подтверждения ГПС из представителей сегмента
            $this->getCreatorResponds()->create($model, $this);
            // Добавление новых респондентов для программы подтверждения ГПС
            if ($this->getAddCountRespond()) {
                $this->getCreatorNewResponds()->create($model, $this);
            }
            // Удаление кэша формы создания подтверждения
            $this->getCacheManager()->deleteCache($this->getCachePathForm());

            return $model;
        }
        throw new NotFoundHttpException('Ошибка. Неудалось создать подтверждение проблемы');
    }


    /**
     * @return string
     */
    public function getNeedConsumer(): string
    {
        return $this->need_consumer;
    }

}
