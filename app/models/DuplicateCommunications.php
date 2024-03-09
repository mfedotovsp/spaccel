<?php


namespace app\models;


use app\models\interfaces\CommunicationsInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


/**
 * Класс коммуникаций, которые дублируют
 * другие коммуникации
 *
 * Class DuplicateCommunications
 * @package app\models
 *
 * @property int $id                         Идентификатор записи
 * @property int $type                       Тип дублирующей коммуникации
 * @property int $source_id                  Идентификатор записи оригинальной коммуникации
 * @property int $sender_id                  Идентификатор отправителя коммуникации
 * @property int $adressee_id                Идентификатор получателя коммуникации
 * @property string $description             Описание коммуникации
 * @property int $status                     Статус прочтения коммуникации
 * @property int $created_at                 Дата создания коммуникации
 * @property int $updated_at                 Дата обновления коммуникации
 *
 * @property ProjectCommunications $source   Получить объект оригинальной коммуникации
 */
class DuplicateCommunications extends ActiveRecord implements CommunicationsInterface
{

    public const READ = 2468;
    public const NO_READ = 3579;


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'duplicate_communications';
    }


    /**
     * Получить объект оригинальной коммуникации
     *
     * @return ProjectCommunications|null
     */
    public function getSource(): ?ProjectCommunications
    {
        if (in_array($this->getType(), [
            TypesDuplicateCommunication::MAIN_ADMIN_TO_EXPERT,
            TypesDuplicateCommunication::EXPERT_COMPLETED_EXPERTISE,
            TypesDuplicateCommunication::EXPERT_UPDATE_DATA_COMPLETED_EXPERTISE,
            TypesDuplicateCommunication::USER_ALLOWED_EXPERTISE,
            TypesDuplicateCommunication::USER_DELETE_STAGE_PROJECT
        ], false))
        {
            return ProjectCommunications::findOne($this->getSourceId());
        }

        return null;
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['source_id', 'sender_id', 'adressee_id', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['source_id', 'sender_id', 'adressee_id', 'type', 'description'], 'required'],
            ['description', 'string', 'max' => 1000],
            ['type', 'in', 'range' => TypesDuplicateCommunication::getTypes()],
            ['status', 'default', 'value' => self::NO_READ],
            ['status', 'in', 'range' => [
                self::READ,
                self::NO_READ
            ]],
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
     * Создание дублирующей коммуникации
     *
     * @param CommunicationsInterface $source
     * @param User $adressee
     * @param int $type
     * @param false|Expertise $expertise
     * @return DuplicateCommunications|null
     */
    public static function create(CommunicationsInterface $source, User $adressee, int $type, $expertise = false): ?DuplicateCommunications
    {
        $model = new self();

        if ($model->setParams($source, $adressee, $type, $expertise)) {

            return $model->save() ? $model : null;
        }
        return null;
    }


    /**
     * Установить параметры
     * дублирующей коммуникации
     *
     * @param CommunicationsInterface $source
     * @param User $adressee
     * @param int $type
     * @param false|Expertise $expertise
     * @return bool
     */
    private function setParams(CommunicationsInterface $source, User $adressee, int $type, $expertise): bool
    {
        if (is_a($source, ProjectCommunications::class)) {

            $this->setType($type);
            $this->setSourceId($source->getId());
            $this->setSenderId($source->getSenderId());
            $this->setAdresseeId($adressee->getId());
            $this->setDescription(PatternsDescriptionDuplicateCommunication::getValue($source, $adressee, $type, $expertise));

            return true;
        }
        return false;
    }


    /**
     * Показывать ли кнопку
     * прочтения уведомления
     *
     * @return bool
     */
    public function isNeedReadButton(): bool
    {
        if ($this->getStatus() === self::NO_READ) {
            return true;
        }
        return false;
    }


    /**
     * Установить параметр
     * прочтения коммуникации
     */
    public function setStatusRead(): void
    {
        $this->status = self::READ;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }


    /**
     * Получить id получателя коммуникации
     *
     * @return int
     */
    public function getAdresseeId(): int
    {
        return $this->adressee_id;
    }

    /**
     * @param int $adressee_id
     */
    public function setAdresseeId(int $adressee_id): void
    {
        $this->adressee_id = $adressee_id;
    }

    /**
     * Получить id отправителя коммуникации
     *
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->sender_id;
    }

    /**
     * @param int $sender_id
     */
    public function setSenderId(int $sender_id): void
    {
        $this->sender_id = $sender_id;
    }

    /**
     * Получить id коммуникации
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getSourceId(): int
    {
        return $this->source_id;
    }

    /**
     * @param int $source_id
     */
    public function setSourceId(int $source_id): void
    {
        $this->source_id = $source_id;
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
}