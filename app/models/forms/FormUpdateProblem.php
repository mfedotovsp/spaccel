<?php


namespace app\models\forms;

use app\models\ExpectedResultsInterviewConfirmProblem;
use app\models\Problems;
use yii\base\Model;

/**
 * Форма обновления гипотезы проблемы
 *
 * Class FormUpdateProblem
 * @package app\models\forms
 *
 * @property int $id                                                                    Идентификатор из таб. problems
 * @property ExpectedResultsInterviewConfirmProblem[] $_expectedResultsInterview        Вопросы для проверки и ответы на них (интервью с ожидаемыми результатами)
 * @property string $description                                                        Описание проблемы
 * @property int $indicator_positive_passage                                            Показатель прохождения теста
 */
class FormUpdateProblem extends Model
{

    public $id;
    public $_expectedResultsInterview;
    public $description;
    public $indicator_positive_passage;


    /**
     * FormUpdateProblem constructor.
     * @param Problems $problem
     * @param array $config
     */
    public function __construct(Problems $problem, array $config = [])
    {
        $this->setId($problem->getId());
        $this->setDescription($problem->getDescription());
        $this->setIndicatorPositivePassage($problem->getIndicatorPositivePassage());
        $this->setExpectedResultsInterview(ExpectedResultsInterviewConfirmProblem::findAll(['problem_id' => $this->getId()]));

        parent::__construct($config);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['indicator_positive_passage'], 'integer'],
            [['description'], 'trim'],
            [['description'], 'string', 'max' => 2000],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'description' => 'Описание гипотезы проблемы сегмента',
            'indicator_positive_passage' => 'Показатель прохождения теста',
        ];
    }


    /**
     * Редактирование данных гипотезы проблемы
     * @return bool
     */
    public function update(): bool
    {
        $model = Problems::findOne($this->getId());
        $model->setDescription($this->getDescription());
        $model->setIndicatorPositivePassage($this->getIndicatorPositivePassage());

        $className = explode('\\', self::class)[3];
        $query = $_POST[$className]['_expectedResultsInterview'];

        if ($model->save()) {
            $this->saveExpectedResultsInterview($query, $model->id);
            return true;
        }
        return false;
    }


    /**
     * @param $query
     * @param int $problemId
     */
    private function saveExpectedResultsInterview ($query, int $problemId): void
    {
        $expectedResultsInterview = ExpectedResultsInterviewConfirmProblem::findAll(['problem_id' => $problemId]);

        if (empty($expectedResultsInterview)) {

            foreach ($query as $k => $q) {
                $newExpectedResultsInterview[$k] = new ExpectedResultsInterviewConfirmProblem();
                $newExpectedResultsInterview[$k]->setQuestion($q['question']);
                $newExpectedResultsInterview[$k]->setAnswer($q['answer']);
                $newExpectedResultsInterview[$k]->setProblemId($problemId);
                $newExpectedResultsInterview[$k]->save();
            }
        } else {

            $query = array_values($query);

            if (count($query) > count($expectedResultsInterview)) {

                foreach ($query as $i => $q) {

                    if (($i+1) <= count($expectedResultsInterview)) {
                        $expectedResultsInterview[$i]->setQuestion($q['question']);
                        $expectedResultsInterview[$i]->setAnswer($q['answer']);
                    } else {
                        $expectedResultsInterview[$i] = new ExpectedResultsInterviewConfirmProblem();
                        $expectedResultsInterview[$i]->setQuestion($q['question']);
                        $expectedResultsInterview[$i]->setAnswer($q['answer']);
                        $expectedResultsInterview[$i]->setProblemId($problemId);
                    }
                    $expectedResultsInterview[$i]->save();
                }

            } else {

                foreach ($query as $i => $q) {
                    $expectedResultsInterview[$i]->setQuestion($q['question']);
                    $expectedResultsInterview[$i]->setAnswer($q['answer']);
                    $expectedResultsInterview[$i]->save();
                }
            }
        }
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
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return ExpectedResultsInterviewConfirmProblem[]
     */
    public function getExpectedResultsInterview(): array
    {
        return $this->_expectedResultsInterview;
    }

    /**
     * @param ExpectedResultsInterviewConfirmProblem[] $expectedResultsInterview
     * @return void
     */
    public function setExpectedResultsInterview(array $expectedResultsInterview): void
    {
        $this->_expectedResultsInterview = $expectedResultsInterview;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getIndicatorPositivePassage(): int
    {
        return $this->indicator_positive_passage;
    }

    /**
     * @param int $indicator_positive_passage
     */
    public function setIndicatorPositivePassage(int $indicator_positive_passage): void
    {
        $this->indicator_positive_passage = $indicator_positive_passage;
    }
}