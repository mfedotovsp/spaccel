<?php


namespace app\models;

use app\models\forms\CreateRespondProblemForm;
use app\models\forms\CreateRespondSegmentForm;
use app\models\forms\CreateRespondGcpForm;
use app\models\forms\CreateRespondMvpForm;
use app\models\interfaces\ConfirmationInterface;
use app\models\interfaces\RespondsInterface;
use yii\base\ErrorException;
use yii\base\Model;
use yii\web\NotFoundHttpException;

/**
 * Редактор количества респондентов на этапах подтверждения гипотез
 *
 * Class EditorCountResponds
 * @package app\models
 */
class EditorCountResponds extends Model
{

    /**
     * @param ConfirmationInterface $confirm
     * @return void
     * @throws ErrorException
     * @throws NotFoundHttpException
     */
    public function edit(ConfirmationInterface $confirm): void
    {
        /** @var RespondsInterface[] $responds */
        $responds = $confirm->responds;
        $countResponds = count($responds);

        if (($countResponds) < $confirm->getCountRespond()){
            for ($count = $countResponds; $count < $confirm->getCountRespond(); $count++ )
            {
                $newRespond[$count] = self::getCreateForm($confirm);
                $newRespond[$count]->setConfirmId($confirm->getId());
                $newRespond[$count]->setName('Респондент ' . ($count+1));
                $newRespond[$count]->create();
            }
        }else{
            $minus = $countResponds - $confirm->getCountRespond();
            $respondsWithoutContractors = [];
            foreach ($responds as $respond) {
                if (!$respond->getContractorId()) {
                    $respondsWithoutContractors[] = $respond;
                }
            }
            $respondsWithoutContractors = array_reverse($respondsWithoutContractors);
            foreach ($respondsWithoutContractors as $i => $respondWithoutContractor) {
                if ($i < $minus) {
                    $respondWithoutContractor->delete();
                }
            }
        }

    }


    /**
     * @param ConfirmationInterface $confirm
     * @return CreateRespondProblemForm|CreateRespondSegmentForm|CreateRespondGcpForm|CreateRespondMvpForm|bool
     */
    private static function getCreateForm(ConfirmationInterface $confirm)
    {
        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_SEGMENT) {
            return new CreateRespondSegmentForm($confirm);
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_PROBLEM) {
            return new CreateRespondProblemForm($confirm);
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_GCP) {
            return new CreateRespondGcpForm($confirm);
        }

        if ($confirm->getStage() === StageConfirm::STAGE_CONFIRM_MVP) {
            return new CreateRespondMvpForm($confirm);
        }
        return false;
    }

}