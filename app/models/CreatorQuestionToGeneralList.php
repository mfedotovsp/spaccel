<?php


namespace app\models;

use app\models\interfaces\ConfirmationInterface;

/**
 * Класс добавляет вопросы в таблицы, которые содержат все вопросы добавляемые на этапах подтверждения гипотез
 *
 * Class CreatorQuestionToGeneralList
 * @package app\models
 */
class CreatorQuestionToGeneralList
{

    /**
     * @param ConfirmationInterface $confirm
     * @param $title
     */
    public function create(ConfirmationInterface $confirm, $title): void
    {
        $user = $confirm->hypothesis->project->user;
        $class = self::getClassQuestion($confirm);
        $baseQuestions = $class::find()->andWhere(['user_id' => $user->getId()])->select('title')->all();
        $existQuestions = 0;

        foreach ($baseQuestions as $baseQuestion){
            if ($baseQuestion->title === $title){
                $existQuestions++;
            }
        }

        if ($existQuestions === 0){
            $general_question = self::getModel($confirm);
            $general_question->setTitle($title);
            $general_question->setUserId($user->getId());
            $general_question->save();
        }
    }


    /**
     * @param $confirm
     * @return bool|string
     */
    private static function getClassQuestion($confirm)
    {
        if ($confirm->stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return AllQuestionsConfirmSegment::class;
        }

        if ($confirm->stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return AllQuestionsConfirmProblem::class;
        }

        if ($confirm->stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return AllQuestionsConfirmGcp::class;
        }

        if ($confirm->stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return AllQuestionsConfirmMvp::class;
        }
        return false;
    }


    /**
     * @param $confirm
     * @return AllQuestionsConfirmGcp|AllQuestionsConfirmMvp|AllQuestionsConfirmProblem|AllQuestionsConfirmSegment|bool
     */
    private static function getModel($confirm)
    {
        if ($confirm->stage === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return new AllQuestionsConfirmSegment();
        }

        if ($confirm->stage === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return new AllQuestionsConfirmProblem();
        }

        if ($confirm->stage === StageConfirm::STAGE_CONFIRM_GCP) {
            return new AllQuestionsConfirmGcp();
        }

        if ($confirm->stage === StageConfirm::STAGE_CONFIRM_MVP) {
            return new AllQuestionsConfirmMvp();
        }
        return false;
    }
}