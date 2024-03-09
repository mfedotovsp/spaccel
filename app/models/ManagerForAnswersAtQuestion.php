<?php


namespace app\models;

use app\models\interfaces\ConfirmationInterface;

/**
 * Менеджер по ответам на вопросы
 *
 * Class ManagerForAnswersAtQuestion
 * @package app\models
 */
class ManagerForAnswersAtQuestion
{

    /**
     * Создание пустого ответа для нового вопроса для каждого респондента
     *
     * @param ConfirmationInterface $confirm
     * @param int $question_id
     */
    public function create(ConfirmationInterface $confirm, int $question_id): void
    {
        foreach ($confirm->responds as $respond) {
            $answer = self::getModel($confirm);
            $answer->setQuestionId($question_id);
            $answer->setRespondId($respond->getId());
            $answer->save();
        }
    }


    /**
     * @param ConfirmationInterface $confirm
     * @return AnswersQuestionsConfirmGcp|AnswersQuestionsConfirmMvp|AnswersQuestionsConfirmProblem|AnswersQuestionsConfirmSegment|bool
     */
    private static function getModel(ConfirmationInterface $confirm)
    {
        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return new AnswersQuestionsConfirmSegment();
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return new AnswersQuestionsConfirmProblem();
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_GCP) {
            return new AnswersQuestionsConfirmGcp();
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_MVP) {
            return new AnswersQuestionsConfirmMvp();
        }
        return false;
    }


    /**
     * @param ConfirmationInterface $confirm
     * @param int $question_id
     */
    public function delete(ConfirmationInterface $confirm, int $question_id): void
    {
        $class = self::getClassAnswer($confirm);
        foreach ($confirm->responds as $respond) {
            $answer = $class::find()->andWhere(['question_id' => $question_id, 'respond_id' => $respond->getId()])->one();
            $answer->delete();
        }
    }


    /**
     * @param ConfirmationInterface $confirm
     * @return bool|string
     */
    private static function getClassAnswer(ConfirmationInterface $confirm)
    {
        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return AnswersQuestionsConfirmSegment::class;
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return AnswersQuestionsConfirmProblem::class;
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_GCP) {
            return AnswersQuestionsConfirmGcp::class;
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_MVP) {
            return AnswersQuestionsConfirmMvp::class;
        }
        return false;
    }
}