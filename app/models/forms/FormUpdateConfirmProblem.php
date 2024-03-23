<?php


namespace app\models\forms;

use app\models\ConfirmProblem;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

/**
 * Форма обновления подтверждения гипотезы проблемы
 *
 * Class FormUpdateConfirmProblem
 * @package app\models\forms
 *
 * @property string $need_consumer                     Потребность потребителя
 */
class FormUpdateConfirmProblem extends FormUpdateConfirm
{

    public $need_consumer;

    /**
     * FormUpdateConfirmProblem constructor.
     * @param int $confirmId
     * @param array $config
     */
    public function __construct(int $confirmId, array $config = [])
    {
        $confirm = ConfirmProblem::findOne($confirmId);
        $this->setEditorCountRespond();

        $this->setParams([
            'id' => $confirmId,
            'count_respond' => $confirm->getCountRespond(),
            'count_positive' => $confirm->getCountPositive(),
            'need_consumer' => $confirm->getNeedConsumer(),
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
        $this->setNeedConsumer($params['need_consumer']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['need_consumer', 'trim'],
            [['need_consumer'], 'string', 'max' => 255],
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
            'count_positive' => 'Количество респондентов, соответствующих проблеме',
            'need_consumer' => 'Потребность потребителя',
        ];
    }


    /**
     * @return ConfirmProblem|false|null
     * @throws ErrorException
     * @throws NotFoundHttpException
     */
    public function update()
    {
        if ($this->validate()) {

            $confirm = ConfirmProblem::findOne($this->getId());
            $confirm->setCountRespond($this->getCountRespond());
            $confirm->setCountPositive($this->getCountPositive());
            $confirm->setNeedConsumer($this->getNeedConsumer());

            if ($confirm->save()){
                $this->getEditorCountRespond()->edit($confirm);
                return $confirm;
            }
            throw new NotFoundHttpException('Ошибка. Неудалось сохранить изменения');
        }
        return false;
    }

    /**
     * @return string
     */
    public function getNeedConsumer(): string
    {
        return $this->need_consumer;
    }

    /**
     * @param string $need_consumer
     */
    public function setNeedConsumer(string $need_consumer): void
    {
        $this->need_consumer = $need_consumer;
    }

}
