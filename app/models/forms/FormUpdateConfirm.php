<?php

namespace app\models\forms;

use app\models\EditorCountResponds;
use yii\base\Model;

/**
 * Форма обновление подтверждения гипотезы
 *
 * Class FormUpdateConfirm
 * @package app\models\forms
 *
 * @property int $id                                            Идентификатор записи
 * @property int $count_respond                                 Количество респондентов
 * @property int $count_positive                                Количество респондентов, которые подтверждают гипотезу
 * @property EditorCountResponds $_editorCountRespond           Редактор количества респондентов
 */
abstract class FormUpdateConfirm extends Model
{

    public $id;
    public $count_respond;
    public $count_positive;
    public $_editorCountRespond;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'min' => '1'],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'max' => '100'],
        ];
    }

    /**
     * @return mixed
     */
    abstract public function update ();

    /**
     * @param array $params
     * @return mixed
     */
    abstract protected function setParams(array $params);

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
     * @return int
     */
    public function getCountRespond(): int
    {
        return $this->count_respond;
    }

    /**
     * @param int $count_respond
     */
    public function setCountRespond(int $count_respond): void
    {
        $this->count_respond = $count_respond;
    }

    /**
     * @return int
     */
    public function getCountPositive(): int
    {
        return $this->count_positive;
    }

    /**
     * @param int $count_positive
     */
    public function setCountPositive(int $count_positive): void
    {
        $this->count_positive = $count_positive;
    }

    /**
     * @return EditorCountResponds
     */
    public function getEditorCountRespond(): EditorCountResponds
    {
        return $this->_editorCountRespond;
    }

    /**
     *
     */
    public function setEditorCountRespond(): void
    {
        $this->_editorCountRespond = new EditorCountResponds();
    }
}