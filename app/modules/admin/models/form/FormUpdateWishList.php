<?php

namespace app\modules\admin\models\form;

use app\models\WishList;
use yii\base\Model;

/**
 * Форма редактирования списка запросов B2B
 *
 * Class FormUpdateWishList
 * @package app\modules\admin\models\form
 *
 * @property string $company_name                                   наименование предприятия
 * @property string $company_field_of_activity                      сфера деятельности предприятия
 * @property string $company_sort_of_activity                       вид деятельности предприятия
 * @property string $company_products                               продукция/услуги предприятия
 * @property integer $size                                          размер предприятия по количеству персонала
 * @property integer $location_id                                   идентификатор локации(города) предприятия
 * @property integer $type_company                                  тип предприятия
 * @property integer $type_production                               тип производства
 * @property string $add_info                                       дополнительная информация
 *
 * @property WishList $_model
 */
class FormUpdateWishList extends Model
{
    public $company_name;
    public $company_field_of_activity;
    public $company_sort_of_activity;
    public $company_products;
    public $size;
    public $location_id;
    public $type_company;
    public $type_production;
    public $add_info;
    public $_model;

    public function __construct(int $id, $config = [])
    {
        $this->_model = WishList::findOne($id);
        $this->setCompanyName($this->_model->getCompanyName());
        $this->setCompanyFieldOfActivity($this->_model->getCompanyFieldOfActivity());
        $this->setCompanySortOfActivity($this->_model->getCompanySortOfActivity());
        $this->setCompanyProducts($this->_model->getCompanyProducts());
        $this->setSize($this->_model->getSize());
        $this->setLocationId($this->_model->getLocationId());
        $this->setTypeCompany($this->_model->getTypeCompany());
        $this->setTypeProduction($this->_model->getTypeProduction());
        $this->setAddInfo($this->_model->getAddInfo());

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['company_name', 'company_field_of_activity', 'company_sort_of_activity', 'company_products', 'size', 'location_id', 'type_company', 'type_production', 'add_info'], 'required'],
            [['company_name', 'company_field_of_activity', 'company_sort_of_activity'], 'string', 'max' => 255],
            [['company_products', 'add_info'], 'string', 'max' => 2000],
            [['company_name', 'company_field_of_activity', 'company_sort_of_activity', 'company_products', 'add_info'], 'trim'],
            [['size', 'location_id', 'type_company', 'type_production',], 'integer'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'company_name' => 'Наименование предприятия',
            'company_field_of_activity' => 'Сфера деятельности предприятия',
            'company_sort_of_activity' => 'Вид деятельности предприятия',
            'company_products' => 'Продукция/услуги предприятия',
            'size' => 'Размер предприятия по количеству персонала',
            'location_id' => 'Локация предприятия (город)',
            'type_company' => 'Тип предприятия',
            'type_production' => 'Тип производства',
            'add_info' => 'Дополнительная информация'
        ];
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        $this->_model->setCompanyName($this->getCompanyName());
        $this->_model->setCompanyFieldOfActivity($this->getCompanyFieldOfActivity());
        $this->_model->setCompanySortOfActivity($this->getCompanySortOfActivity());
        $this->_model->setCompanyProducts($this->getCompanyProducts());
        $this->_model->setSize($this->getSize());
        $this->_model->setLocationId($this->getLocationId());
        $this->_model->setTypeCompany($this->getTypeCompany());
        $this->_model->setTypeProduction($this->getTypeProduction());
        $this->_model->setAddInfo($this->getAddInfo());
        return $this->_model->save();
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->company_name;
    }

    /**
     * @param string $company_name
     */
    public function setCompanyName(string $company_name): void
    {
        $this->company_name = $company_name;
    }

    /**
     * @return string
     */
    public function getCompanyFieldOfActivity(): string
    {
        return $this->company_field_of_activity;
    }

    /**
     * @param string $company_field_of_activity
     */
    public function setCompanyFieldOfActivity(string $company_field_of_activity): void
    {
        $this->company_field_of_activity = $company_field_of_activity;
    }

    /**
     * @return string
     */
    public function getCompanySortOfActivity(): string
    {
        return $this->company_sort_of_activity;
    }

    /**
     * @param string $company_sort_of_activity
     */
    public function setCompanySortOfActivity(string $company_sort_of_activity): void
    {
        $this->company_sort_of_activity = $company_sort_of_activity;
    }

    /**
     * @return string
     */
    public function getCompanyProducts(): string
    {
        return $this->company_products;
    }

    /**
     * @param string $company_products
     */
    public function setCompanyProducts(string $company_products): void
    {
        $this->company_products = $company_products;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getLocationId(): int
    {
        return $this->location_id;
    }

    /**
     * @param int $location_id
     */
    public function setLocationId(int $location_id): void
    {
        $this->location_id = $location_id;
    }

    /**
     * @return int
     */
    public function getTypeCompany(): int
    {
        return $this->type_company;
    }

    /**
     * @param int $type_company
     */
    public function setTypeCompany(int $type_company): void
    {
        $this->type_company = $type_company;
    }

    /**
     * @return int
     */
    public function getTypeProduction(): int
    {
        return $this->type_production;
    }

    /**
     * @param int $type_production
     */
    public function setTypeProduction(int $type_production): void
    {
        $this->type_production = $type_production;
    }

    /**
     * @return string
     */
    public function getAddInfo(): string
    {
        return $this->add_info;
    }

    /**
     * @param string $add_info
     */
    public function setAddInfo(string $add_info): void
    {
        $this->add_info = $add_info;
    }
}