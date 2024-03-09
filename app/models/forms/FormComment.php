<?php

namespace app\models\forms;

use yii\base\Model;

/**
 * Форма изменения статуса задания для исполнитея проекта
 *
 * Class FormComment
 * @package app\models\forms
 *
 * @property string $comment
 */
class FormComment extends Model
{
    public $comment;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            ['comment', 'required'],
            ['comment', 'string', 'max' => 2000]
        ];
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }
}