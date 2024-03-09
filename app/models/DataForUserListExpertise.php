<?php

namespace app\models;

use app\models\forms\expertise\FormExpertiseManyAnswer;
use app\models\forms\expertise\FormExpertiseSingleAnswer;
use yii\base\Model;

/**
 * Класс содержит данные вывода списка экспертиз для пользователей
 *
 * @property int $id                                                        Идентификатор экспертизы
 * @property int $updated_at                                                Дата обновления экспертизы
 * @property int $type                                                      Тип экспертной деятельности
 * @property string $username_expert                                        Логин эксперта
 * @property int $general_estimation_by_one                                 Общее количество баллов по всем вопросам по одной экспертизе
 * @property string $comment                                                Комментарий эксперта к экспертизе
 * @property FormExpertiseSingleAnswer|FormExpertiseManyAnswer $form        Форма создания экспертизы для показа данных
 */
class DataForUserListExpertise extends Model
{
    public $id;
    public $updated_at;
    public $type;
    public $username_expert;
    public $general_estimation_by_one;
    public $comment;
    public $form;


    /**
     * @param int $id
     * @param int $updated_at
     * @param int $type
     * @param string $username_expert
     * @param int $general_estimation_by_one
     * @param string $comment
     * @param FormExpertiseSingleAnswer|FormExpertiseManyAnswer $form
     * @param array $config
     */
    public function __construct(int $id, int $updated_at, int $type, string $username_expert, int $general_estimation_by_one, string $comment, $form, array $config = [])
    {
        $this->setId($id);
        $this->setUpdatedAt($updated_at);
        $this->setType($type);
        $this->setUsernameExpert($username_expert);
        $this->setGeneralEstimationByOne($general_estimation_by_one);
        $this->setComment($comment);
        $this->setForm($form);
        parent::__construct($config);
    }

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
    private function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    /**
     * @param int $updatedAt
     * @return void
     */
    private function setUpdatedAt(int $updatedAt): void
    {
        $this->updated_at = $updatedAt;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return void
     */
    private function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getUsernameExpert(): string
    {
        return $this->username_expert;
    }

    /**
     * @param string $usernameExpert
     * @return void
     */
    private function setUsernameExpert(string $usernameExpert): void
    {
        $this->username_expert = $usernameExpert;
    }

    /**
     * @return int
     */
    public function getGeneralEstimationByOne(): int
    {
        return $this->general_estimation_by_one;
    }

    /**
     * @param int $estimation
     * @return void
     */
    private function setGeneralEstimationByOne(int $estimation): void
    {
        $this->general_estimation_by_one = $estimation;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return void
     */
    private function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return FormExpertiseManyAnswer|FormExpertiseSingleAnswer
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param FormExpertiseSingleAnswer|FormExpertiseManyAnswer $form
     * @return void
     */
    private function setForm($form): void
    {
        $this->form = $form;
    }
}