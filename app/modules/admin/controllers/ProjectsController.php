<?php

namespace app\modules\admin\controllers;

use app\models\Client;
use app\models\ClientSettings;
use app\models\PatternHttpException;
use app\models\Projects;
use app\models\SortForm;
use app\models\User;
use app\modules\admin\models\form\SearchForm;
use Yii;
use yii\data\Pagination;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Контроллер с методами для получения информации о созданных проектах
 *
 * Class ProjectsController
 * @package app\modules\admin\controllers
 */
class ProjectsController extends AppAdminController
{

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());
        $currentClientUser = $currentUser->clientUser;

        if ($action->id === 'index') {

            if (User::isUserDev($currentUser->getUsername()) || User::isUserMainAdmin($currentUser->getUsername())) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        }elseif ($action->id === 'group') {

            $user = User::findOne((int)Yii::$app->request->get('id'));
            if (!$user) {
                PatternHttpException::noData();
            }

            if ($user->getId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserDev($currentUser->getUsername()) || User::isUserMainAdmin($currentUser->getUsername())) {

                $modelClientUser = $user->clientUser;

                if ($currentClientUser->getClientId() === $modelClientUser->getClientId()) {
                    return parent::beforeAction($action);
                }

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
                    return parent::beforeAction($action);
                }
            }

            PatternHttpException::noAccess();

        } elseif ($action->id === 'client') {

            $client = Client::findOne((int)Yii::$app->request->get('id'));
            if (!$client) {
                PatternHttpException::noData();
            }

            if (User::isUserDev($currentUser->getUsername()) || User::isUserMainAdmin($currentUser->getUsername())) {

                if ($currentClientUser->getClientId() === $client->getId()) {
                    return parent::beforeAction($action);
                }

                if ($client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE) {
                    return parent::beforeAction($action);
                }
            }

            PatternHttpException::noAccess();

        } else{
            return parent::beforeAction($action);
        }


    }

    /**
     * Страница сводной таблицы по проетам,
     * которые относятся к организации
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $sortModel = new SortForm();
        $searchModel = new SearchForm();
        $show_count_projects = [
            10 => 'по 10 проектов',
            20 => 'по 20 проектов',
            30 => 'по 30 проектов'
        ];

        return $this->render('index', [
            'sortModel' => $sortModel,
            'show_count_projects' => $show_count_projects,
            'searchModel' => $searchModel,
            'pageClientProjects' => false
        ]);
    }


    /**
     * Страница сводной таблицы по проектам,
     * которые курирует трекер
     *
     * @param int $id
     * @return string
     */
    public function actionGroup(int $id): string
    {
        $tracker = User::findOne($id);
        Yii::$app->view->title = 'Портфель проектов трекера «' . $tracker->getUsername(). '»';
        $sortModel = new SortForm();
        $searchModel = new SearchForm();
        $show_count_projects = [
            10 => 'по 10 проектов',
            20 => 'по 20 проектов',
            30 => 'по 30 проектов'
        ];

        return $this->render('index', [
            'sortModel' => $sortModel,
            'show_count_projects' => $show_count_projects,
            'searchModel' => $searchModel,
            'pageClientProjects' => false
        ]);
    }


    /**
     * Страница сводной таблицы по проектам,
     * которые относятся к организации с указанным id
     *
     * @param int $id
     * @return string
     */
    public function actionClient(int $id): string
    {
        $client = Client::findOne($id);
        Yii::$app->view->title = 'Портфель проектов организации «' . $client->getName() . '»';
        $sortModel = new SortForm();
        $searchModel = new SearchForm();
        $show_count_projects = [
            10 => 'по 10 проектов',
            20 => 'по 20 проектов',
            30 => 'по 30 проектов'
        ];

        return $this->render('index', [
            'sortModel' => $sortModel,
            'show_count_projects' => $show_count_projects,
            'searchModel' => $searchModel,
            'pageClientProjects' => true
        ]);
    }


    /**
     * Получение сводной таблицы по проектам
     *
     * @param int|string $id
     * @param int $page
     * @param int $per_page
     * @param string $search
     * @return array|bool
     */
    public function actionGetResultProjects($id, int $page, int $per_page, string $search = '')
    {
        if(Yii::$app->request->isAjax) {

            if ($id === 'all_projects') {
                // вывести все проекты организации
                $user = User::findOne(Yii::$app->user->getId());
                $clientUser = $user->clientUser;
                $client = $clientUser->client;
                $query = Projects::find()->with('user')
                    ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
                    ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
                    ->andWhere(['client_user.client_id' => $client->getId()]);

            } else {
                // вывести проекты, которые курирует трекер
                $query = Projects::find()
                    ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
                    ->andWhere(['user.id_admin' => $id])->orderBy(['id' => SORT_DESC]);
            }

            if ($search !== '') {
                $query ->andWhere(['or',
                    ['like', 'project_name', trim($search)],
                    ['like', 'project_fullname', trim($search)]
                ]);
            }

            $query->orderBy(['id' => SORT_DESC]);
            $pages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => $per_page]);
            $pages->pageSizeParam = false; //убираем параметр $per-page
            $projects = $query->offset($pages->offset)->limit($per_page)->all();

            $response = ['renderAjax' => $this->renderAjax('_index_ajax', ['projects' => $projects, 'pages' => $pages])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Получение сводной таблицы по проектам,
     * которые относятся к организации с указанным id
     *
     * @param int $id
     * @param int $page
     * @param int $per_page
     * @param string $search
     * @return array|bool
     */
    public function actionGetResultClientProjects(int $id, int $page, int $per_page, string $search = '')
    {
        $query = Projects::find()
            ->leftJoin('user', '`user`.`id` = `projects`.`user_id`')
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['client_user.client_id' => $id]);

        if ($search !== '') {
            $query ->andWhere(['or',
                ['like', 'project_name', trim($search)],
                ['like', 'project_fullname', trim($search)]
            ]);
        }

        $query->orderBy(['id' => SORT_DESC]);
        $pages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => $per_page, ]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $projects = $query->offset($pages->offset)->limit($per_page)->all();

        if(Yii::$app->request->isAjax) {

            $response = ['renderAjax' => $this->renderAjax('_index_ajax', ['projects' => $projects, 'pages' => $pages])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }
}