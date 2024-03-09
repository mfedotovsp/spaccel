<?php


namespace app\models\forms;

use yii\base\Model;

/**
 * Форма редактирования информации о респонденте
 *
 * Class UpdateFormRespond
 * @package app\models\forms
 *
 * @property int $id                                Идентификатор респондента
 * @property string $name                           ФИО респондента
 * @property string $info_respond                   Другая информация о респонденте
 * @property string $place_interview                Место проведения интервью
 * @property string $email                          Эл.почта респондента
 * @property $date_plan                             Плановая дата проведения интервью
 * @property int $confirm_id                        Идентификатор подтверждения гипотезы, к которому отновится респондент
 * @property int|null deleted_at                    Дата удаления
 * @property int|null $contractor_id                Идентификатор исполнителя, который опросил респондента (если null - опрос проводил проектант)
 * @property int|null $task_id                      Идентификатор задания исполнителя, по которому исполнитель опросил респондента (если null - опрос проводил проектант)
 */
abstract class UpdateFormRespond extends Model
{

    public $id;
    public $name;
    public $info_respond;
    public $place_interview;
    public $email;
    public $date_plan;
    public $confirm_id;
    public $deleted_at;
    public $contractor_id;
    public $task_id;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'info_respond', 'place_interview', 'date_plan'], 'required'],
            [['name', 'info_respond', 'place_interview', 'email'], 'trim'],
            [['date_plan'], 'safe'],
            [['name'], 'uniqueName'],
            [['name'], 'string', 'max' => 100],
            [['info_respond', 'place_interview', 'email'], 'string', 'max' => 255],
            ['email', 'email', 'message' => 'Неверный формат адреса электронной почты'],
            [['contractor_id', 'task_id'], 'safe'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Респондент',
            'info_respond' => 'Данные респондента',
            'email' => 'E-mail',
            'date_plan' => 'Плановая дата интервью',
            'place_interview' => 'Место проведения интервью',
        ];
    }


    abstract public function findConfirm();

    abstract public function update();

    abstract public function uniqueName($attr);

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getInfoRespond(): string
    {
        return $this->info_respond;
    }

    /**
     * @param string $info_respond
     */
    public function setInfoRespond(string $info_respond): void
    {
        $this->info_respond = $info_respond;
    }

    /**
     * @return string
     */
    public function getPlaceInterview(): string
    {
        return $this->place_interview;
    }

    /**
     * @param string $place_interview
     */
    public function setPlaceInterview(string $place_interview): void
    {
        $this->place_interview = $place_interview;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getDatePlan()
    {
        return $this->date_plan;
    }

    /**
     * @param mixed $date_plan
     */
    public function setDatePlan($date_plan): void
    {
        $this->date_plan = $date_plan;
    }

    /**
     * @return int
     */
    public function getConfirmId(): int
    {
        return $this->confirm_id;
    }

    /**
     * @param int $confirm_id
     */
    public function setConfirmId(int $confirm_id): void
    {
        $this->confirm_id = $confirm_id;
    }

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
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