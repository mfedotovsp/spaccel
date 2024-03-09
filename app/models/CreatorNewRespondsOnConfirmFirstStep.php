<?php


namespace app\models;

use app\models\forms\FormCreateConfirm;
use app\models\interfaces\ConfirmationInterface;
use app\models\interfaces\CreateRespondsOnConfirmFirstStepInterface;
use yii\base\Model;

/**
 * Класс создания новых респондентов по заданному кол-ву на первом шаге подтверждения
 * Class CreatorNewRespondsOnConfirmFirstStep
 * @package app\models
 */
class CreatorNewRespondsOnConfirmFirstStep extends Model implements CreateRespondsOnConfirmFirstStepInterface
{

    /**
     * @param ConfirmationInterface $confirm
     * @param FormCreateConfirm $form
     */
    public function create(ConfirmationInterface $confirm, FormCreateConfirm $form): void
    {
        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            for ($i = 1; $i <= $form->count_respond; $i++) {
                $newRespond[$i] = self::getCreateModel($confirm);
                $newRespond[$i]->setConfirmId($confirm->getId());
                $newRespond[$i]->setName('Респондент ' . $i);
                $newRespond[$i]->save();
            }
        } else {
            for ($i = ++$form->count_respond; $i < array_sum([$form->count_respond, $form->add_count_respond]); $i++ ) {
                $newRespond[$i] = self::getCreateModel($confirm);
                $newRespond[$i]->setConfirmId($confirm->getId());
                $newRespond[$i]->setName('Респондент ' . $i);
                $newRespond[$i]->save();
            }
        }
    }


    /**
     * @param ConfirmationInterface $confirm
     * @return RespondsProblem|RespondsGcp|RespondsMvp|RespondsSegment|bool
     */
    private static function getCreateModel(ConfirmationInterface $confirm)
    {
        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return new RespondsSegment();
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return new RespondsProblem();
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_GCP) {
            return new RespondsGcp();
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_MVP) {
            return new RespondsMvp();
        }
        return false;
    }
}