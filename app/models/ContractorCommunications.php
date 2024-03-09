<?php

namespace app\models;

use app\models\interfaces\CommunicationsInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\Html;

/**
 * Класс коммуникаций проектантов и исполнителей проектов для доступа к проекту
 *
 * Class ContractorCommunications
 * @package app\models
 *
 * @property int $id                                                        Идентификатор коммуникации
 * @property int $sender_id                                                 Идентификатор отправителя коммуникации из таб.User
 * @property int $adressee_id                                               Идентификатор получателя коммуникации из таб.User
 * @property int $type                                                      Тип коммуникации из ContractorCommunicationTypes
 * @property int $project_id                                                Идентификатор проекта, по которому отправлена коммуникация
 * @property int $activity_id                                               Идентификатор вида деятельности
 * @property int|null $stage                                                Этап проекта
 * @property int|null $stage_id                                             Идентификатор этапа проекта
 * @property int $status                                                    Статус прочтения коммуникации
 * @property int|null $triggered_communication_id                           Идентификатор коммуникации в ответ, на которую была создана данная коммуникация
 * @property int $created_at                                                Дата создания коммуникации
 *
 * @property ContractorProjectAccess $contractorProjectAccess               объект доступа исполнителя к проекту по коммуникации
 * @property ContractorCommunicationResponse $communicationResponse         объект ответа по коммуникации
 * @property ContractorCommunications $responsiveCommunication              объект ответной коммуникации, т.е. обращение от коммуникации на которую ответили, а запрос на поиск коммуникации, которой ответили
 * @property ContractorCommunications $communicationAnswered                коммуникация на которую, была создана ответная коммуникация, запрос выполняется от ответной коммуникации
 * @property User $contractor                                               исполнитель
 * @property User $user                                                     проектант
 * @property Projects $project                                              объект проекта, по которому создана коммуникация
 * @property string $nameStage                                              название этапа проекта
 * @property string $accessStatus                                           статус доступа к проекту
 * @property string $notificationStatus                                     Тип (статус) уведомления для исполнителя
 *
 * @property Projects|Segments|ConfirmSegment|Problems|ConfirmProblem|Gcps|ConfirmGcp|Mvps|ConfirmMvp|BusinessModel $hypothesis   Объект этапа проекта
 * @property ContractorActivities $activity                                 Вид деятельности
 */
class ContractorCommunications extends ActiveRecord implements CommunicationsInterface
{
    public const STATUS_READ = 70008;
    public const STATUS_NO_READ = 80007;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'contractor_communications';
    }

    /**
     * Получить объект доступа пользователя
     * к проекту по коммуникации
     *
     * @return ActiveQuery
     */
    public function getContractorProjectAccess(): ActiveQuery
    {
        return $this->hasOne(ContractorProjectAccess::class, ['communication_id' => 'id']);
    }


    /**
     * Получить объект
     * ответа по коммуникации
     *
     * @return ActiveQuery
     */
    public function getCommunicationResponse(): ActiveQuery
    {
        return $this->hasOne(ContractorCommunicationResponse::class, ['communication_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getActivity(): ActiveQuery
    {
        return $this->hasOne(ContractorActivities::class, ['id' => 'activity_id']);
    }


    /**
     * Получить объект ответной коммуникации,
     * т.е. обращение от коммуникации на которую ответили,
     * а запрос на поиск коммуникации, которой ответили
     *
     * @return ContractorCommunications|null
     */
    public function getResponsiveCommunication(): ?ContractorCommunications
    {
        return self::findOne(['triggered_communication_id' => $this->getId()]);
    }


    /**
     * Получить коммуникацию на которую,
     * была создана ответная коммуникация,
     * запрос выполняется от ответной коммуникации
     *
     * @return ContractorCommunications|null
     */
    public function getCommunicationAnswered(): ?ContractorCommunications
    {
        return self::findOne(['id' => $this->getTriggeredCommunicationId()]);
    }


    /**
     * Получить объект
     * исполнителя
     *
     * @return User|null
     */
    public function getContractor(): ?User
    {
        if (in_array($this->getType(), [
            ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT,
            ContractorCommunicationTypes::CONTRACTOR_CHANGE_STATUS_TASK
        ], true)) {
            return User::findOne($this->getSenderId());
        }

        if (in_array($this->getType(), [
            ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_REQUEST_ABOUT_READINESS_TO_JOIN_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT,
            ContractorCommunicationTypes::USER_CHANGE_STATUS_TASK,
        ], true)) {
            return User::findOne($this->getAdresseeId());
        }

        return null;
    }


    /**
     * Получить объект
     * проектанта
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (in_array($this->getType(), [
            ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT,
            ContractorCommunicationTypes::CONTRACTOR_CHANGE_STATUS_TASK
        ], true)) {
            return User::findOne($this->getAdresseeId());
        }

        if (in_array($this->getType(), [
            ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_REQUEST_ABOUT_READINESS_TO_JOIN_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT,
            ContractorCommunicationTypes::USER_APPOINTS_SEGMENT_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_SEGMENT_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_PROBLEM_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_PROBLEM_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_GCP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_GCP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_MVP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_MVP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_DELETED_PROJECT,
            ContractorCommunicationTypes::USER_DELETED_SEGMENT,
            ContractorCommunicationTypes::USER_DELETED_PROBLEM,
            ContractorCommunicationTypes::USER_DELETED_GCP,
            ContractorCommunicationTypes::USER_DELETED_MVP,
            ContractorCommunicationTypes::USER_CHANGE_STATUS_TASK,
        ], true)) {
            return User::findOne($this->getSenderId());
        }

        return null;
    }


    /**
     * @return ActiveRecord|null
     */
    public function getHypothesis(): ?ActiveRecord
    {
        if ($this->getType() === StageExpertise::PROJECT) {
            return Projects::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::SEGMENT) {
            return Segments::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::CONFIRM_SEGMENT) {
            return ConfirmSegment::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::PROBLEM) {
            return Problems::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::CONFIRM_PROBLEM) {
            return ConfirmProblem::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::GCP) {
            return Gcps::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::CONFIRM_GCP) {
            return ConfirmGcp::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::MVP) {
            return Mvps::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::CONFIRM_MVP) {
            return ConfirmMvp::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }
        if ($this->getType() === StageExpertise::BUSINESS_MODEL) {
            return BusinessModel::find(false)
                ->andWhere(['id' => $this->getStageId()])
                ->one();
        }

        return null;
    }


    /**
     * Получить объект проекта,
     * по которому создана коммуникация
     *
     * @return ActiveQuery
     */
    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }


    /**
     * Получить название этапа проекта
     *
     * @param int $stage
     * @return string
     */
    public function getNameStage(int $stage): string
    {
        switch ($this->getStage()) {
            case StageExpertise::PROJECT:
                return 'описание проекта';
            case StageExpertise::SEGMENT:
                return 'генерация гипотезы целевого сегмента';
            case StageExpertise::CONFIRM_SEGMENT:
                return 'подтверждение гипотезы целевого сегмента';
            case StageExpertise::PROBLEM:
                return 'генерация гипотезы проблемы сегмента';
            case StageExpertise::CONFIRM_PROBLEM:
                return 'подтверждение гипотезы проблемы сегмента';
            case StageExpertise::GCP:
                return 'разработка гипотезы ценностного предложения';
            case StageExpertise::CONFIRM_GCP:
                return 'подтверждение гипотезы ценностного предложения';
            case StageExpertise::MVP:
                return 'разработка MVP';
            case StageExpertise::CONFIRM_MVP:
                return 'подтверждение MVP';
            case StageExpertise::BUSINESS_MODEL:
                return 'генерация бизнес-модели';
            default:
                return '';
        }
    }


    /**
     * Получить статус
     * доступа к проекту
     *
     * @return string
     */
    public function getAccessStatus(): string
    {
        if ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT) {
            if ($this->contractorProjectAccess->getDateStop() > (time() + 60)) {
                return '<div class="text-success">Открыт до ' . date('d.m.Y H:i', $this->contractorProjectAccess->getDateStop()) . '</div>';
            }

            return '<div class="text-danger">Открыт до ' . date('d.m.Y H:i', $this->contractorProjectAccess->getDateStop()) . '</div>';
        }

        if (in_array($this->getType(), [
            ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_REQUEST_ABOUT_READINESS_TO_JOIN_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT], false)) {
            return '<div class="text-danger">Закрыт</div>';
        }

        if ($this->type === ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT) {
            return '<div class="text-success">Бессрочный</div>';
        }

        return '';
    }


    /**
     * Получить тип (статус)
     * уведомления для исполнителя
     *
     * @return string
     */
    public function getNotificationStatus(): string
    {
        if ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT) {
            if ($this->getStatus() === self::STATUS_READ) {
                return '<div class="text-success">Ответ получен</div>';
            }
            if ($this->getStatus() === self::STATUS_NO_READ && time() < $this->contractorProjectAccess->getDateStop()) {
                return '<div class="text-warning">Требуется ответ</div>';
            }
            if ($this->getStatus() === self::STATUS_NO_READ && time() > $this->contractorProjectAccess->getDateStop()) {
                return '<div class="text-danger">Просрочена дата ответа</div>';
            }
        } elseif ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_REQUEST_ABOUT_READINESS_TO_JOIN_PROJECT) {
            return '<div class="text-danger">Запрос отозван</div>';
        } elseif ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT) {
            return '<div class="text-success">Назначен(-а) на проект</div>';
        } elseif ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT) {
            return '<div class="text-danger">Отказано в назначении на проект</div>';
        } elseif ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT) {
            return '<div class="text-danger">Отозван(-а) с проекта</div>';
        } elseif (in_array($this->getType(), [
            ContractorCommunicationTypes::USER_CHANGE_STATUS_TASK,
            ContractorCommunicationTypes::USER_APPOINTS_SEGMENT_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_SEGMENT_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_PROBLEM_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_PROBLEM_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_GCP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_GCP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_MVP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_MVP_TASK_CONTRACTOR,
        ], true)) {
            if ($this->getStatus() === self::STATUS_READ) {
                return '<div class="text-success">Прочитано</div>';
            }
            return '<div class="text-warning">Не прочитано</div>';
        }

        return '';
    }


    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['sender_id', 'adressee_id', 'type', 'project_id', 'activity_id', 'created_at'], 'integer'],
            [['stage', 'stage_id', 'triggered_communication_id'], 'safe'],
            [['sender_id', 'adressee_id', 'type', 'project_id', 'activity_id', ], 'required'],
            ['status', 'default', 'value' => self::STATUS_NO_READ],
            ['status', 'in', 'range' => [
                self::STATUS_READ,
                self::STATUS_NO_READ
            ]],
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at']],
            ],
        ];
    }


    /**
     * Проверка на необходимость спросить исполнителя
     * (о готовности сделать задание)
     *
     * @param int $contractor_id
     * @param int $project_id
     * @param int $activity_id
     * @return bool
     */
    public static function isNeedAskContractor(int $contractor_id, int $project_id, int $activity_id): bool
    {
        $communications = self::find()
            ->andWhere(['project_id' => $project_id])
            ->andWhere(['or', ['adressee_id' => $contractor_id], ['sender_id' => $contractor_id]])
            ->andWhere(['activity_id' => $activity_id]);

        $existCommunications = $communications->exists();

        if (!$existCommunications) {
            return true;
        }

        /** @var self $lastCommunication */
        $lastCommunication = $communications->orderBy('created_at DESC')->one();

        if (in_array($lastCommunication->getType(), [
            ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_REQUEST_ABOUT_READINESS_TO_JOIN_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT,
            ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT
        ], false)) {
            return true;
        }

        if ($lastCommunication->getType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT
            && $lastCommunication->contractorProjectAccess->getDateStop() < time()) {
            return true;
        }

        if ($lastCommunication->getType() === ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT
            && $lastCommunication->communicationResponse->getAnswer() === ContractorCommunicationResponse::NEGATIVE_RESPONSE) {
            return true;
        }
        return false;
    }


    /**
     * Получение последней коммуникации исполнителя по проектам текущего проектанта
     *
     * @param int $contractorId
     * @return array|ActiveRecord|null
     */
    public static function getLastCommunicationWithContractor(int $contractorId)
    {
        $projects = Projects::find()
            ->andWhere(['user_id' => Yii::$app->user->getId()])
            ->all();

        return self::find()
            ->andWhere(['or', ['adressee_id' => $contractorId], ['sender_id' => $contractorId]])
            ->andWhere(['in', 'project_id', array_column($projects, 'id')])
            ->andWhere(['in', 'type', ContractorCommunicationTypes::getListTypes()])
            ->orderBy('created_at DESC')
            ->one();
    }


    /**
     * Показывать ли исполнителю кнопку
     * ответа на коммуникацию
     *
     * @return bool
     */
    public function isNeedShowButtonAnswer(): bool
    {
        if (User::isUserContractor(Yii::$app->user->identity['username'])) {
            if ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT &&
                $this->getStatus() === self::STATUS_NO_READ && time() < $this->contractorProjectAccess->getDateStop()
                && !self::findOne(['triggered_communication_id' => $this->getId()])) {
                return true;
            }
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
        if (User::isUserContractor(Yii::$app->user->identity['username'])) {
            if ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT) {
                if ($this->getStatus() === self::STATUS_NO_READ && time() > $this->contractorProjectAccess->getDateStop()) {
                    return true;
                }
                if ($this->getStatus() === self::STATUS_NO_READ && self::findOne(['triggered_communication_id' => $this->getId()])) {
                    return true;
                }
            } elseif (!in_array($this->getType(), [ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT, ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT], false)) {
                if ($this->getStatus() === self::STATUS_NO_READ) {
                    return true;
                }
            } elseif ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT &&
                ($this->getStatus() === self::STATUS_NO_READ || time() > $this->contractorProjectAccess->getDateStop())) {
                return true;
            } elseif ($this->getType() === ContractorCommunicationTypes::USER_CHANGE_STATUS_TASK && $this->getStatus() === self::STATUS_NO_READ) {
                return true;
            }
        }
        if (User::isUserSimple(Yii::$app->user->identity['username'])) {
            if ($this->getType() === ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT) {
                if ($this->getStatus() === self::STATUS_NO_READ && !self::findOne(['triggered_communication_id' => $this->getId()])) {
                    return true;
                }
            } elseif ($this->getType() === ContractorCommunicationTypes::CONTRACTOR_CHANGE_STATUS_TASK && $this->getStatus() === self::STATUS_NO_READ) {
                return true;
            }
        }
        return false;
    }


    /**
     * Описание коммуникации
     *
     * @param bool $isSendEmail
     * @return string
     */
    public function getDescription(bool $isSendEmail = false): string
    {
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $this->getProjectId()])
            ->one();

        $projectName_search = '{{наименование проекта}}';
        $projectName_replace = '«' . $project->getProjectName() . '»';
        $activityAccess_search = '{{вид деятельности исполнителя}}';
        $activityAccess_replace = $this->activity->getTitle();
        $linkProjectName_search = '{{наименование проекта, ссылка на проект}}';
        $linkProjectName_replace = Html::a($projectName_replace, ['/projects/index', 'id' => $project->getUserId(), 'project_id' => $project->getId()]);
        $linkProjectName_replace = $isSendEmail ? Html::a($projectName_replace, Yii::$app->urlManager->createAbsoluteUrl(['/projects/index', 'id' => $project->getUserId(), 'project_id' => $project->getId()])) : $linkProjectName_replace;

        if ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_ASKS_ABOUT_READINESS_TO_JOIN_PROJECT) {
            $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_ABOUT_READINESS_TO_JOIN_PROJECT;
            return str_replace($linkProjectName_search, $linkProjectName_replace, str_replace($activityAccess_search, $activityAccess_replace, $defaultPattern));
        }

        if ($this->getType() === ContractorCommunicationTypes::CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT) {
            $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_CONTRACTOR_ANSWERS_QUESTION_ABOUT_READINESS_TO_JOIN_PROJECT;
            return str_replace($linkProjectName_search, $linkProjectName_replace, str_replace($activityAccess_search, $activityAccess_replace, $defaultPattern));
        }

        if ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_REQUEST_ABOUT_READINESS_TO_JOIN_PROJECT) {
            $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_WITHDRAWS_REQUEST_ABOUT_READINESS_TO_JOIN_PROJECT;
            return str_replace($projectName_search, $projectName_replace, str_replace($activityAccess_search, $activityAccess_replace, $defaultPattern));
        }

        if ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_APPOINTS_CONTRACTOR_PROJECT) {
            $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_APPOINTS_CONTRACTOR_PROJECT;
            return str_replace($linkProjectName_search, $linkProjectName_replace, str_replace($activityAccess_search, $activityAccess_replace, $defaultPattern));
        }

        if ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT) {
            $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_DOES_NOT_APPOINTS_CONTRACTOR_PROJECT;
            return str_replace($projectName_search, $projectName_replace, str_replace($activityAccess_search, $activityAccess_replace, $defaultPattern));
        }

        if ($this->getType() === ContractorCommunicationTypes::SIMPLE_USER_WITHDRAWS_CONTRACTOR_FROM_PROJECT) {
            $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_WITHDRAWS_CONTRACTOR_FROM_PROJECT;
            return str_replace($projectName_search, $projectName_replace, str_replace($activityAccess_search, $activityAccess_replace, $defaultPattern));
        }

        if (in_array($this->getType(), [
            ContractorCommunicationTypes::USER_APPOINTS_SEGMENT_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_SEGMENT_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_PROBLEM_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_PROBLEM_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_GCP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_GCP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_MVP_TASK_CONTRACTOR,
            ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_MVP_TASK_CONTRACTOR,
        ], true)) {
            $userName_search = '{{проектант}}';
            $activity_search = '{{вид деятельности исполнителя}}';
            $linkStageProject_search = '{{наименование этапа проекта, ссылка на этап проекта}}';
            $userName_replace = $this->user->getUsername();
            $activity_replace = $this->activity->getTitle();
            $linkStageProject_replace = '';
            $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_USER_CREATED_TASK_STAGE_PROJECT;

            if ($this->getType() === ContractorCommunicationTypes::USER_APPOINTS_SEGMENT_TASK_CONTRACTOR) {
                if (!User::isUserContractor(Yii::$app->user->identity['username'])) {
                    $linkStageProject_replace = Html::a($this->getNameStage(StageExpertise::SEGMENT), ['/contractor/segments/index', 'id' => $this->getStageId()]);
                } else {
                    $linkStageProject_replace = $this->getNameStage(StageExpertise::SEGMENT);
                }
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_SEGMENT_TASK_CONTRACTOR) {
                if (!User::isUserContractor(Yii::$app->user->identity['username'])) {
                    $linkStageProject_replace = Html::a($this->getNameStage(StageExpertise::CONFIRM_SEGMENT), ['/contractor/confirm-segment/view', 'id' => $this->getStageId()]);
                } else {
                    $linkStageProject_replace = $this->getNameStage(StageExpertise::CONFIRM_SEGMENT);
                }
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_APPOINTS_PROBLEM_TASK_CONTRACTOR) {
                if (!User::isUserContractor(Yii::$app->user->identity['username'])) {
                    $linkStageProject_replace = Html::a($this->getNameStage(StageExpertise::PROBLEM), ['/contractor/problems/index', 'id' => $this->getStageId()]);
                } else {
                    $linkStageProject_replace = $this->getNameStage(StageExpertise::PROBLEM);
                }
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_PROBLEM_TASK_CONTRACTOR) {
                if (!User::isUserContractor(Yii::$app->user->identity['username'])) {
                    $linkStageProject_replace = Html::a($this->getNameStage(StageExpertise::CONFIRM_PROBLEM), ['/contractor/confirm-problem/view', 'id' => $this->getStageId()]);
                } else {
                    $linkStageProject_replace = $this->getNameStage(StageExpertise::CONFIRM_PROBLEM);
                }
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_APPOINTS_GCP_TASK_CONTRACTOR) {
                if (!User::isUserContractor(Yii::$app->user->identity['username'])) {
                    $linkStageProject_replace = Html::a($this->getNameStage(StageExpertise::GCP), ['/contractor/gcps/index', 'id' => $this->getStageId()]);
                } else {
                    $linkStageProject_replace = $this->getNameStage(StageExpertise::GCP);
                }
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_GCP_TASK_CONTRACTOR) {
                if (!User::isUserContractor(Yii::$app->user->identity['username'])) {
                    $linkStageProject_replace = Html::a($this->getNameStage(StageExpertise::CONFIRM_GCP), ['/contractor/confirm-gcp/view', 'id' => $this->getStageId()]);
                } else {
                    $linkStageProject_replace = $this->getNameStage(StageExpertise::CONFIRM_GCP);
                }

            }

            if ($this->getType() === ContractorCommunicationTypes::USER_APPOINTS_MVP_TASK_CONTRACTOR) {
                if (!User::isUserContractor(Yii::$app->user->identity['username'])) {
                    $linkStageProject_replace = Html::a($this->getNameStage(StageExpertise::MVP), ['/contractor/mvps/index', 'id' => $this->getStageId()]);
                } else {
                    $linkStageProject_replace = $this->getNameStage(StageExpertise::MVP);
                }
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_APPOINTS_CONFIRM_MVP_TASK_CONTRACTOR) {
                if (!User::isUserContractor(Yii::$app->user->identity['username'])) {
                    $linkStageProject_replace = Html::a($this->getNameStage(StageExpertise::CONFIRM_MVP), ['/contractor/confirm-mvp/view', 'id' => $this->getStageId()]);
                } else {
                    $linkStageProject_replace = $this->getNameStage(StageExpertise::CONFIRM_MVP);
                }
            }

            return str_replace($activity_search, $activity_replace, str_replace($projectName_search, $projectName_replace, str_replace($userName_search, $userName_replace, str_replace($linkStageProject_search, $linkStageProject_replace, $defaultPattern))));
        }

        if (in_array($this->getType(), [
            ContractorCommunicationTypes::USER_DELETED_PROJECT,
            ContractorCommunicationTypes::USER_DELETED_SEGMENT,
            ContractorCommunicationTypes::USER_DELETED_PROBLEM,
            ContractorCommunicationTypes::USER_DELETED_GCP,
            ContractorCommunicationTypes::USER_DELETED_MVP
        ], true)) {
            $userName_search = '{{проектант}}';
            $linkStageProject_search = '{{наименование этапа проекта, ссылка на этап проекта}}';
            $userName_replace = $this->user->getUsername();
            $linkStageProject_replace = '';
            $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_USER_DELETED_STAGE_PROJECT;

            if ($this->getType() === ContractorCommunicationTypes::USER_DELETED_PROJECT) {
                $linkStageProject_replace = Html::a('проект: ' . $this->hypothesis->getProjectName(), ['/contractor/projects/index', 'id' => $this->hypothesis->getUserId(), 'project_id' => $this->getProjectId()]);
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_DELETED_SEGMENT) {
                $linkStageProject_replace = Html::a('сегмент: ' . $this->hypothesis->getName(), ['/contractor/segments/index', 'id' => $this->getProjectId()]);
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_DELETED_PROBLEM) {
                $linkStageProject_replace = Html::a('проблему сегмента: ' . $this->hypothesis->getTitle(), ['/contractor/problems/index', 'id' => $this->hypothesis->getBasicConfirmId()]);
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_DELETED_GCP) {
                $linkStageProject_replace = Html::a('ценностное предложение: ' . $this->hypothesis->getTitle(), ['/contractor/gcps/index', 'id' => $this->hypothesis->getBasicConfirmId()]);
            }

            if ($this->getType() === ContractorCommunicationTypes::USER_DELETED_MVP) {
                $linkStageProject_replace = Html::a('продукт-MVP: ' . $this->hypothesis->getTitle(), ['/contractor/mvps/index', 'id' => $this->hypothesis->getBasicConfirmId()]);
            }

            return str_replace($projectName_search, $projectName_replace, str_replace($userName_search, $userName_replace, str_replace($linkStageProject_search, $linkStageProject_replace, $defaultPattern)));
        }

        if (in_array($this->getType(), [
            ContractorCommunicationTypes::USER_CHANGE_STATUS_TASK,
            ContractorCommunicationTypes::CONTRACTOR_CHANGE_STATUS_TASK
        ], true)) {
            $userName_search = '{{пользователь}}';
            $activity_search = '{{вид деятельности исполнителя}}';
            $linkStageProject_search = '{{наименование этапа проекта}}';
            $userName_replace = $this->user->getUsername();
            $activity_replace = $this->activity->getTitle();
            $linkStageProject_replace = '';
            $defaultPattern = '';

            if ($this->getType() === ContractorCommunicationTypes::USER_CHANGE_STATUS_TASK) {
                $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_USER_CHANGE_STATUS_TASK;
                $linkStageProject_replace = $this->getNameStage($this->getStage());
            }

            if ($this->getType() === ContractorCommunicationTypes::CONTRACTOR_CHANGE_STATUS_TASK) {
                $userName_replace = $this->contractor->getUsername();
                $defaultPattern = ContractorCommunicationPatterns::COMMUNICATION_DEFAULT_CONTRACTOR_CHANGE_STATUS_TASK;
                $linkStageProject_replace = $this->getNameStage($this->getStage());
            }

            return str_replace($activity_search, $activity_replace, str_replace($projectName_search, $projectName_replace, str_replace($userName_search, $userName_replace, str_replace($linkStageProject_search, $linkStageProject_replace, $defaultPattern))));
        }
        
        return '';
    }


    /**
     * @param int $adresseeId
     * @param int $projectId
     * @param int $activityId
     * @param int $type
     * @param int|null $stage
     * @param int|null $stageId
     * @param int|null $triggeredCommunicationId
     */
    public function setParams(
        int $adresseeId,
        int $projectId,
        int $activityId,
        int $type,
        int $stage = null,
        int $stageId = null,
        int $triggeredCommunicationId = null
    ): void
    {
        $this->setSenderId(Yii::$app->user->getId());
        $this->setAdresseeId($adresseeId);
        $this->setProjectId($projectId);
        $this->setActivityId($activityId);
        $this->setType($type);
        $this->setStage($stage);
        $this->setStageId($stageId);
        $this->setTriggeredCommunicationId($triggeredCommunicationId);
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
    public function getActivityId(): int
    {
        return $this->activity_id;
    }

    /**
     * @param int $activity_id
     */
    public function setActivityId(int $activity_id): void
    {
        $this->activity_id = $activity_id;
    }

    /**
     * @return int|null
     */
    public function getStage(): ?int
    {
        return $this->stage;
    }

    /**
     * @param int|null $stage
     */
    public function setStage(?int $stage): void
    {
        $this->stage = $stage;
    }

    /**
     * @return int|null
     */
    public function getStageId(): ?int
    {
        return $this->stage_id;
    }

    /**
     * @param int|null $stage_id
     */
    public function setStageId(?int $stage_id): void
    {
        $this->stage_id = $stage_id;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Установить параметр
     * прочтения коммуникации
     */
    public function setStatusRead(): void
    {
        $this->status = self::STATUS_READ;
    }

    /**
     * @return int|null
     */
    public function getTriggeredCommunicationId(): ?int
    {
        return $this->triggered_communication_id;
    }

    /**
     * @param int|null $triggered_communication_id
     */
    public function setTriggeredCommunicationId(?int $triggered_communication_id): void
    {
        $this->triggered_communication_id = $triggered_communication_id;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

}