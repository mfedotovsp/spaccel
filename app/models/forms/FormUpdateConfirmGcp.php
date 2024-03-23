<?php


namespace app\models\forms;

use app\models\ConfirmGcp;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

/**
 * Форма обновления аодтверждения гипотезы ценностного предложения
 *
 * Class FormUpdateConfirmGcp
 * @package app\models\forms
 */
class FormUpdateConfirmGcp extends FormUpdateConfirm
{


    /**
     * FormUpdateConfirmGcp constructor.
     * @param int $confirmId
     * @param array $config
     */
    public function __construct(int $confirmId, array $config = [])
    {
        $confirm = ConfirmGcp::findOne($confirmId);
        $this->setEditorCountRespond();

        $this->setParams([
            'id' => $confirmId,
            'count_respond' => $confirm->getCountRespond(),
            'count_positive' => $confirm->getCountPositive(),
        ]);

        parent::__construct($config);
    }


    /**
     * @param array $params
     * @return void
     */
    protected function setParams(array $params): void
    {
        $this->setId($params['id']);
        $this->setCountRespond($params['count_respond']);
        $this->setCountPositive($params['count_positive']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['count_respond', 'integer', 'integerOnly' => TRUE, 'min' => 0],
            ['count_positive', 'integer', 'integerOnly' => TRUE, 'min' => 1],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'max' => 100],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'count_respond' => 'Количество респондентов',
            'count_positive' => 'Количество респондентов, соответствующих ценностному предложению',
        ];
    }


    /**
     * @return ConfirmGcp|false|null
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function update()
    {
        if ($this->validate()) {

            $confirm = ConfirmGcp::findOne($this->getId());
            $confirm->setCountRespond($this->getCountRespond());
            $confirm->setCountPositive($this->getCountPositive());

            if ($confirm->save()) {
                $this->getEditorCountRespond()->edit($confirm);
                return $confirm;
            }
            throw new NotFoundHttpException('Ошибка. Неудалось сохранить изменения');
        }
        return false;
    }
}
