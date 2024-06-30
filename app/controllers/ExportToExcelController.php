<?php

namespace app\controllers;

use app\models\BusinessModel;
use app\models\ClientSettings;
use app\models\ContractorProject;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\User;
use Yii;
use yii\data\ArrayDataProvider;

/**
 * Class ExportToExcelController
 */
class ExportToExcelController extends AppUserPartController
{
    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());
        $currentClientUser = $currentUser->clientUser;

        if ($action->id === 'project') {

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => (int)Yii::$app->request->get('id')])
                ->one();

            if (!$project) {
                PatternHttpException::noData();
            }

            $user = $project->user;
            if (!$user) {
                PatternHttpException::noData();
            }

            if (User::isUserSimple($currentUser->getUsername()) && $user->getId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $user->clientUser;

                if ($currentClientUser->getClientId() === $modelClientUser->getClientId()) {
                    return parent::beforeAction($action);
                }

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE && !User::isUserAdminCompany($currentUser->getUsername())) {
                    return parent::beforeAction($action);
                }
            }

            PatternHttpException::noAccess();
        } else {
            return parent::beforeAction($action);
        }
    }

    public function actionProject(int $id): string
    {
        /** @var $project Projects */
        $project = Projects::findOne($id);
        $segments = $project->segments;
        $businessModels = [];
        foreach ($segments as $segment) {
            if ($segment->problems) {
                foreach ($segment->problems as $problem) {
                    if ($problem->gcps) {
                        foreach ($problem->gcps as $gcp) {
                            if ($gcp->mvps) {
                                foreach ($gcp->mvps as $mvp) {
                                    if ($mvp->businessModel) {
                                        $businessModels[] = $mvp->businessModel;
                                    } else {
                                        $businessModel = new BusinessModel();
                                        $businessModel->setMvpId($mvp->getId());
                                        $businessModel->setGcpId($gcp->getId());
                                        $businessModel->setProblemId($problem->getId());
                                        $businessModel->setSegmentId($segment->getId());
                                        $businessModels[] = $businessModel;
                                    }
                                }
                            } else {
                                $businessModel = new BusinessModel();
                                $businessModel->setGcpId($gcp->getId());
                                $businessModel->setProblemId($problem->getId());
                                $businessModel->setSegmentId($segment->getId());
                                $businessModels[] = $businessModel;
                            }
                        }
                    } else {
                        $businessModel = new BusinessModel();
                        $businessModel->setProblemId($problem->getId());
                        $businessModel->setSegmentId($segment->getId());
                        $businessModels[] = $businessModel;
                    }
                }
            } else {
                $businessModel = new BusinessModel();
                $businessModel->setSegmentId($segment->getId());
                $businessModels[] = $businessModel;
            }
        }

        $cellsMerged = [];
        $cellsMerged['project'] = count($businessModels) > 1 ? [2, (count($businessModels) + 1)] : [];
        foreach ($businessModels as $k => $businessModel) {
            if ($k !== 0) {
                if ($businessModel->getSegmentId() === $businessModels[$k-1]->getSegmentId()) {
                    $cellsMerged['segments'][] = [$k+1, $k+2];
                }
                if ($businessModel->problem_id && $businessModel->problem_id === $businessModels[$k-1]->problem_id) {
                    $cellsMerged['problems'][] = [$k+1, $k+2];
                }
                if ($businessModel->gcp_id && $businessModel->gcp_id === $businessModels[$k-1]->gcp_id) {
                    $cellsMerged['gcps'][] = [$k+1, $k+2];
                }
            }
        }

        $dataStringProject = $this->getDataStringProject($project);
        $project_filename = 'ИтоговаяТаблицаПроекта_' . str_replace(' ', '_', $project->getProjectName());
        $dataProvider = new ArrayDataProvider(['allModels' => $businessModels, 'pagination' => false, 'sort' => false]);
        return $this->render('result-export',[
            'dataProvider' => $dataProvider,
            'project' => $project,
            'dataStringProject' => $dataStringProject,
            'cellsMerged' => $cellsMerged,
            'project_filename' => $project_filename,
        ]);
    }

    /**
     * @param Projects $project
     * @return string
     */
    private function getListAuthors(Projects $project): string
    {
        $listAuthors = '';
        foreach ($project->authors as $k => $author) {
            $listAuthors .= 'Сотрудник №'.($k+1) . '
ФИО: ' . $author->getFio() . '
Роль в проекте: ' . $author->getRole() . '
Опыт работы: ' . $author->getExperience();
            if (($k+1) !== count($project->authors)) {
                $listAuthors .= '

';
            }
        }

        return $listAuthors;
    }


    /**
     * @param Projects $project
     * @return string
     */
    private function getListContractors(Projects $project): string
    {
        $contractorProjects = ContractorProject::findAll([
            'project_id' => $project->getId(),
            'deleted_at' => null
        ]);

        $contractors = [];
        $contractorIds = [];
        foreach ($contractorProjects as $contractorProject) {
            if (!in_array($contractorProject->getContractorId(), $contractorIds, true)) {
                $contractorIds[] = $contractorProject->getContractorId();
                $contractors[$contractorProject->getContractorId()]['username'] = $contractorProject->contractor->getUsername();
                $contractors[$contractorProject->getContractorId()]['activity'] = $contractorProject->activity->getTitle();
            } else {
                $contractors[$contractorProject->getContractorId()]['activity'] .= ', ' . $contractorProject->activity->getTitle();
            }
        }

        $listContractors = '';
        $j = 0;
        foreach ($contractors as $contractor) {
            $j++;
            $listContractors .= 'Сотрудник №'.$j . '
Логин: ' . $contractor['username'] . '
Вид деятельности: ' . $contractor['activity'];
            if ($j !== count($project->authors)) {
                $listContractors .= '

';
            }
        }

        return $listContractors;
    }


    /**
     * @param Projects $project
     * @return string
     */
    private function getDataStringProject(Projects $project): string
    {
        $dataProject = [
            'Сокращенное наименование проекта' => $project->getProjectName(),
            'Полное наименование проекта' => $project->getProjectFullname(),
            'Описание проекта' => $project->getDescription(),
            'Цель проекта' => $project->getPurposeProject(),
            'Результат интеллектуальной деятельности' => $project->getRid(),
            'Суть результата интеллектуальной деятельности' => $project->getCoreRid(),
            'Наименование патента' => $project->getPatentName(),
            'Номер патента' => $project->getPatentNumber(),
            'Дата получения патента' => $project->getPatentDate() ? date('d.m.Y', $project->getPatentDate()) : '',
            'Авторы проекта' => strip_tags(nl2br($this->getListAuthors($project))),
            'Исполнители проекта' => $this->getListContractors($project),
            'На какой технологии основан проект' => $project->getTechnology(),
            'Макет базовой технологии' => $project->getLayoutTechnology(),
            'Зарегистрированное юр. лицо' => $project->getRegisterName(),
            'Дата регистрации' => $project->getRegisterDate() ? date('d.m.Y', $project->getRegisterDate()) : '',
            'Адрес сайта' => $project->getSite(),
            'Инвестор' => $project->getInvestName(),
            'Сумма инвестиций' => $project->getInvestAmount() ? number_format($project->getInvestAmount(), 0, '', ' ') . ' руб.' : '',
            'Дата получения инвестиций' => $project->getInvestDate() ? date('d.m.Y', $project->getInvestDate()) : '',
            'Мероприятие, на котором проект анонсирован впервые' => $project->getAnnouncementEvent(),
            'Дата анонсирования проекта' => $project->getDateOfAnnouncement() ? date('d.m.Y', $project->getDateOfAnnouncement()) : '',
        ];

        $dataStringProject = '';
        foreach ($dataProject as $key => $value) {
            if ($value !== '') {
                if (in_array($key, ['Авторы проекта', 'Исполнители проекта'], true)) {
                    $dataStringProject .= $key . ':

' . $value . '

';
                } else {
                    $dataStringProject .= $key . ':
' . $value . '

';
                }
            }
        }

        return $dataStringProject;
    }
}
