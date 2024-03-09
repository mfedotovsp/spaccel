<?php


namespace app\modules\admin\controllers;

use app\models\ClientUser;
use app\models\CommunicationTypes;
use app\models\EnableExpertise;
use app\models\PatternHttpException;
use app\models\ProjectCommunications;
use app\models\Projects;
use app\models\User;
use app\modules\admin\models\form\SearchForm;
use app\modules\admin\models\form\SearchFormExperts;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;
use Yii;

class ExpertiseController extends AppAdminController
{

    /**
     * Количество заданий
     * на экспертизу на странице
     */
    public const TASKS_PAGE_SIZE = 20;

    public $layout = '@app/modules/admin/views/layouts/users';


    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {

        if (in_array($action->id, ['index', 'tasks'])) {

            if (User::isUserDev(Yii::$app->user->identity['username']) || User::isUserMainAdmin(Yii::$app->user->identity['username'])) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } else{
            return parent::beforeAction($action);
        }

    }


    /**
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }


    /**
     * Страница проектов
     * на экспертизу
     *
     * @param int $page
     * @return array|string
     */
    public function actionTasks(int $page = 1)
    {
        $clientUser = ClientUser::findOne(['user_id' => Yii::$app->user->getId()]);
        $client = $clientUser->client;
        $searchForm = new SearchForm();
        $pageSize = self::TASKS_PAGE_SIZE;
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        $cache->cachePath = '../runtime/cache/forms/user-'.Yii::$app->user->getId().'/searchFormExpertiseTasks/';
        $cache_form_search_tasks = $cache->get('searchFormExpertiseTasks');
        if ($cache_form_search_tasks) {
            $searchForm->search = trim($cache_form_search_tasks['SearchForm']['search']);
        }

        $query = $searchForm->search;
        if (5 <= mb_strlen($query)) {

            $query_projects = Projects::find(false)
                ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
                ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
                ->andWhere(['client_user.client_id' => $client->getId()])
                ->andWhere(['enable_expertise' => EnableExpertise::ON])
                ->andWhere(['or',
                    ['like', 'project_name', $query],
                    ['like', 'user.username', $query],
                ])->orderBy(['id' => SORT_DESC]);

        } else {

            $query_projects = Projects::find(false)
                ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
                ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
                ->andWhere(['client_user.client_id' => $client->getId()])
                ->andWhere(['enable_expertise' => EnableExpertise::ON])
                ->orderBy(['id' => SORT_DESC]);

        }
        $pages = new Pagination(['totalCount' => $query_projects->count(), 'page' => ($page - 1), 'pageSize' => $pageSize]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $projects = $query_projects->offset($pages->offset)->limit($pageSize)->all();

        if (Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('ajax_search_tasks', [
                'projects' => $projects, 'pages' => $pages, 'search' => true
                ]), 'projects' => $projects];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;

        }

        return $this->render('tasks', [
            'projects' => $projects,
            'pages' => $pages,
            'searchForm' => $searchForm,
        ]);
    }


    public function actionResultTasks(int $page = 1)
    {
        $clientUser = ClientUser::findOne(['user_id' => Yii::$app->user->getId()]);
        $client = $clientUser->client;
        $pageSize = self::TASKS_PAGE_SIZE;

        $query_projects = Projects::find(false)
            ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['client_user.client_id' => $client->getId()])
            ->orderBy(['id' => SORT_DESC]);

        $countProjects = $query_projects->count();

        $countEnableProjects = Projects::find(false)
            ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['client_user.client_id' => $client->getId()])
            ->andWhere(['enable_expertise' => EnableExpertise::ON])
            ->count();

        $pages = new Pagination(['totalCount' => $query_projects->count(), 'page' => ($page - 1), 'pageSize' => $pageSize]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $projects = $query_projects->offset($pages->offset)->limit($pageSize)->all();

        if (Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('ajax_search_tasks', [
                    'projects' => $projects, 'pages' => $pages, 'search' => true
                ]), 'projects' => $projects];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;

        }

        return $this->render('result-tasks', [
            'countProjects' => $countProjects,
            'countEnableProjects' => $countEnableProjects,
            'projects' => $projects,
            'pages' => $pages,
        ]);
    }


    public function actionGetResultTask(int $id)
    {
        if (Yii::$app->request->isAjax) {

            /** @var $project Projects */
            $project = Projects::find(false)
                ->andWhere(['id' => $id])
                ->one();

            $communicationsMainAdminAskExpert = ProjectCommunications::findAll([
                'project_id' => $id, 'type' => CommunicationTypes::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE
            ]);

            $communicationsMainAdminAppointExpert = ProjectCommunications::findAll([
                'project_id' => $id, 'type' => CommunicationTypes::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT
            ]);

            $response = [
                'renderAjax' => $this->renderAjax('ajax_result_task', [
                    'project' => $project, 'communicationsMainAdminAskExpert' => $communicationsMainAdminAskExpert,
                    'communicationsMainAdminAppointExpert' => $communicationsMainAdminAppointExpert
                ])
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Сохранение кэша поиска
     * проекта на экспертизу
     */
    public function actionSaveCacheSearchForm (): void
    {
        $cache = Yii::$app->cache; //Обращаемся к кэшу приложения
        $data = $_POST; //Массив, который будем записывать в кэш

        if(Yii::$app->request->isAjax) {

            $cache->cachePath = '../runtime/cache/forms/user-'.Yii::$app->user->getId().'/searchFormExpertiseTasks/';
            $key = 'searchFormExpertiseTasks'; //Формируем ключ
            $cache->set($key, $data, 3600*24*30); //Создаем файл кэша на 30дней
        }
    }


    /**
     * Получить сводную
     * таблицу проекта
     *
     * @param int $id
     * @return array|bool
     */
    public function actionGetProjectSummaryTable(int $id)
    {
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        if(Yii::$app->request->isAjax) {

            $response = [
                'renderAjax' => $this->renderAjax('ajax_project_summary_table', [
                'project' => $project]), 'project_id' => $project->getId()];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Получить форму
     * поиска экспертов
     *
     * @param int $id
     * @return array|bool
     */
    public function actionGetSearchFormExperts(int $id)
    {
        /** @var $project Projects */
        $project = Projects::find(false)
            ->andWhere(['id' => $id])
            ->one();

        $searchFormExperts = new SearchFormExperts();

        if(Yii::$app->request->isAjax) {

            $response = ['renderAjax' => $this->renderAjax('ajax_get_search_form_expert', [
                'project' => $project, 'searchFormExperts' => $searchFormExperts]), 'project_id' => $project->id];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Вывести результат
     * поиска экспертов
     *
     * @param int $project_id
     * @return array|bool
     */
    public function actionSearchExperts(int $project_id)
    {
        if(Yii::$app->request->isAjax) {

            $experts = SearchFormExperts::search();

            $response = ['renderAjax' => $this->renderAjax('result_search_experts_ajax', ['experts' => $experts, 'project_id' => $project_id])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|bool
     */
    public function actionGetExpertiseByProject(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $response = ['renderAjax' => $this->renderAjax('ajax_get_expertise_by_project', ['project_id' => $id]), 'project_id' => $id];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }
}