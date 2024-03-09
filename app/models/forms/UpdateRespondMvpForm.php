<?php


namespace app\models\forms;

use app\models\ConfirmMvp;
use app\models\ContractorTasks;
use app\models\interfaces\ConfirmationInterface;
use app\models\RespondsMvp;
use yii\web\NotFoundHttpException;

/**
 * Форма редактирования информации о респонденте
 * на этапе подтверждения mvp-продукта
 *
 * Class UpdateRespondMvpForm
 * @package app\models\forms
 *
 * @property int $id                                Идентификатор респондента
 * @property string $name                           ФИО респондента
 * @property string $info_respond                   Другая информация о респонденте
 * @property string $place_interview                Место проведения интервью
 * @property string $email                          Эл.почта респондента
 * @property $date_plan                             Плановая дата проведения интервью
 * @property int $confirm_id                        Идентификатор подтверждения гипотезы, к которому отновится респондент
 */
class UpdateRespondMvpForm extends UpdateFormRespond
{


    /**
     * UpdateRespondMvpForm constructor.
     * @param int $id
     * @param bool $isOnlyNotDelete
     * @param array $config
     */
    public function __construct(int $id, bool $isOnlyNotDelete = true, array $config = [])
    {
        /** @var $respond RespondsMvp */
        $respond = $isOnlyNotDelete ?
            RespondsMvp::findOne($id) :
            RespondsMvp::find(false)
                ->andWhere(['id' => $id])
                ->one();

        foreach ($respond as $key => $value) {
            $this[$key] = $value;
        }

        parent::__construct($config);
    }


    /**
     * Получить модель подтверждения
     * @param bool $isOnlyNotDelete
     * @return ConfirmMvp|null
     */
    public function findConfirm(bool $isOnlyNotDelete = true): ?ConfirmMvp
    {
        /** @var $confirm ConfirmMvp */
        $confirm = ConfirmMvp::find($isOnlyNotDelete)
            ->andWhere(['id' => $this->getConfirmId()])
            ->one();

        return $confirm ?: null;
    }


    /**
     * @param ContractorTasks|null $task
     * @return RespondsMvp|null
     * @throws NotFoundHttpException
     */
    public function update(ContractorTasks $task = null): ?RespondsMvp
    {
        $respond = RespondsMvp::findOne($this->getId());
        $respond->setName($this->getName());
        if ($task) {
            $respond->setTaskId($task->getId());
            $respond->setContractorId($task->getContractorId());
        }
        $respond->setParams([
            'info_respond' => $this->getInfoRespond(),
            'place_interview' => $this->getPlaceInterview(),
            'email' => $this->getEmail()
        ]);
        $respond->setDatePlan(strtotime($this->getDatePlan()));
        if ($respond->save()) {
            return $respond;
        }

        throw new NotFoundHttpException('Ошибка. Неудалось обновить данные респондента');
    }


    /**
     * @param $attr
     */
    public function uniqueName($attr)
    {
        $models = RespondsMvp::findAll([
            'confirm_id' => $this->getConfirmId(),
            'task_id' => $this->getTaskId(),
            'contractor_id' => $this->getContractorId()
        ]);

        foreach ($models as $item){

            if ($this->getId() !== $item->getId() && mb_strtolower(str_replace(' ', '', $this->getName())) === mb_strtolower(str_replace(' ', '',$item->getName()))){

                $this->addError($attr, 'Респондент с таким именем «'. $this->getName() .'» уже существует!');
            }
        }
    }

}