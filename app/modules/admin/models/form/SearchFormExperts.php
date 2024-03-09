<?php


namespace app\modules\admin\models\form;

use app\models\ClientUser;
use app\models\ExpertType;
use app\models\User;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;

class SearchFormExperts extends Model
{

    /**
     * Логин эксперта
     * @var string
     */
    public $name;

    /**
     * Ключевые слова
     * @var string
     */
    public $keywords;

    /**
     * Сфера профессиональной компетенции
     * @var string
     */
    public $scope_professional_competence;

    /**
     * Тип эксперта
     * @var array
     */
    public $type;


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['keywords', 'name', 'scope_professional_competence'], 'string', 'max' => 255],
            [['keywords', 'name', 'scope_professional_competence'], 'trim'],
            ['type', 'safe'],
        ];
    }


    /**
     * Поиск экспертов
     *
     * @return array|ActiveRecord[]
     */
    public static function search(): array
    {
        $clientUser = ClientUser::findOne(['user_id' => Yii::$app->user->getId()]);
        $client = $clientUser->client;
        $filter = $_POST['SearchFormExperts'];
        $name = trim($filter['name']);
        $scopeProfessionalCompetence = trim($filter['scope_professional_competence']);
        $keywords = explode(' ', trim($filter['keywords']));
        if (!$filter['type']) {
            $listTypes = array_keys(ExpertType::getListTypes());
            $type = implode('|', $listTypes);
        } else {
            $type = implode('|', $filter['type']);
        }

        $experts = User::find()->joinWith(['expertInfo', 'keywords'])
            ->leftJoin('client_user', '`client_user`.`user_id` = `user`.`id`')
            ->andWhere(['role' => User::ROLE_EXPERT, 'status' => User::STATUS_ACTIVE, 'client_user.client_id' => $client->getId()])
            ->andWhere(['REGEXP', 'expert_info.type', $type])
            ->andWhere(['like', 'username', $name])
            ->andWhere(['like', 'scope_professional_competence', $scopeProfessionalCompetence]);

        foreach ($keywords as $keyword) {
            $experts->orOnCondition(['like', 'keywords_expert.description', $keyword]);
        }

        return $experts->all();
    }
}