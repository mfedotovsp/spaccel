<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * Класс хранит информацию о том, по какому шаблону будет отправлена коммуникация эксперту от администратора
 *
 * Class CommunicationPatterns
 * @package app\models
 *
 * @property int $id                                                Идентификатор записи
 * @property int $communication_type                                Тип коммуникации
 * @property int $initiator                                         Идентификатор пользователя, который создал шаблон
 * @property int $is_active                                         Флаг активации шаблона коммуникации, указывает что будет применятся данный шаблон
 * @property string $description                                    Описание шаблона коммуникации
 * @property int $project_access_period                             Срок доступа к проекту
 * @property int $created_at                                        Дата создания шаблона
 * @property int $updated_at                                        Дата редактирования шаблона
 * @property int $is_remote                                         В бд хранятся все шаблоны, в тос числе и удаленные, поэтому было добавлено данное поле
 */
class CommunicationPatterns extends ActiveRecord
{

    public const ACTIVE = 123;
    public const NO_ACTIVE = 321;
    public const NOT_REMOTE = 0;
    public const REMOTE = 1;

    public const COMMUNICATION_DEFAULT_ABOUT_READINESS_CONDUCT_EXPERTISE = 'Вы готовы провести экспертизу по проекту {{наименование проекта, ссылка на проект}} ? Для предварительной оценки Вам открыт доступ к проекту на 14 дней.';
    public const DEFAULT_USER_ACCESS_TO_PROJECT = 14;
    public const COMMUNICATION_DEFAULT_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE = 'Произошли изменения в проекте {{наименование проекта}}. Приносим Вам свои извинения, запрос на экспертизу отозван.';
    public const COMMUNICATION_DEFAULT_APPOINTS_EXPERT_PROJECT = 'Вы назначены на экспертизу по проекту {{наименование проекта, ссылка на проект}} по типам деятельности: {{список типов деятельности эксперта}}. Приступайте к экспертизе на этапе описания проекта. Внимание! В работе эксперта есть ограничение по времени, не более 7 дней для выставления экспертной оценки после уведомления о необходимости провести экспертизу для той или иной сущности на этапе проекта.';
    public const COMMUNICATION_DEFAULT_DOES_NOT_APPOINTS_EXPERT_PROJECT = 'Вы не назначены на экспертизу по проекту {{наименование проекта}}. Приносим Вам свои извинения, запрос на экспертизу отозван.';
    public const COMMUNICATION_DEFAULT_WITHDRAWS_EXPERT_FROM_PROJECT = 'Вы отозваны с экспертизы по проекту {{наименование проекта}}. Подробную информацию получите у администратора сайта Spaccel.ru';
    public const COMMUNICATION_DEFAULT_USER_ALLOWED_STAGE_EXPERTISE = 'Проектант, {{проектант}}, разрешил экспертизу по этапу «{{наименование этапа проекта, ссылка на этап проекта}}».<br>Проект: {{наименование проекта}}.';
    public const COMMUNICATION_DEFAULT_USER_DELETED_STAGE_PROJECT = 'Проектант, {{проектант}}, удалил «{{наименование этапа проекта, ссылка на этап проекта}}».<br>Проект: {{наименование проекта}}.';


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'communication_patterns';
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['communication_type', 'initiator', 'is_active', 'project_access_period', 'created_at', 'updated_at', 'is_remote'], 'integer'],
            [['description', 'communication_type', 'initiator'], 'required'],
            [['description'], 'string', 'max' => 255],
            [['description'], 'trim'],
            ['communication_type', 'in', 'range' => CommunicationTypes::getListTypes()],
            ['is_active', 'default', 'value' => self::NO_ACTIVE],
            ['is_active', 'in', 'range' => [
                self::NO_ACTIVE,
                self::ACTIVE
            ]],
            ['is_remote', 'default', 'value' => self::NOT_REMOTE],
            ['is_remote', 'in', 'range' => [
                self::NOT_REMOTE,
                self::REMOTE
            ]],
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
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class
        ];
    }


    /**
     * @param int $communication_type
     */
    public function setParams(int $communication_type): void
    {
        $this->setCommunicationType($communication_type);
        $this->setInitiator(Yii::$app->user->getId());
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     */
    public function setCommunicationType(int $communication_type): void
    {
        $this->communication_type = $communication_type;
    }

    /**
     * @return int
     */
    public function getInitiator(): int
    {
        return $this->initiator;
    }

    /**
     * @param int $initiator
     */
    public function setInitiator(int $initiator): void
    {
        $this->initiator = $initiator;
    }

    /**
     * @return int
     */
    public function getIsActive(): int
    {
        return $this->is_active;
    }

    /**
     * @param int $is_active
     */
    public function setIsActive(int $is_active): void
    {
        $this->is_active = $is_active;
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
    public function getProjectAccessPeriod(): int
    {
        return $this->project_access_period;
    }

    /**
     * @param int $project_access_period
     */
    public function setProjectAccessPeriod(int $project_access_period): void
    {
        $this->project_access_period = $project_access_period;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    /**
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    /**
     * @return int
     */
    public function getIsRemote(): int
    {
        return $this->is_remote;
    }

    /**
     * @param int $is_remote
     */
    public function setIsRemote(int $is_remote): void
    {
        $this->is_remote = $is_remote;
    }
}