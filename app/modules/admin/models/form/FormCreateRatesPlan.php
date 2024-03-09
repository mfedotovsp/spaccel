<?php


namespace app\modules\admin\models\form;

use app\models\RatesPlan;
use yii\base\Model;

/**
 * Форма создания тарифного плана
 *
 * Class FormCreateRatesPlan
 * @package app\modules\admin\models\form
 *
 * @property string $name                       наименование тарифного плана
 * @property string $description                описание тарифного плана
 * @property int $max_count_project_user        максимальное количество проектантов по тарифному плану
 * @property int $max_count_tracker             максимальное количество трекеров по тарифному плану
 * @property RatesPlan $_ratesPlan              объект модели тарифного плана
 */
class FormCreateRatesPlan extends Model
{

    public $name;
    public $description;
    public $max_count_project_user;
    public $max_count_tracker;
    private $_ratesPlan;


    /**
     * FormCreateRatesPlan constructor
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->_ratesPlan = new RatesPlan();
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'description', 'max_count_project_user', 'max_count_tracker'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 2000],
            [['name', 'description'], 'trim'],
            [['max_count_project_user', 'max_count_tracker'], 'integer'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Наименование',
            'description' => 'Описание',
            'max_count_project_user' => 'Максимальное количество проектантов',
            'max_count_tracker' => 'Максимальное количество трекеров',
        ];
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        $this->_ratesPlan->setName($this->name);
        $this->_ratesPlan->setDescription($this->description);
        $this->_ratesPlan->setMaxCountProjectUser($this->max_count_project_user);
        $this->_ratesPlan->setMaxCountTracker($this->max_count_tracker);
        return $this->_ratesPlan->save();
    }
}