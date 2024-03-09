<?php

namespace app\models;

use app\models\interfaces\ConfirmationInterface;
use app\models\interfaces\RespondsInterface;
use yii\base\Model;

/**
 * Класс для создания пустых ответов на вопросы для нового респондента
 *
 * Class CreatorAnswersForNewRespond
 * @package app\models
 */
class CreatorAnswersForNewRespond extends Model
{

    /**
     * Создание пустых ответов на вопросы для нового респондента
     * @param RespondsInterface $respond
     */
    public function create(RespondsInterface $respond): void
    {
        /**
         * @var ConfirmSegment|ConfirmProblem|ConfirmGcp|ConfirmMvp $confirm
         * @var QuestionsConfirmSegment|QuestionsConfirmProblem|QuestionsConfirmGcp|QuestionsConfirmMvp $questions
         */
        $confirm = $respond->confirm;
        $questions = $confirm->questions;

        foreach ($questions as $question){
            $answer = self::getCreateModel($confirm);
            $answer->setQuestionId($question->getId());
            $answer->setRespondId($respond->getId());
            $answer->save();
        }
    }


    /**
     * @param ConfirmationInterface $confirm
     * @return AnswersQuestionsConfirmGcp|AnswersQuestionsConfirmMvp|AnswersQuestionsConfirmProblem|AnswersQuestionsConfirmSegment|bool
     */
    private static function getCreateModel(ConfirmationInterface $confirm)
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
}