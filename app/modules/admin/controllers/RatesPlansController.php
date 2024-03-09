<?php


namespace app\modules\admin\controllers;

use app\models\Client;
use app\models\ClientRatesPlan;
use app\models\PatternHttpException;
use app\models\RatesPlan;
use app\models\User;
use app\modules\admin\models\form\FormCreateRatesPlan;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Контроллер с методами для работы с тарифными планами
 * Доступ только для главного админа Spaccel
 *
 * Class RatesPlansController
 * @package app\modules\admin\controllers
 */
class RatesPlansController extends AppAdminController
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
     * Страница с тарифными планами
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $ratesPlans = RatesPlan::find()->all();
        return $this->render('index', [
            'ratesPlans' => $ratesPlans
        ]);
    }


    /**
     * Показать форму создания тарифного плана
     *
     * @return array|bool
     */
    public function actionGetFormCreate()
    {
        if (Yii::$app->request->isAjax) {
            $model = new FormCreateRatesPlan();
            $response = ['renderAjax' => $this->renderAjax('form_create', ['model' => $model])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @return bool|Response
     */
    public function actionCreate()
    {
        $model = new FormCreateRatesPlan();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->create()) {
                return $this->redirect('/admin/rates-plans/index');
            }
        }
        return false;
    }


    /**
     * Получить список тарифных планов
     *
     * @param int $clientId
     * @return array|bool
     */
    public function actionGetListRatesPlans(int $clientId)
    {
        if (Yii::$app->request->isAjax){

            $ratesPlans = RatesPlan::find()->all();
            $clientRatesPlan = ClientRatesPlan::find()
                ->andWhere(['client_id' => $clientId])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();
            if (!$clientRatesPlan) {
                $clientRatesPlan = new ClientRatesPlan();
                $clientRatesPlan->setClientId($clientId);
            }

            $response = ['renderAjax' => $this->renderAjax('list-rates-plans', [
                'ratesPlans' => $ratesPlans, 'clientRatesPlan' => $clientRatesPlan])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * Присваивание тарифа клиенту
     *
     * @return array|bool
     */
    public function actionCreateClientRatesPlan()
    {
        if (Yii::$app->request->isAjax){
            $clientRatesPlan = new ClientRatesPlan();
            if ($clientRatesPlan->load(Yii::$app->request->post())) {
                $clientRatesPlan->setDateStart(strtotime($_POST['ClientRatesPlan']['date_start']));
                $clientRatesPlan->setDateEnd(strtotime($_POST['ClientRatesPlan']['date_end']));
                if ($clientRatesPlan->save()) {
                    $response = [
                        'renderAjax' => $this->renderAjax('/clients/data_client', ['client' => Client::findOne($clientRatesPlan->getClientId())]),
                        'client_id' => $clientRatesPlan->getClientId()
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