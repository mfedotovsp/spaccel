<?php

namespace app\models\forms;

use yii\base\Model;

/**
 * Форма указания кода доступа при регистрации
 *
 * Class FormClientCodeRegistration
 * @package app\models\forms
 *
 * @property string $code
 */
class FormClientCodeRegistration extends Model
{
    public $code = '';

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['code', 'string', 'max' => 32]
        ];
    }

}