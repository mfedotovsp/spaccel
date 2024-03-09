<?php


namespace app\controllers;


use app\models\DuplicateCommunications;
use app\models\PatternHttpException;
use app\models\User;
use Throwable;
use yii\data\Pagination;
use yii\db\StaleObjectException;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\Response;


class CommunicationsController extends AppUserPartController
{

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {

        if ($action->id === 'notifications') {

            $user = User::findOne((int)Yii::$app->request->get('id'));
            if (!$user) {
                PatternHttpException::noData();
            }
            
            if (User::isUserDev(Yii::$app->user->identity['username']) || User::isUserMainAdmin(Yii::$app->user->identity['username'])
                || (Yii::$app->user->getId() === $user->getId())) {
                return parent::beforeAction($action);
            }

            PatternHttpException::noAccess();

        } else{
            return parent::beforeAction($action);
        }

    }


    /**
     * Страница уведомлений
     *
     * @param int $id
     * @return string
     */
    public function actionNotifications(int $id): string
    {
        $query = DuplicateCommunications::find()->andWhere(['adressee_id' => $id])->orderBy('id DESC');
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $pages->pageSizeParam = false; //убираем параметр $per-page
        $communications = $query->offset($pages->offset)->limit(20)->all();

        return $this->render('notifications', [
            'communications' => $communications,
            'pages' => $pages
        ]);
    }


    /**
     * Прочтение уведомления
     * (коммуникации)
     *
     * @param int $id
     * @return array|bool
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionReadCommunication(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $communication = DuplicateCommunications::findOne($id);
            $communication->setStatusRead();
            $communication->update();

            $user = User::findOne($communication->getAdresseeId());
            $countUnreadCommunications = $user->getCountUnreadCommunications();
            $countUnreadCommunicationsByProject = $user->getCountUnreadCommunicationsByProject($communication->source->getProjectId());

            $response = [
                'project_id' => $communication->source->getProjectId(),
                'countUnreadCommunications' => $countUnreadCommunications,
                'countUnreadCommunicationsByProject' => $countUnreadCommunicationsByProject
            ];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }

        return false;
    }
}