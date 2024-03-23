<?php


namespace app\models\forms;

use app\models\ConfirmMvp;
use app\models\Mvps;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

/**
 * Форма создания подтверждения гипотезы mvp-продукта
 *
 * Class FormCreateConfirmMvp
 * @package app\models\forms
 */
class FormCreateConfirmMvp extends FormCreateConfirm
{


    /**
     * FormCreateConfirmMvp constructor.
     * @param Mvps $hypothesis
     * @param array $config
     */
    public function __construct(Mvps $hypothesis, array $config = [])
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
     * @param Mvps $hypothesis
     * @return string
     */
    public static function getCachePath(Mvps $hypothesis): string
    {
        $gcp = $hypothesis->gcp;
        $problem = $hypothesis->problem;
        $segment = $hypothesis->segment;
        $project = $hypothesis->project;
        $user = $project->user;
        return '../runtime/cache/forms/user-'.$user->getId().'/projects/project-'.$project->getId().'/segments/segment-'.$segment->getId().
            '/problems/problem-'.$problem->getId().'/gcps/gcp-'.$gcp->getId().'/mvps/mvp-'.$hypothesis->getId().'/confirm/formCreateConfirm/';
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['hypothesis_id', 'count_positive'], 'required'],
            [['count_respond', 'add_count_respond', 'hypothesis_id'], 'integer'],
            [['count_respond', 'count_positive', 'add_count_respond'], 'integer', 'integerOnly' => TRUE, 'max' => 100],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'count_respond' => 'Количество респондентов, подтвердивших ценностное предложение',
            'count_positive' => 'Необходимое количество респондентов, подтверждающих продукт (MVP)',
        ];
    }


    /**
     * @return ConfirmMvp
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function create(): ConfirmMvp
    {
        $model = new ConfirmMvp();
        $model->setMvpId($this->getHypothesisId());
        $model->setCountRespond(array_sum([$this->getCountRespond(), $this->getAddCountRespond()]));
        $model->setCountPositive($this->getCountPositive());

        if ($model->save()) {
            //Создание респондентов для программы подтверждения MVP из респондентов подтвердивших ГЦП
            $this->getCreatorResponds()->create($model, $this);
            // Добавление новых респондентов для программы подтверждения MVP
            if ($this->getAddCountRespond()) {
                $this->getCreatorNewResponds()->create($model, $this);
            }
            //Удаление кэша формы создания подтверждения
            $this->getCacheManager()->deleteCache($this->getCachePathForm());

            return $model;
        }
        throw new NotFoundHttpException('Ошибка. Неудалось создать подтверждение продукта (MVP)');
    }

}
