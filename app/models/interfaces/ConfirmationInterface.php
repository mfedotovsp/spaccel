<?php


namespace app\models\interfaces;

use app\models\Gcps;
use app\models\Mvps;
use app\models\Problems;
use app\models\QuestionsConfirmGcp;
use app\models\QuestionsConfirmMvp;
use app\models\QuestionsConfirmProblem;
use app\models\QuestionsConfirmSegment;
use app\models\RespondsGcp;
use app\models\RespondsMvp;
use app\models\RespondsProblem;
use app\models\RespondsSegment;
use app\models\Segments;
use yii\db\ActiveQuery;

/**
 * Интерфейс для классов, которые реализуют подтверждение гипотез
 *
 * Interface ConfirmationInterface
 * @package app\models\interfaces
 *
 * @property QuestionsConfirmSegment[]|QuestionsConfirmProblem[]|QuestionsConfirmGcp[]|QuestionsConfirmMvp[] $questions
 * @property RespondsSegment[]|RespondsProblem[]|RespondsGcp[]|RespondsMvp[] $responds
 * @property Segments|Problems|Gcps|Mvps $hypothesis
 */
interface ConfirmationInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * Получить вопросы привязанные к подтверждению
     * @return ActiveQuery
     */
    public function getQuestions(): ActiveQuery;


    /**
     * Получить респондентов привязанных к подтверждению
     * @return ActiveQuery
     */
    public function getResponds(): ActiveQuery;


    /**
     * Установить кол-во респондентов
     * @param int $count
     */
    public function setCountRespond(int $count);

    /**
     * @return int
     */
    public function getCountRespond(): int;


    /**
     * @return int
     */
    public function getStage(): int;


    /**
     * Получить гипотезу подтверждения
     * @return ActiveQuery
     */
    public function getHypothesis(): ActiveQuery;
}