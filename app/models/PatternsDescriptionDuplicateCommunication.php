<?php


namespace app\models;


use app\models\interfaces\CommunicationsInterface;
use app\models\interfaces\ConfirmationInterface;
use yii\helpers\Html;


/**
 * Класс, который определяет какой шаблон коммуникации DuplicateCommunication отправить
 *
 * Class PatternsDescriptionDuplicateCommunication
 * @package app\models
 *
 * @property string $description
 */
class PatternsDescriptionDuplicateCommunication
{

    /**
     * Описание шаблона коммуникации
     * @var string
     */
    private $description;


    /**
     * @param CommunicationsInterface $source
     * @param User $adressee
     * @param int $type
     * @param false|Expertise $expertise
     * @return string
     */
    public static function getValue(CommunicationsInterface $source, User $adressee, int $type, $expertise): string
    {
        $model = new self();

        if (is_a($source, ProjectCommunications::class)) {
            /** @var ProjectCommunications $source */
            if ($type === TypesDuplicateCommunication::MAIN_ADMIN_TO_EXPERT) {
                return $model->getDescriptionDuplicateMainAdminToExpertCommunication($source, $adressee);
            }
            if ($type === TypesDuplicateCommunication::USER_ALLOWED_EXPERTISE) {
                return $model->getDescriptionDuplicateUserAllowedExpertiseCommunication($source);
            }
            if ($type === TypesDuplicateCommunication::USER_DELETE_STAGE_PROJECT) {
                return $model->getDescriptionDuplicateUserSoftDeleteStageProjectCommunication($source);
            }

            if (is_a($expertise, Expertise::class)) {

                if ($type === TypesDuplicateCommunication::EXPERT_COMPLETED_EXPERTISE) {
                    return $model->getDescriptionDuplicateExpertCompletedExpertiseCommunication($source, $adressee, $expertise);
                }
                if ($type === TypesDuplicateCommunication::EXPERT_UPDATE_DATA_COMPLETED_EXPERTISE) {
                    return $model->getDescriptionDuplicateExpertUpdateExpertiseCommunication($source, $adressee, $expertise);
                }
            }
        }
        return 'Извините! Произошла ошибка формирования уведомления, пожалуйста, сообщите об этом в техподдержку.';
    }


    /**
     * Получить описание шаблона коммуникации для трекера и проектанта,
     * когда гл.админ назначает или отзывает эксперта с проекта
     *
     * @param ProjectCommunications $source
     * @param User $adressee
     * @return string
     */
    private function getDescriptionDuplicateMainAdminToExpertCommunication(ProjectCommunications $source, User $adressee): string
    {

        if ($source->getType() === CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT) {

            if (User::isUserSimple($adressee->getUsername())) {

                $this->description = 'На ваш проект «' . $source->project->getProjectName().'» назначен эксперт ' . $source->expert->getUsername()
                    . '. Типы деятельности эксперта, по которым назначены экспертизы проекта: '
                    . ExpertType::getContent($source->typesAccessToExpertise->getTypes()) . '. В сообщениях создана беседа с экспертом.';

            } elseif (User::isUserAdmin($adressee->getUsername())) {

                $this->description = 'На проект «' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()])
                    . '» (проектант: ' . Html::a($source->project->user->getUsername(), ['/profile/index', 'id' => $source->project->getUserId()]) . ') назначен эксперт ' .
                    $source->expert->getUsername() . '. Типы деятельности эксперта, по которым назначены экспертизы проекта: '
                    . ExpertType::getContent($source->typesAccessToExpertise->getTypes()) . '.';
            }

        } elseif ($source->getType() === CommunicationTypes::MAIN_ADMIN_WITHDRAWS_EXPERT_FROM_PROJECT) {

            if (User::isUserSimple($adressee->getUsername())) {

                $this->description = 'С вашего проекта «' . $source->project->getProjectName() .'» отозван эксперт ' . $source->expert->getUsername() . '.';

            } elseif (User::isUserAdmin($adressee->getUsername())) {

                $this->description = 'С проекта «' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()])
                    . '» (проектант: ' . Html::a($source->project->user->getUsername(), ['/profile/index', 'id' => $source->project->getUserId()])
                    . ') отозван эксперт ' . $source->expert->getUsername() . '.';
            }
        }
        return $this->description;
    }


    /**
     * Получить описание шаблона коммуникации для трекера о том,
     * что проектант разрешил экспертизу по этапу проекта
     *
     * @param ProjectCommunications $source
     * @return string
     */
    private function getDescriptionDuplicateUserAllowedExpertiseCommunication(ProjectCommunications $source): string
    {
        if ($source->getType() === CommunicationTypes::USER_ALLOWED_PROJECT_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::PROJECT)
                . ': ' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_ALLOWED_SEGMENT_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::SEGMENT)
                . ': ' . Html::a($source->hypothesis->getName(), ['/segments/index', 'id' => $source->getProjectId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_ALLOWED_CONFIRM_SEGMENT_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::CONFIRM_SEGMENT)
                . ': ' . Html::a($source->hypothesis->segment->getName(), ['/confirm-segment/view', 'id' => $source->getHypothesisId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_ALLOWED_PROBLEM_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::PROBLEM)
                . ': ' . Html::a($source->hypothesis->getTitle(), ['/problems/index', 'id' => $source->hypothesis->getBasicConfirmId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_ALLOWED_CONFIRM_PROBLEM_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::CONFIRM_PROBLEM)
                . ': ' . Html::a($source->hypothesis->problem->getTitle(), ['/confirm-problem/view', 'id' => $source->getHypothesisId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_ALLOWED_GCP_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::GCP)
                . ': ' . Html::a($source->hypothesis->getTitle(), ['/gcps/index', 'id' => $source->hypothesis->getBasicConfirmId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_ALLOWED_CONFIRM_GCP_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::CONFIRM_GCP)
                . ': ' . Html::a($source->hypothesis->gcp->getTitle(), ['/confirm-gcp/view', 'id' => $source->getHypothesisId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_ALLOWED_MVP_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::MVP)
                . ': ' . Html::a($source->hypothesis->getTitle(), ['/mvps/index', 'id' => $source->hypothesis->getBasicConfirmId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_ALLOWED_CONFIRM_MVP_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::CONFIRM_MVP)
                . ': ' . Html::a($source->hypothesis->mvp->getTitle(), ['/confirm-mvp/view', 'id' => $source->getHypothesisId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_ALLOWED_BUSINESS_MODEL_EXPERTISE) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', разрешил эспертизу по этапу «' . $this->getStage(StageExpertise::BUSINESS_MODEL)
                . ': ' . Html::a($source->hypothesis->mvp->getTitle(), ['/business-model/index', 'id' => $source->getHypothesisId()]) . '»';
        }

        return $this->description;
    }


    /**
     * Получить описание шаблона коммуникации для трекера о том,
     * что проектант удалил этап проекта
     *
     * @param ProjectCommunications $source
     * @return string
     */
    private function getDescriptionDuplicateUserSoftDeleteStageProjectCommunication(ProjectCommunications $source): string
    {
        if ($source->getType() === CommunicationTypes::USER_DELETED_PROJECT) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', удалил проект «' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_DELETED_SEGMENT) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', удалил сегмент «' . Html::a($source->hypothesis->getName(), ['/segments/index', 'id' => $source->getProjectId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_DELETED_PROBLEM) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', удалил проблему сегмента «' . Html::a($source->hypothesis->getTitle(), ['/problems/index', 'id' => $source->hypothesis->getBasicConfirmId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_DELETED_GCP) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', удалил ценностное предложение «' . Html::a($source->hypothesis->getTitle(), ['/gcps/index', 'id' => $source->hypothesis->getBasicConfirmId()]) . '»';
        }

        if ($source->getType() === CommunicationTypes::USER_DELETED_MVP) {
            $this->description = 'Проектант, ' . $source->user->getUsername() . ', удалил MVP-продукт «' . Html::a($source->hypothesis->getTitle(), ['/mvps/index', 'id' => $source->hypothesis->getBasicConfirmId()]) . '»';
        }

        return $this->description;
    }


    /**
     * Описание уведомления для трекера и проектанта,
     * когда эксперт завершил экспертизу по этапу проекта
     *
     * @param ProjectCommunications $source
     * @param User $adressee
     * @param Expertise $expertise
     * @return string
     */
    private function getDescriptionDuplicateExpertCompletedExpertiseCommunication(ProjectCommunications $source, User $adressee, Expertise $expertise): string
    {
        if (User::isUserSimple($adressee->getUsername())) {

            if ($expertise->getStage() === StageExpertise::PROJECT) {
                $this->description = 'Эксперт, ' . $source->expert->getUsername() . ', завершил экспертизу по этапу «' . $this->getStage($expertise->getStage())
                    . ': ' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '». Тип деятельности эксперта: ' . ExpertType::getListTypes()[$expertise->getTypeExpert()] . '.';
            }else {
                $this->description = 'Эксперт, ' . $source->expert->getUsername() . ', завершил экспертизу по этапу «' . $this->getStage($expertise->getStage())
                    . ': ' . $this->getLinkStage($expertise) . '». Проект: «' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '». Тип деятельности эксперта: ' . ExpertType::getListTypes()[$expertise->getTypeExpert()] . '.';
            }

        } elseif (User::isUserAdmin($adressee->getUsername())) {

            if ($expertise->getStage() === StageExpertise::PROJECT) {
                $this->description = 'Эксперт, ' . $source->expert->getUsername() . ', завершил экспертизу по этапу «' . $this->getStage($expertise->getStage())
                    . ': ' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '». Тип деятельности эксперта: ' . ExpertType::getListTypes()[$expertise->getTypeExpert()]
                    . '. Проектант: ' . Html::a($source->project->user->getUsername(), ['/profile/index', 'id' => $source->project->getUserId()]) . '.';
            }else {
                $this->description = 'Эксперт, ' . $source->expert->getUsername() . ', завершил экспертизу по этапу «' . $this->getStage($expertise->getStage())
                    . ': ' . $this->getLinkStage($expertise) . '». Проект: «' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '». Тип деятельности эксперта: ' . ExpertType::getListTypes()[$expertise->getTypeExpert()]
                    . '. Проектант: ' . Html::a($source->project->user->getUsername(), ['/profile/index', 'id' => $source->project->getUserId()]) . '.';
            }
        }
        return $this->description;
    }


    /**
     * Описание уведомления для трекера и проектанта,
     * когда эксперт обновил данные раннее завершенной экспертизы
     *
     * @param ProjectCommunications $source
     * @param User $adressee
     * @param Expertise $expertise
     * @return string
     */
    private function getDescriptionDuplicateExpertUpdateExpertiseCommunication(ProjectCommunications $source, User $adressee, Expertise $expertise): string
    {
        if (User::isUserSimple($adressee->getUsername())) {

            if ($expertise->getStage() === StageExpertise::PROJECT) {
                $this->description = 'Эксперт, ' . $source->expert->getUsername() . ', обновил данные ранее завершенной экспертизы по этапу «' . $this->getStage($expertise->getStage())
                    . ': ' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '». Тип деятельности эксперта: ' . ExpertType::getListTypes()[$expertise->getTypeExpert()] . '.';
            }else {
                $this->description = 'Эксперт, ' . $source->expert->getUsername() . ', обновил данные ранее завершенной экспертизы по этапу «' . $this->getStage($expertise->getStage())
                    . ': ' . $this->getLinkStage($expertise) . '». Проект: «' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '». Тип деятельности эксперта: ' . ExpertType::getListTypes()[$expertise->getTypeExpert()] . '.';
            }

        } elseif (User::isUserAdmin($adressee->getUsername())) {

            if ($expertise->getStage() === StageExpertise::PROJECT) {
                $this->description = 'Эксперт, ' . $source->expert->getUsername() . ', обновил данные ранее завершенной экспертизы по этапу «' . $this->getStage($expertise->getStage())
                    . ': ' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '». Тип деятельности эксперта: ' . ExpertType::getListTypes()[$expertise->getTypeExpert()]
                    . '. Проектант: ' . Html::a($source->project->user->getUsername(), ['/profile/index', 'id' => $source->project->getUserId()]) . '.';
            }else {
                $this->description = 'Эксперт, ' . $source->expert->getUsername() . ', обновил данные ранее завершенной экспертизы по этапу «' . $this->getStage($expertise->getStage())
                    . ': ' . $this->getLinkStage($expertise) . '». Проект: «' . Html::a($source->project->getProjectName(), ['/projects/index', 'id' => $source->project->getUserId()]) . '». Тип деятельности эксперта: ' . ExpertType::getListTypes()[$expertise->getTypeExpert()]
                    . '. Проектант: ' . Html::a($source->project->user->getUsername(), ['/profile/index', 'id' => $source->project->getUserId()]) . '.';
            }
        }
        return $this->description;
    }


    /**
     * Получить название этапа экспертизы проекта
     *
     * @param int $stage
     * @return string
     */
    private function getStage(int $stage): string
    {
        switch ($stage) {
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
     * Получить объект проекта
     *
     * @param Expertise $expertise
     * @return Projects
     */
    private function getProject(Expertise $expertise): Projects
    {
        $stageClass = StageExpertise::getClassByStage(StageExpertise::getList()[$expertise->getStage()]);
        if (!$stageClass instanceof ConfirmationInterface) {
            $hypothesis = $stageClass::findOne($expertise->getStageId());
        } else {
            $hypothesis = $stageClass::findOne($expertise->getStageId())->getHypothesis();
        }
        return $hypothesis->project;
    }


    /**
     * Получить ссылку на этап проекта,
     * по которому была проведена экспертиза
     *
     * @param Expertise $expertise
     * @return string
     */
    public function getLinkStage(Expertise $expertise): string
    {
        $stageClass = StageExpertise::getClassByStage(StageExpertise::getList()[$expertise->getStage()]);
        $stageObj = $stageClass::findOne($expertise->getStageId());

        if ($stageObj instanceof Segments) {
            return Html::a($stageObj->getName(), ['/segments/index', 'id' => $stageObj->getProjectId()]);
        }

        if ($stageObj instanceof ConfirmSegment) {
            return Html::a($stageObj->segment->getName(), ['/confirm-segment/view', 'id' => $stageObj->getId()]);
        }

        if ($stageObj instanceof Problems) {
            return Html::a($stageObj->getTitle(), ['/problems/index', 'id' => $stageObj->getConfirmSegmentId()]);
        }

        if ($stageObj instanceof ConfirmProblem) {
            return Html::a($stageObj->problem->getTitle(), ['/confirm-problem/view', 'id' => $stageObj->getId()]);
        }

        if ($stageObj instanceof Gcps) {
            return Html::a($stageObj->getTitle(), ['/gcps/index', 'id' => $stageObj->getConfirmProblemId()]);
        }

        if ($stageObj instanceof ConfirmGcp) {
            return Html::a($stageObj->gcp->getTitle(), ['/confirm-gcp/view', 'id' => $stageObj->getId()]);
        }

        if ($stageObj instanceof Mvps) {
            return Html::a($stageObj->getTitle(), ['/mvps/index', 'id' => $stageObj->getConfirmGcpId()]);
        }

        if ($stageObj instanceof ConfirmMvp) {
            return Html::a($stageObj->mvp->getTitle(), ['/confirm-mvp/view', 'id' => $stageObj->getId()]);
        }

        if ($stageObj instanceof BusinessModel) {
            return Html::a('бизнес-модель для ' . $stageObj->mvp->getTitle(), ['/business-model/index', 'id' => $stageObj->getConfirmMvpId()]);
        }
        return '';
    }
}