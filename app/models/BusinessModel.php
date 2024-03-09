<?php

namespace app\models;

use app\models\traits\SoftDeleteModelTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Класс, который хранит объекты бизнес-моделей в бд
 *
 * Class BusinessModel
 * @package app\models
 *
 * @property int $id                                Идентификатор записи в таб. business_model
 * @property int $basic_confirm_id                  Идентификатор записи в таб. confirm_mvp
 * @property int $segment_id                        Идентификатор записи в таб. segments
 * @property int $project_id                        Идентификатор записи в таб. projects
 * @property int $problem_id                        Идентификатор записи в таб. problems
 * @property int $gcp_id                            Идентификатор записи в таб. gcps
 * @property int $mvp_id                            Идентификатор записи в таб. mvps
 * @property string $relations                      Взаимоотношения с клиентами
 * @property string $partners                       Ключевые партнеры
 * @property string $distribution_of_sales          Каналы коммуникации и сбыта
 * @property string $resources                      Ключевые ресурсы
 * @property string $cost                           Структура издержек
 * @property string $revenue                        Потоки поступления доходов
 * @property int $created_at                        Дата создания mvp-продукта
 * @property int $updated_at                        Дата обновления mvp-продукта
 * @property string $enable_expertise               Параметр разрешения на экспертизу по даному этапу
 * @property int|null $enable_expertise_at          Дата разрешения на экспертизу по даному этапу
 * @property int|null $deleted_at                   Дата удаления
 *
 * @property Projects $project                      Проект
 * @property Segments $segment                      Сегмент
 * @property Problems $problem                      Проблема
 * @property Gcps $gcp                              Ценностное предложение
 * @property Mvps $mvp                              Mvp-продукт
 * @property ConfirmMvp $confirmMvp                 Подтверждение mvp-продукта
 */
class BusinessModel extends ActiveRecord
{
    use SoftDeleteModelTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'business_model';
    }


    /**
     * Получить объект проекта
     *
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }


    /**
     * Получить объект сегмента
     *
     * @return ActiveQuery
     */
    public function getSegment(): ActiveQuery
    {
        return $this->hasOne(Segments::class, ['id' => 'segment_id']);
    }


    /**
     * Получить объект проблемы
     *
     * @return ActiveQuery
     */
    public function getProblem(): ActiveQuery
    {
        return $this->hasOne(Problems::class, ['id' => 'problem_id']);
    }


    /**
     * Получить объект Gcps
     *
     * @return ActiveQuery
     */
    public function getGcp(): ActiveQuery
    {
        return $this->hasOne(Gcps::class, ['id' => 'gcp_id']);
    }


    /**
     * Получить объект Mvps
     *
     * @return ActiveQuery
     */
    public function getMvp(): ActiveQuery
    {
        return $this->hasOne(Mvps::class, ['id' => 'mvp_id']);
    }


    /**
     * Получить объект подтверждения Mvps
     * @return ActiveQuery
     */
    public function getConfirmMvp(): ActiveQuery
    {
        return $this->hasOne(ConfirmMvp::class, ['id' => 'basic_confirm_id']);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['basic_confirm_id', 'relations', 'partners', 'distribution_of_sales', 'resources', 'cost', 'revenue'], 'required'],
            [['basic_confirm_id', 'project_id', 'segment_id', 'problem_id', 'gcp_id', 'mvp_id', 'created_at', 'updated_at'], 'integer'],
            [['relations', 'distribution_of_sales', 'resources'], 'string', 'max' => 255],
            [['partners', 'cost', 'revenue'], 'string', 'max' => 1000],
            [['relations', 'partners', 'distribution_of_sales', 'resources', 'cost', 'revenue'], 'trim'],
            ['enable_expertise', 'default', 'value' => EnableExpertise::OFF],
            ['enable_expertise', 'in', 'range' => [
                EnableExpertise::OFF,
                EnableExpertise::ON,
            ]],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'confirm_mvp_id' => 'Confirm Mvps ID',
            'relations' => 'Взаимоотношения с клиентами',
            'partners' => 'Ключевые партнеры',
            'distribution_of_sales' => 'Каналы коммуникации и сбыта',
            'resources' => 'Ключевые ресурсы',
            'cost' => 'Структура издержек',
            'revenue' => 'Потоки поступления доходов',
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


    public function init()
    {

        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->project->touch('updated_at');
            $this->project->user->touch('updated_at');
        });

        $this->on(self::EVENT_AFTER_UPDATE, function (){
            $this->project->touch('updated_at');
            $this->project->user->touch('updated_at');
        });

        parent::init();
    }


    /**
     * Разрешение эксертизы и отправка уведомлений
     * эксперту и трекеру (если на проект назначен экперт)
     *
     * @return bool
     * @throws StaleObjectException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function allowExpertise(): bool
    {
        if ($this->getEnableExpertise() === EnableExpertise::ON) {
            return true;
        }

        $user = $this->project->user;
        if ($expertIds = ProjectCommunications::getExpertIdsByProjectId($this->getProjectId())) {
            $transaction = Yii::$app->db->beginTransaction();

            $communicationIds = [];
            foreach ($expertIds as $i => $expertId) {
                $communication = new ProjectCommunications();
                $communication->setParams($expertId, $this->getProjectId(), CommunicationTypes::USER_ALLOWED_BUSINESS_MODEL_EXPERTISE, $this->getId());
                if ($i === 0 && $communication->save() && DuplicateCommunications::create($communication, $user->admin, TypesDuplicateCommunication::USER_ALLOWED_EXPERTISE)) {
                    $communicationIds[] = $communication->getId();
                    SendingCommunicationsToEmail::allowExpertiseToStageProject($communication, true);
                } elseif ($communication->save()) {
                    $communicationIds[] = $communication->getId();
                    SendingCommunicationsToEmail::allowExpertiseToStageProject($communication);
                }
            }

            if (count($communicationIds) === count($expertIds)) {
                $this->setEnableExpertise();
                if ($this->update()) {
                    $transaction->commit();
                    return true;
                }
            }

            $transaction->rollBack();
            return false;
        }

        $this->setEnableExpertise();
        return (bool)$this->update();
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
    public function getBasicConfirmId(): int
    {
        return $this->basic_confirm_id;
    }

    /**
     * @param int $basic_confirm_id
     */
    public function setBasicConfirmId(int $basic_confirm_id): void
    {
        $this->basic_confirm_id = $basic_confirm_id;
    }

    /**
     * @return int
     */
    public function getSegmentId(): int
    {
        return $this->segment_id;
    }

    /**
     * @param int $segment_id
     */
    public function setSegmentId(int $segment_id): void
    {
        $this->segment_id = $segment_id;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->project_id;
    }

    /**
     * @param int $project_id
     */
    public function setProjectId(int $project_id): void
    {
        $this->project_id = $project_id;
    }

    /**
     * @return int
     */
    public function getProblemId(): int
    {
        return $this->problem_id;
    }

    /**
     * @param int $problem_id
     */
    public function setProblemId(int $problem_id): void
    {
        $this->problem_id = $problem_id;
    }

    /**
     * @return int
     */
    public function getGcpId(): int
    {
        return $this->gcp_id;
    }

    /**
     * @param int $gcp_id
     */
    public function setGcpId(int $gcp_id): void
    {
        $this->gcp_id = $gcp_id;
    }

    /**
     * @return int
     */
    public function getMvpId(): int
    {
        return $this->mvp_id;
    }

    /**
     * @param int $mvp_id
     */
    public function setMvpId(int $mvp_id): void
    {
        $this->mvp_id = $mvp_id;
    }

    /**
     * @return string
     */
    public function getRelations(): string
    {
        return $this->relations;
    }

    /**
     * @param string $relations
     */
    public function setRelations(string $relations): void
    {
        $this->relations = $relations;
    }

    /**
     * @return string
     */
    public function getPartners(): string
    {
        return $this->partners;
    }

    /**
     * @param string $partners
     */
    public function setPartners(string $partners): void
    {
        $this->partners = $partners;
    }

    /**
     * @return string
     */
    public function getDistributionOfSales(): string
    {
        return $this->distribution_of_sales;
    }

    /**
     * @param string $distribution_of_sales
     */
    public function setDistributionOfSales(string $distribution_of_sales): void
    {
        $this->distribution_of_sales = $distribution_of_sales;
    }

    /**
     * @return string
     */
    public function getResources(): string
    {
        return $this->resources;
    }

    /**
     * @param string $resources
     */
    public function setResources(string $resources): void
    {
        $this->resources = $resources;
    }

    /**
     * @return string
     */
    public function getCost(): string
    {
        return $this->cost;
    }

    /**
     * @param string $cost
     */
    public function setCost(string $cost): void
    {
        $this->cost = $cost;
    }

    /**
     * @return string
     */
    public function getRevenue(): string
    {
        return $this->revenue;
    }

    /**
     * @param string $revenue
     */
    public function setRevenue(string $revenue): void
    {
        $this->revenue = $revenue;
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
    public function getConfirmMvpId(): int
    {
        return $this->basic_confirm_id;
    }

    /**
     * @return string
     */
    public function getEnableExpertise(): string
    {
        return $this->enable_expertise;
    }

    /**
     *  Установить разрешение на экспертизу
     */
    public function setEnableExpertise(): void
    {
        $this->enable_expertise = EnableExpertise::ON;
        $this->setEnableExpertiseAt(time());
    }

    /**
     * @return int|null
     */
    public function getEnableExpertiseAt(): ?int
    {
        return $this->enable_expertise_at;
    }

    /**
     * @param int $enable_expertise_at
     */
    public function setEnableExpertiseAt(int $enable_expertise_at): void
    {
        $this->enable_expertise_at = $enable_expertise_at;
    }

    /**
     * @return int|null
     */
    public function getDeletedAt(): ?int
    {
        return $this->deleted_at;
    }

    /**
     * @param int $deleted_at
     */
    public function setDeletedAt(int $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }
}
