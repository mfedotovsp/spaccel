<?php

namespace app\models\forms;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class SearchContractorsForm extends Model
{
    /**
     * Вид деятельности исполнителя
     * @var integer
     */
    public $activityId;

    /**
     * Идентификатор проекта
     * @var integer
     */
    public $projectId;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['activityId', 'projectId'], 'required'],
            [['activityId', 'projectId'], 'integer'],
        ];
    }


    /**
     * @return array|ActiveRecord[]
     */
    public function search(): array
    {
        $user = User::findOne(Yii::$app->user->getId());
        return User::find()
            ->innerJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->innerJoin('contractor_info', '`contractor_info`.`contractor_id` = `user`.`id`')
            ->andWhere(['role' => User::ROLE_CONTRACTOR])
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->andWhere(['confirm' => User::CONFIRM])
            ->andWhere(['client_user.client_id' => $user->clientUser->getClientId()])
            ->andWhere(['like', 'contractor_info.activities', $this->activityId])
            ->all();
    }
}