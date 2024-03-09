<?php


namespace app\models\forms\expertise;


use app\models\Expertise;
use app\models\StageExpertise;
use app\models\TypesExpertAssessment;
use yii\base\Model;
use Yii;


/**
 * Форма создания экспертизы с выбором единственного ответа
 *
 * Class FormExpertiseSingleAnswer
 * @package app\models\forms\expertise
 *
 * @property $checkbox                      Оценка выбранная экспертом
 * @property $answerOptions                 Объект stdClass с вариантами ответов
 * @property string $comment                Комментарий к ответу в экспертизе
 * @property Expertise $_expertise          Объект экспертизы
 */
class FormExpertiseSingleAnswer extends Model
{

    /**
     * Принимает единственное значение
     * из предложенных вариантов ответа
     */
    public $checkbox;

    /**
     * Объект stdClass с вариантами ответов
     *
     * @var object
     */
    protected $answerOptions = array();

    /**
     * Комментарий к ответу в экспертизе
     *
     * @var string
     */
    public $comment;

    /**
     * Свойство, которое хранит в себе сведения о экспертизе
     *
     * @var Expertise
     */
    public $_expertise;

    /**
     * FormCreateExpertiseAssessmentConsumerSettings constructor.
     * @param Expertise $expertise
     * @param array $config
     */
    public function __construct(Expertise $expertise, array $config = [])
    {
        $this->setExpertise($expertise);
        $this->setAnswerOptions();
        $this->setCheckbox($expertise->getEstimation());
        $this->setComment($expertise->getComment());

        parent::__construct($config);
    }

    /**
     * Получить объект stdClass с вариантами ответов
     *
     * @return object
     */
    public function getAnswerOptions()
    {
        return $this->answerOptions;
    }

    /**
     * Установить массив с вариантами ответов
     */
    public function setAnswerOptions(): void
    {
        // Установить путь к файлу с вариантами ответов для формы
        $filename = StageExpertise::getList()[$this->_expertise->getStage()] . '.json';

        /** @var string $filePath */
        if (TypesExpertAssessment::getValue($this->getExpertise()->getTypeExpert()) === TypesExpertAssessment::ASSESSMENT_TECHNOLOGICAL_LEVEL) {
            $filePath = Yii::getAlias('@dirDataFormExpertise') . '/assessmentTechnologicalLevel/' . $filename;
        } elseif (TypesExpertAssessment::getValue($this->getExpertise()->getTypeExpert()) === TypesExpertAssessment::ASSESSMENT_CONSUMER_SETTINGS) {
            $filePath = Yii::getAlias('@dirDataFormExpertise') . '/assessmentConsumerSettings/' . $filename;
        }

        // Получить содержимое файла
        if ($filePath) {
            $file = file_get_contents($filePath);
            $this->answerOptions = json_decode($file, true);
        }
    }

    /**
     * @return Expertise
     */
    public function getExpertise(): Expertise
    {
        return $this->_expertise;
    }

    /**
     * @param Expertise $expertise
     * @return void
     */
    private function setExpertise(Expertise $expertise): void
    {
        $this->_expertise = $expertise;
    }

    /**
     * Получить комментарий
     *
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Установить комментарий
     *
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @param mixed $checkbox
     * @return void
     */
    public function setCheckbox($checkbox): void
    {
        $this->checkbox = $checkbox;
    }

    /**
     * @return mixed
     */
    public function getCheckbox()
    {
        return $this->checkbox;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['comment'], 'string', 'max' => 2000],
            [['comment'], 'trim'],
            [['checkbox', 'comment'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'checkbox' => 'Выберите один из вариантов ответа',
            'comment' => 'Напишите комментарий'
        ];
    }


    /**
     * Сохранение экспертизы, если передан парааметр
     * $completed, то экспертиза будет завершена и
     * будут отправлены коммуникации (уведомления)
     * трекеру и проектанту
     *
     * @param bool $completed
     * @return bool
     */
    public function saveRecord(bool $completed = false): bool
    {
        $this->getExpertise()->setEstimation($this->getCheckbox()[0]);
        $this->getExpertise()->setComment($this->getComment());
        if ($completed) {
            $this->getExpertise()->setCompleted();
        }
        return $this->getExpertise()->saveRecord();
    }

}