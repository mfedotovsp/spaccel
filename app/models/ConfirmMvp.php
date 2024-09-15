<?php

namespace app\models;

use app\models\interfaces\ConfirmationInterface;
use app\models\traits\SoftDeleteModelTrait;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;

/**
 * Класс, который хранит объекты подтверждений mvp-продуктов в бд
 *
 * Class ConfirmMvp
 * @package app\models
 *
 * @property int $id                                    Идентификатор записи в таб. confirm_mvp
 * @property int $mvp_id                                Идентификатор записи в таб. mvps
 * @property int $count_respond                         Количество респондентов
 * @property int $count_positive                        Количество респондентов, подтверждающих mvp-продукт
 * @property string $enable_expertise                   Параметр разрешения на экспертизу по даному этапу
 * @property int|null $enable_expertise_at              Дата разрешения на экспертизу по даному этапу
 * @property int|null $deleted_at                       Дата удаления
 * @property boolean $exist_desc                        Флаг наличия описания подтверждения (учебный вариант)
 *
 * @property Mvps $mvp                                  Mvp-продукт
 * @property RespondsMvp[] $responds                    Респонденты, привязанные к подтверждению
 * @property BusinessModel $business                    Бизнес-модель
 * @property QuestionsConfirmMvp[] $questions           Вопросы, привязанные к подтверждению
 * @property Mvps $hypothesis                           Гипотеза, к которой относится подтверждение
 *
 * @property ConfirmDescription|null $confirmDescription                   Описание подтверждения для учебного варианта
 */
class ConfirmMvp extends ActiveRecord implements ConfirmationInterface
{
    use SoftDeleteModelTrait;

    public const STAGE = 8;
    public const LIMIT_COUNT_RESPOND = 100;


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'confirm_mvp';
    }


    /**
     * @return int
     */
    public function getStage(): int
    {
        return self::STAGE;
    }


    /**
     * Проверка на ограничение кол-ва респондентов
     *
     * @return bool
     */
    public function checkingLimitCountRespond(): bool
    {
        if ($this->getCountRespond() < self::LIMIT_COUNT_RESPOND) {
            return true;
        }

        return false;
    }


    /**
     * Получить объект текущего Mvps
     *
     * @return ActiveQuery
     */
    public function getMvp(): ActiveQuery
    {
        return $this->hasOne(Mvps::class, ['id' => 'mvp_id']);
    }


    /**
     * Получить респондентов привязанных к подтверждению
     *
     * @return ActiveQuery
     */
    public function getResponds(): ActiveQuery
    {
        return $this->hasMany(RespondsMvp::class, ['confirm_id' => 'id']);
    }


    /**
     * Получить объект бизнес модели
     *
     * @return ActiveQuery
     */
    public function getBusiness(): ActiveQuery
    {
        return $this->hasOne(BusinessModel::class, ['basic_confirm_id' => 'id']);
    }


    /**
     * Получить вопросы привязанные к подтверждению
     *
     * @return ActiveQuery
     */
    public function getQuestions(): ActiveQuery
    {
        return $this->hasMany(QuestionsConfirmMvp::class, ['confirm_id' => 'id']);
    }


    /**
     * Получить гипотезу подтверждения
     *
     * @return ActiveQuery
     */
    public function getHypothesis(): ActiveQuery
    {
        return $this->hasOne(Mvps::class, ['id' => 'mvp_id']);
    }


    /**
     * Получить описание подтверждения
     * для учебного варианта
     *
     * @return ActiveRecord|null
     */
    public function getConfirmDescription(): ?ActiveRecord
    {
        return ConfirmDescription::find()
            ->andWhere(['confirm_id' => $this->getId()])
            ->andWhere(['type' => StageExpertise::CONFIRM_MVP])
            ->one() ?: null;
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['mvp_id', 'count_respond', 'count_positive'], 'required'],
            [['mvp_id'], 'integer'],
            ['count_respond', 'integer', 'integerOnly' => TRUE, 'min' => 0],
            ['count_positive', 'integer', 'integerOnly' => TRUE, 'min' => 1],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'max' => 100],
            ['enable_expertise', 'default', 'value' => EnableExpertise::OFF],
            ['enable_expertise', 'in', 'range' => [
                EnableExpertise::OFF,
                EnableExpertise::ON,
            ]],
            ['exist_desc', 'default', 'value' => false],
            ['enable_expertise', 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'count_respond' => 'Количество респондентов',
            'count_positive' => 'Необходимое количество позитивных ответов',
        ];
    }


    public function init()
    {

        $this->on(self::EVENT_AFTER_INSERT, function (){
            $this->mvp->project->touch('updated_at');
            $this->mvp->project->user->touch('updated_at');
        });

        $this->on(self::EVENT_AFTER_UPDATE, function (){
            $this->mvp->project->touch('updated_at');
            $this->mvp->project->user->touch('updated_at');
        });

        parent::init();
    }


    /**
     * Список вопросов, который будет показан для добавления нового вопроса
     *
     * @return array
     */
    public function queryQuestionsGeneralList(): array
    {
        $user = $this->mvp->project->user;
        $questions = []; //Добавляем в массив $questions вопросы уже привязанные к данной программе
        foreach ($this->questions as $question) {
            $questions[] = $question['title'];
        }

        /**
         * @var AllQuestionsConfirmMvp[] $attachQuestions
         */
        // Вопросы, предлагаемые по-умолчанию на данном этапе
        $defaultQuestions = AllQuestionsConfirmMvp::defaultListQuestions();
        // Вопросы, которые когда-либо добавлял пользователь на данном этапе
        $attachQuestions = AllQuestionsConfirmMvp::find()
            ->andWhere(['user_id' => $user->getId()])
            ->orderBy(['id' => SORT_DESC])
            ->select('title')
            ->asArray()
            ->all();

        $qs = array(); // Добавляем в массив вопросы, предлагаемые по-умолчанию на данном этапе
        foreach ($defaultQuestions as $question) {
            $qs[] = $question['title'];
        }
        // Убираем из списка вопросов, которые когда-либо добавлял пользователь на данном этапе
        // вопросы, которые совпадают  с вопросами по-умолчанию
        foreach ($attachQuestions as $key => $queryQuestion) {
            if (in_array($queryQuestion['title'], $qs, false)) {
                unset($attachQuestions[$key]);
            }
        }

        //Убираем из списка для добавления вопросов, вопросы уже привязанные к данной программе
        $queryQuestions = array_merge($defaultQuestions, $attachQuestions);
        foreach ($queryQuestions as $key => $queryQuestion) {
            if (in_array($queryQuestion['title'], $questions, false)) {
                unset($queryQuestions[$key]);
            }
        }

        return $queryQuestions;
    }


    /**
     * @return bool
     */
    public function getButtonMovingNextStage(): bool
    {
        $count_interview = (int)RespondsMvp::find()->with('interview')
            ->leftJoin('interview_confirm_mvp', '`interview_confirm_mvp`.`respond_id` = `responds_mvp`.`id`')
            ->andWhere(['confirm_id' => $this->getId()])->andWhere(['not', ['interview_confirm_mvp.id' => null]])->count();

        $count_positive = (int)RespondsMvp::find()->with('interview')
            ->leftJoin('interview_confirm_mvp', '`interview_confirm_mvp`.`respond_id` = `responds_mvp`.`id`')
            ->andWhere(['confirm_id' => $this->getId(), 'interview_confirm_mvp.status' => '1'])->count();

        if ($this->business || (count($this->responds) === $count_interview && $this->getCountPositive() <= $count_positive)) {
            return true;
        }

        return false;
    }


    /**
     * @return int|string
     */
    public function getCountRespondsOfModel()
    {
        //Кол-во респондентов, у кот-х заполнены данные
        return RespondsMvp::find(false)->andWhere(['confirm_id' => $this->getId()])->andWhere(['not', ['info_respond' => '']])
            ->andWhere(['not', ['date_plan' => null]])->andWhere(['not', ['place_interview' => '']])->count();
    }


    /**
     * @return int|string
     */
    public function getCountDescInterviewsOfModel()
    {
        // Кол-во респондентов, у кот-х существует анкета
        return RespondsMvp::find(false)->with('interview')
            ->leftJoin('interview_confirm_mvp', '`interview_confirm_mvp`.`respond_id` = `responds_mvp`.`id`')
            ->andWhere(['confirm_id' => $this->getId()])->andWhere(['not', ['interview_confirm_mvp.id' => null]])->count();
    }


    /**
     * @return int|string
     */
    public function getCountConfirmMembers()
    {
        // Кол-во подтвердивших MVP
        return RespondsMvp::find(false)->with('interview')
            ->leftJoin('interview_confirm_mvp', '`interview_confirm_mvp`.`respond_id` = `responds_mvp`.`id`')
            ->andWhere(['confirm_id' => $this->getId(), 'interview_confirm_mvp.status' => '1'])->count();
    }


    /**
     * Путь к папке всего
     * кэша данного подтверждения
     *
     * @return string
     */
    public function getCachePath(): string
    {
        $mvp = $this->mvp;
        $gcp = $mvp->gcp;
        $problem = $mvp->problem;
        $segment = $mvp->segment;
        $project = $mvp->project;
        $user = $project->user;
        return '../runtime/cache/forms/user-'.$user->getId().'/projects/project-'.$project->getId(). '/segments/segment-'.$segment->getId().
            '/problems/problem-'.$problem->getId().'/gcps/gcp-'.$gcp->getId().'/mvps/mvp-'.$mvp->getId().'/confirm';
    }


    /**
     * Разрешение эксертизы и отправка уведомлений
     * эксперту и трекеру (если на проект назначен экперт)
     *
     * @param Mvps $mvp
     * @return bool
     * @throws StaleObjectException
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function allowExpertise(Mvps $mvp): bool
    {
        if ($this->getEnableExpertise() === EnableExpertise::ON) {
            return true;
        }

        $project = $this->hypothesis->project;
        $user = $project->user;
        $transaction = Yii::$app->db->beginTransaction();
        if ($expertIds = ProjectCommunications::getExpertIdsByProjectId($project->getId())) {

            $communicationIds = [];
            foreach ($expertIds as $i => $expertId) {
                $communication = new ProjectCommunications();
                $communication->setParams($expertId, $project->getId(), CommunicationTypes::USER_ALLOWED_CONFIRM_MVP_EXPERTISE, $this->getId());
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
                if ($this->update() && $mvp->update()) {
                    $transaction->commit();
                    return true;
                }
            }

            $transaction->rollBack();
            return false;
        }

        $this->setEnableExpertise();
        if ($this->update() && $mvp->update()) {
            $transaction->commit();
            return true;
        }

        $transaction->rollBack();
        return false;
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
    public function setMvpId(int $id): void
    {
        $this->mvp_id = $id;
    }

    /**
     * @return int
     */
    public function getMvpId(): int
    {
        return $this->mvp_id;
    }

    /**
     * @return int
     */
    public function getCountRespond(): int
    {
        return $this->count_respond;
    }

    /**
     * @param int $count
     */
    public function setCountRespond(int $count): void
    {
        $this->count_respond = $count;
    }

    /**
     * @return int
     */
    public function getCountPositive(): int
    {
        return $this->count_positive;
    }

    /**
     * @param int $count
     */
    public function setCountPositive(int $count): void
    {
        $this->count_positive = $count;
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

    /**
     * @return bool
     */
    public function isExistDesc(): bool
    {
        return $this->exist_desc;
    }

    /**
     * @param bool $exist_desc
     */
    public function setExistDesc(bool $exist_desc): void
    {
        $this->exist_desc = $exist_desc;
    }
}
