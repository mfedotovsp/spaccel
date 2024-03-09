<?php


namespace app\modules\admin\models\form;

use app\models\CommunicationPatterns;
use Throwable;
use yii\base\Model;
use Yii;
use yii\db\StaleObjectException;

/**
 * Форма редактирования шаблона коммуникации
 * Class FormUpdateCommunicationPattern
 * @package app\modules\admin\models\form
 *
 * @property int $id
 * @property int $communication_type
 * @property string $description
 * @property int $project_access_period
 */
class FormUpdateCommunicationPattern extends Model
{

    public $id;
    public $communication_type;
    public $description;
    public $project_access_period;


    /**
     * FormUpdateCommunicationPattern constructor.
     *
     * @param int $id
     * @param int $communicationType
     * @param array $config
     */
    public function __construct(int $id, int $communicationType, array $config = [])
    {
        $pattern = CommunicationPatterns::find()
            ->andWhere(['id' => $id, 'communication_type' => $communicationType])
            ->andWhere(['initiator' => Yii::$app->user->getId(), 'is_remote' => CommunicationPatterns::NOT_REMOTE])
            ->one();

        $this->setParams($pattern);
        parent::__construct($config);
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['communication_type', 'project_access_period'], 'integer'],
            [['description', 'communication_type'], 'required'],
            [['description'], 'string', 'max' => 255],
            [['description'], 'trim'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'description' => 'Описание шаблона коммуникации',
            'project_access_period' => 'Срок доступа к проекту'
        ];
    }


    /**
     * Установка параметров формы
     *
     * @param CommunicationPatterns $pattern
     */
    public function setParams(CommunicationPatterns $pattern): void
    {
        $this->setId($pattern->getId());
        $this->setCommunicationType($pattern->getCommunicationType());
        $this->setDescription($pattern->getDescription());
        $this->setProjectAccessPeriod($pattern->getProjectAccessPeriod());
    }


    /**
     * Обновление шаблона
     * коммуникации
     *
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function update(): void
    {
        $pattern = CommunicationPatterns::findOne($this->getId());
        $pattern->setDescription($this->getDescription());
        $pattern->setProjectAccessPeriod($this->getProjectAccessPeriod());
        $pattern->update(true, ['description', 'project_access_period']);
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
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getCommunicationType(): int
    {
        return $this->communication_type;
    }

    /**
     * @param int $communication_type
     * @return void
     */
    public function setCommunicationType(int $communication_type): void
    {
        $this->communication_type = $communication_type;
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
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getProjectAccessPeriod(): int
    {
        return $this->project_access_period;
    }

    /**
     * @param int $project_access_period
     * @return void
     */
    public function setProjectAccessPeriod(int $project_access_period): void
    {
        $this->project_access_period = $project_access_period;
    }
}