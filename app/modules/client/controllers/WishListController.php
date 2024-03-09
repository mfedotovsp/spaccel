<?php

namespace app\modules\client\controllers;

use app\models\Client;
use app\models\forms\FormFilterRequirement;
use app\models\PatternHttpException;
use app\models\ReasonRequirementWishList;
use app\models\RequirementWishList;
use app\models\User;
use app\models\WishList;
use app\modules\admin\models\form\FormCreateWishList;
use app\modules\admin\models\form\FormUpdateWishList;
use Throwable;
use Yii;
use yii\data\Pagination;
use yii\db\StaleObjectException;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

class WishListController extends AppClientController
{
    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        if (User::isUserAdminCompany(Yii::$app->user->identity['username'])) {
            return parent::beforeAction($action);
        }

        PatternHttpException::noAccess();
    }

    /**
     * @param int $page
     * @return array|string
     */
    public function actionIndex(int $page = 1)
    {
        $user = User::findOne(Yii::$app->user->getId());
        $client = $user->clientUser->client;

        $limit = 20;
        $query = $client->findWishListsForPagination();
        $filters = new FormFilterRequirement();

        if(Yii::$app->request->isAjax) {

            $queryRequirement = RequirementWishList::find();
            if ($filters->load(Yii::$app->request->post())) {
                if ($filters->getRequirement()) {
                    $queryRequirement = $queryRequirement->andWhere(['like', 'requirement', $filters->getRequirement()]);
                }
                if ($filters->getReason()) {
                    $queryRequirement = $queryRequirement->innerJoin('reason_requirement_wish_list', '`reason_requirement_wish_list`.`requirement_wish_list_id` = `requirement_wish_list`.`id`')
                        ->andWhere(['like', 'reason_requirement_wish_list.reason', $filters->getReason()]);
                }
                if ($filters->getExpectedResult()) {
                    $queryRequirement = $queryRequirement->andWhere(['like', 'requirement_wish_list.expected_result', $filters->getExpectedResult()]);
                }
                if ($filters->getFieldOfActivity()) {
                    $query = $query->andWhere(['like', 'company_field_of_activity', $filters->getFieldOfActivity()]);
                }
                if ($filters->getSortOfActivity()) {
                    $query = $query->andWhere(['like', 'company_sort_of_activity', $filters->getSortOfActivity()]);
                }
                if ($filters->getSize()) {
                    $query = $query->andWhere(['size' => (int)$filters->getSize()]);
                }
                if ($filters->getLocationId()) {
                    $query = $query->andWhere(['location_id' => (int)$filters->getLocationId()]);
                }
                if ($filters->getTypeCompany()) {
                    $query = $query->andWhere(['type_company' => (int)$filters->getTypeCompany()]);
                }
                if ($filters->getTypeProduction()) {
                    $query = $query->andWhere(['type_production' => (int)$filters->getTypeProduction()]);
                }
                if ($filters->getClientId()) {
                    $query = $query->andWhere(['client_id' => (int)$filters->getClientId()]);
                }
                if ($filters->getStartDate()) {
                    $query = $query->andWhere(['>=', 'completed_at', strtotime($filters->getStartDate())]);
                }
                if ($filters->getEndDate()) {
                    $query = $query->andWhere(['<=', 'completed_at', strtotime($filters->getEndDate())]);
                }

                $wishListIds = $queryRequirement
                    ->select('requirement_wish_list.wish_list_id wish_list_id')
                    ->distinct()
                    ->asArray()
                    ->all();

                $resultWishListIds = [];
                foreach ($wishListIds as $wishListId) {
                    foreach ($wishListId as $item) {
                        $resultWishListIds[] = $item;
                    }
                }

                $query = $query->andWhere(['in', 'id', $resultWishListIds]);
            }

            $pages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => $limit]);
            $pages->pageSizeParam = false; //убираем параметр $per-page
            $models = $query->offset($pages->offset)->limit($limit)->all();

            $response = [
                'renderAjax' => $this->renderAjax('index_ajax', [
                    'models' => $models,
                    'pages' => $pages,
                    'clientId' => $client->getId()
                ])
            ];

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        $pages = new Pagination(['totalCount' => $query->count(), 'page' => ($page - 1), 'pageSize' => $limit]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $models = $query->offset($pages->offset)->limit($limit)->all();
        $listClient = Client::find()
            ->andWhere(['in', 'id', array_column($models, 'client_id')])
            ->all();

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'clientId' => $client->getId(),
            'filters' => $filters,
            'listClient' => $listClient
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new FormCreateWishList();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->create()) {
                return $this->redirect('/client/wish-list/new');
            }
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }


    /**
     * @return string
     */
    public function actionNew(): string
    {
        $user = User::findOne(Yii::$app->user->getId());
        $models = WishList::findAll(['completed_at' => null, 'client_id' => $user->clientUser->getClientId()]);

        return $this->render('new', [
            'models' => array_reverse($models)
        ]);
    }

    /**
     * @param int $id
     * @return array|false
     */
    public function actionComplete(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $model = WishList::findOne($id);
            if (count($model->requirements) > 0 && !$model->getCompletedAt()) {
                $model->setCompletedAt(time());
                if ($model->save()) {
                    $response = [
                        'success' => true,
                        'renderAjax' => $this->renderAjax('new_ajax', [
                            'models' => array_reverse(WishList::findAll(['completed_at' => null]))
                        ])
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }

    /**
     * @param int $id
     * @return string|Response
     */
    public function actionUpdate(int $id)
    {
        $model = new FormUpdateWishList($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->update()) {
                return $this->redirect('/client/wish-list/new');
            }
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     */
    public function actionAddRequirement(int $id)
    {
        $model = new RequirementWishList();
        $model->setWishListId($id);

        if ($model->load(Yii::$app->request->post())) {
            $countFullReasons = 0;
            $reasons = $_POST['RequirementWishList']['reasons'];
            foreach ($reasons as $reason) {
                if (trim($reason['reason']) !== '') {
                    $countFullReasons++;
                }
            }
            if ($countFullReasons === count($reasons)) {
                if ($model->create($id) === true) {
                    return $this->redirect(['/client/wish-list/update', 'id' => $id]);
                }
            }
        }

        return $this->render('add_requirement', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return string|Response
     */
    public function actionRequirementUpdate(int $id)
    {
        $model = RequirementWishList::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            $countFullReasons = 0;
            $reasons = $_POST['RequirementWishList']['reasons'];
            foreach ($reasons as $reason) {
                if (trim($reason['reason']) !== '') {
                    $countFullReasons++;
                }
            }
            if ($countFullReasons === count($reasons)) {
                if ($model->updateRecord() === true) {
                    return $this->redirect(['/client/wish-list/update', 'id' => $model->getWishListId()]);
                }
            }
        }

        return $this->render('requirement_update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return array|false
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionChangeRequirementActual(int $id)
    {
        if (Yii::$app->request->isAjax) {
            $model = RequirementWishList::findOne($id);
            if ($model->getIsActual() === RequirementWishList::REQUIREMENT_ACTUAL) {
                $model->setIsActual(RequirementWishList::REQUIREMENT_NOT_ACTUAL);
            } else {
                $model->setIsActual(RequirementWishList::REQUIREMENT_ACTUAL);
            }
            if ($model->update(false)) {
                $response = [
                    'success' => true,
                    'result' => $model->getIsActualDesc()
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }

    /**
     * @param int $id
     * @return array|false
     * @throws Throwable
     */
    public function actionRequirementDelete(int $id)
    {
        if (Yii::$app->request->isAjax) {
            $model = RequirementWishList::findOne($id);
            $wishListId = $model->getWishListId();
            $result = $model->deleteRecord();

            if ($result === true) {
                $response = [
                    'success' => true,
                    'renderAjax' => $this->renderAjax('requirements_ajax', [
                        'requirements' => RequirementWishList::findAll(['wish_list_id' => $wishListId])
                    ])
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            $response = [
                'success' => false,
                'messageError' => $result
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }

    /**
     * @param int $id
     * @return bool[]|false
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionReasonDelete(int $id)
    {
        if (Yii::$app->request->isAjax) {
            $model = ReasonRequirementWishList::findOne($id);

            if ($model->delete()) {
                $response = ['success' => true];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }

    /**
     * @param int $id
     * @return array|false
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete(int $id)
    {
        if (Yii::$app->request->isAjax) {

            $wishList = WishList::findOne($id);
            $result = $wishList->deleteRecord();

            if ($result === true) {

                $response = [
                    'success' => true,
                    'renderAjax' => $this->renderAjax('new_ajax', [
                        'models' => array_reverse(WishList::findAll(['completed_at' => null]))
                    ])
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }

            $response = [
                'success' => false,
                'messageError' => $result
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }
}