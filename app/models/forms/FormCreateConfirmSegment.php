<?php


namespace app\models\forms;

use app\models\ConfirmSegment;
use app\models\Segments;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

/**
 * Форма создания подтверждения гипотезы сегмента
 *
 * Class FormCreateConfirmSegment
 * @package app\models\forms
 *
 * @property string $greeting_interview                 Приветствие в начале встречи
 * @property string $view_interview                     Информация о вас для респондентов
 * @property string $reason_interview                   Причина и тема (что побудило) для проведения исследования
 */
class FormCreateConfirmSegment extends FormCreateConfirm
{

    public $greeting_interview;
    public $view_interview;
    public $reason_interview;


    /**
     * FormCreateConfirmSegment constructor.
     * @param Segments $hypothesis
     * @param array $config
     */
    public function __construct(Segments $hypothesis, array $config = [])
    {
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
     * @param Segments $hypothesis
     * @return string
     */
    public static function getCachePath(Segments $hypothesis): string
    {
        $project = $hypothesis->project;
        $user = $project->user;
        return '../runtime/cache/forms/user-'.$user->getId().'/projects/project-'.$project->getId().'/segments/segment-'.$hypothesis->getId().'/confirm/formCreateConfirm/';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['hypothesis_id', 'count_respond', 'count_positive', 'greeting_interview', 'view_interview', 'reason_interview'], 'required'],
            [['hypothesis_id'], 'integer'],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'min' => '1'],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'max' => '100'],
            [['greeting_interview', 'view_interview', 'reason_interview'], 'string', 'max' => '2000'],
            [['greeting_interview', 'view_interview', 'reason_interview'], 'trim'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'count_respond' => 'Количество респондентов',
            'count_positive' => 'Количество респондентов, соответствующих сегменту',
            'greeting_interview' => 'Приветствие в начале встречи',
            'view_interview' => 'Информация о вас для респондентов',
            'reason_interview' => 'Причина и тема (что побудило) для проведения исследования',
        ];
    }


    /**
     * @return ConfirmSegment
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function create (): ConfirmSegment
    {
        $model = new ConfirmSegment();
        $model->setSegmentId($this->getHypothesisId());
        $model->setCountRespond($this->getCountRespond());
        $model->setCountPositive($this->getCountPositive());
        $model->setParams([
            'greeting_interview' => $this->getGreetingInterview(),
            'view_interview' => $this->getViewInterview(),
            'reason_interview' => $this->getReasonInterview()
        ]);

        if ($model->save()) {
            //Создание респондентов по заданному значению count_respond
            $this->getCreatorNewResponds()->create($model, $this);
            // Удаление кэша формы создания
            $this->getCacheManager()->deleteCache($this->cachePath);

            return $model;
        }
        throw new NotFoundHttpException('Ошибка. Неудалось создать подтверждение сегмента');
    }

    /**
     * @return string
     */
    public function getGreetingInterview(): string
    {
        return $this->greeting_interview;
    }

    /**
     * @return string
     */
    public function getViewInterview(): string
    {
        return $this->view_interview;
    }

    /**
     * @return string
     */
    public function getReasonInterview(): string
    {
        return $this->reason_interview;
    }

}