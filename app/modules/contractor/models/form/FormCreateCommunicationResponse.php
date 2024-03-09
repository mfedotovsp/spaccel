<?php


namespace app\modules\contractor\models\form;


use app\models\ContractorCommunicationResponse;
use yii\base\Model;
use yii\web\NotFoundHttpException;


/**
 * Класс формы создания ответа на коммуникацию
 *
 * FormCreateCommunicationResponse
 * @package app\modules\expert\models\form
 *
 * @property int $communication_id                                      Идентификатор коммуникации, к которой будет присоединен ответ
 * @property int $answer                                                Ответ на предложение присоединиться к работе над проектом
 * @property string $comment                                            Комментарий к ответу
 * @property ContractorCommunicationResponse $_model                    Объект ответа, который будет сохранен
 */
class FormCreateCommunicationResponse extends Model
{

    public $communication_id;
    public $answer;
    public $comment;
    public $_model;


    /**
     * FormCreateCommunicationResponse constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->_model = new ContractorCommunicationResponse();
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['answer', 'communication_id'], 'integer'],
            [['answer', 'communication_id'], 'required'],
            [['comment'], 'string', 'max' => 255],
            ['answer', 'in', 'range' => [
                ContractorCommunicationResponse::POSITIVE_RESPONSE,
                ContractorCommunicationResponse::NEGATIVE_RESPONSE
            ]],
        ];
    }


    public function attributeLabels(): array
    {
        return [
            'answer' => 'Дайте ответ на вопрос',
            'comment' => 'Напишите комментарий'
        ];
    }


    /**
     * Получить массив
     * значений для ответа
     *
     * @return array
     */
    public static function getAnswers(): array
    {
        return [
            ContractorCommunicationResponse::POSITIVE_RESPONSE => 'Готов(-а) приступить к работе над проектом',
            ContractorCommunicationResponse::NEGATIVE_RESPONSE => 'Не готов(-а) приступить к работе над проектом'
        ];
    }


    /**
     * Установить параметр
     * communication_id
     *
     * @param int $id
     */
    public function setCommunicationId(int $id): void
    {
        $this->communication_id = $id;
    }


    /**
     * Сохранение формы
     *
     * @return bool
     * @throws NotFoundHttpException
     */
    public function create(): bool
    {
        $this->_model->setParams($this->answer, $this->comment, $this->communication_id);
        if ($this->_model->save()) {
            return true;
        }
        throw new NotFoundHttpException('Неудалось сохранить ответ');
    }
}