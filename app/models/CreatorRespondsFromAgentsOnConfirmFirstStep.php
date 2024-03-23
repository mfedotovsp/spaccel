<?php


namespace app\models;

use app\models\forms\FormCreateConfirm;
use app\models\interfaces\ConfirmationInterface;
use app\models\interfaces\CreateRespondsOnConfirmFirstStepInterface;
use app\models\interfaces\RespondsInterface;
use yii\base\Model;

/**
 * Класс создания новых респондентов из представителей на первом шаге подтверждения
 * Class CreatorRespondsFromAgentsOnConfirmFirstStep
 * @package app\models
 */
class CreatorRespondsFromAgentsOnConfirmFirstStep extends Model implements CreateRespondsOnConfirmFirstStepInterface
{

    /**
     * @param ConfirmationInterface $confirm
     * @param FormCreateConfirm $form
     */
    public function create(ConfirmationInterface $confirm, FormCreateConfirm $form): void
    {
        /**
         * @var RespondsInterface $respond
         */
        foreach ($confirm->hypothesis->respondsAgents as $respond) {
            if (!$respond->getContractorId()) {
                $respondConfirm = self::getCreateModel($confirm);
                $respondConfirm->setConfirmId($confirm->getId());
                $respondConfirm->setName($respond->getName());
                $respondConfirm->setParams([
                    'info_respond' => $respond->getInfoRespond(),
                    'place_interview' =>$respond->getPlaceInterview(),
                    'email' => $respond->getEmail()]);
                $respondConfirm->save();
            }
        }
    }


    /**
     * @param ConfirmationInterface $confirm
     * @return RespondsProblem|RespondsGcp|RespondsMvp|bool
     */
    private static function getCreateModel(ConfirmationInterface $confirm)
    {
        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return new RespondsProblem();
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_GCP) {
            return new RespondsGcp();
        }

        if($confirm->getStage() === StageConfirm::STAGE_CONFIRM_MVP) {
            return new RespondsMvp();
        }
        return false;
    }
}
