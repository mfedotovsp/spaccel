<?php

namespace app\modules\admin\controllers;

use app\models\LocationWishList;
use app\models\PatternHttpException;
use app\models\User;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

class LocationController extends AppAdminController
{
    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        if (User::isUserMainAdmin(Yii::$app->user->identity['username'])) {
            return parent::beforeAction($action);
        }

        PatternHttpException::noAccess();
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $models = LocationWishList::find()
            ->orderBy('name ASC')
            ->all();

        return $this->render('index', [
            'models' => $models,
            'modelCreate' => new LocationWishList()
        ]);
    }

    /**
     * @return array|false
     */
    public function actionCreate()
    {
        if (Yii::$app->request->isAjax) {
            $model = new LocationWishList();
            if ($model->load(Yii::$app->request->post())) {
                $exist = LocationWishList::find()
                    ->andWhere(['like', 'name', $model->getName()])
                    ->exists();
                if (!$exist) {
                    if ($model->save()) {
                        $response = [
                            'success' => true,
                            'renderAjax' => $this->renderAjax('index_ajax', [
                                'models' => LocationWishList::find()
                                    ->orderBy('name ASC')
                                    ->all()
                            ])
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Локация уже существует'
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
     * @return array|false
     */
    public function actionGetLocationToUpdate(int $id)
    {
        $model = LocationWishList::findOne($id);
        if(Yii::$app->request->isAjax) {
            $response = [
                'renderAjax' => $this->renderAjax('update', [
                    'model' => $model
                ]),
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }

    /**
     * @param int $id
     * @return array|false
     */
    public function actionUpdate(int $id)
    {
        if (Yii::$app->request->isAjax) {
            $model = LocationWishList::findOne($id);
            if ($model->load(Yii::$app->request->post())) {
                $exist = LocationWishList::find()
                    ->andWhere(['!=', 'id', $id])
                    ->andWhere(['like', 'name', $model->getName()])
                    ->exists();
                if (!$exist) {
                    if ($model->save()) {
                        $response = [
                            'success' => true,
                            'renderAjax' => $this->renderAjax('index_ajax', [
                                'models' => LocationWishList::find()
                                    ->orderBy('name ASC')
                                    ->all()
                            ])
                        ];
                        Yii::$app->response->format = Response::FORMAT_JSON;
                        Yii::$app->response->data = $response;
                        return $response;
                    }
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Локация уже существует'
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }
}