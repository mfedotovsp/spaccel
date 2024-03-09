<?php


namespace app\models\forms;

use app\models\ConfirmGcp;
use app\models\Gcps;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

/**
 * Форма создания подтверждения гипотезы ценностного предложения
 *
 * Class FormCreateConfirmGcp
 * @package app\models\forms
 */
class FormCreateConfirmGcp extends FormCreateConfirm
{

    /**
     * FormCreateConfirmGcp constructor.
     * @param Gcps $hypothesis
     * @param array $config
     */
    public function __construct(Gcps $hypothesis, array $config = [])
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
     * @param Gcps $hypothesis
     * @return string
     */
    public static function getCachePath(Gcps $hypothesis): string
    {
        $problem = $hypothesis->problem;
        $segment = $hypothesis->segment;
        $project = $hypothesis->project;
        $user = $project->user;
        return '../runtime/cache/forms/user-'.$user->getId().'/projects/project-'.$project->getId(). '/segments/segment-'.$segment->getId()
            .'/problems/problem-'.$problem->getId().'/gcps/gcp-'.$hypothesis->getId().'/confirm/formCreateConfirm/';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['hypothesis_id', 'count_positive'], 'required'],
            ['count_respond', 'required', 'when' => function($model) {
                return $model->add_count_respond === 0;
            }],
            ['add_count_respond', 'required', 'when' => function($model) {
                return $model->count_respond === 0;
            }],
            [['hypothesis_id'], 'integer'],
            [['count_respond', 'count_positive', 'add_count_respond'], 'integer', 'integerOnly' => TRUE, 'max' => '100'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'count_respond' => 'Количество респондентов, подтвердивших проблему',
            'add_count_respond' => 'Добавить новых респондентов',
            'count_positive' => 'Необходимое количество респондентов, подтверждающих ценностное предложение',
        ];
    }


    /**
     * @return ConfirmGcp
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function create(): ConfirmGcp
    {
        $model = new ConfirmGcp();
        $model->setGcpId($this->getHypothesisId());
        $model->setCountRespond(array_sum([$this->getCountRespond(), $this->getAddCountRespond()]));
        $model->setCountPositive($this->getCountPositive());

        if ($model->save()) {
            //Создание респондентов для программы подтверждения ГЦП из респондентов подтвердивших проблему
            $this->getCreatorResponds()->create($model, $this);
            // Добавление новых респондентов для программы подтверждения ГЦП
            if ($this->getAddCountRespond()) {
                $this->getCreatorNewResponds()->create($model, $this);
            }
            //Удаление кэша формы создания подтверждения
            $this->getCacheManager()->deleteCache($this->getCachePathForm());

            return $model;
        }
        throw new NotFoundHttpException('Ошибка. Неудалось создать подтверждение ценностного предложения');
    }
}