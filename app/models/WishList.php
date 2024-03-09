<?php

namespace app\models;

use Throwable;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс хранит информацию в бд о списках запросов компаний B2B сегмента
 *
 * Class WishList
 * @package app\models
 *
 * @property int $id                                    идентификатор записи
 * @property int $client_id                             идентификатор клиента
 * @property string $company_name                       наименование предприятия
 * @property string $company_field_of_activity          сфера деятельности предприятия
 * @property string $company_sort_of_activity           вид деятельности предприятия
 * @property string $company_products                   продукция/услуги предприятия
 * @property int $size                                  размер предприятия по количеству персонала
 * @property int $location_id                           идентификатор локации(города) предприятия
 * @property int $type_company                          тип предприятия
 * @property int $type_production                       тип производства
 * @property string $add_info                           дополнительная информация
 * @property int|null $completed_at                     дата завершения(готовности списка), если поле !== null, то список будет доступен для пользователей и его нельзя будет уже редактировать
 * @property int $created_at                            дата создания
 * @property int $updated_at                            дата редактирования
 *
 * @property Client $client                             Организация, которой принадлежит список
 * @property LocationWishList $location                 Локация(города) предприятия
 * @property RequirementWishList[] $requirements        Запросы предприятия
 */
class WishList extends ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'wish_list';
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['company_name', 'company_field_of_activity', 'company_sort_of_activity', 'company_products', 'size', 'location_id', 'type_company', 'type_production', 'add_info', 'client_id'], 'required'],
            [['company_name', 'company_field_of_activity', 'company_sort_of_activity'], 'string', 'max' => 255],
            [['company_products', 'add_info'], 'string', 'max' => 2000],
            [['company_name', 'company_field_of_activity', 'company_sort_of_activity', 'company_products', 'add_info'], 'trim'],
            [['size', 'location_id', 'type_company', 'type_production', 'client_id', 'completed_at'], 'integer'],
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
     * Получить объект организации
     *
     * @return ActiveQuery
     */
    public function getClient(): ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    /**
     * Получить объект локации
     *
     * @return ActiveQuery
     */
    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(LocationWishList::class, ['id' => 'location_id']);
    }

    /**
     * Получить объекты запросов
     *
     * @return ActiveQuery
     */
    public function getRequirements(): ActiveQuery
    {
        return $this->hasMany(RequirementWishList::class, ['wish_list_id' => 'id']);
    }

    /**
     * @return bool|string
     * @throws Throwable
     */
    public function deleteRecord()
    {
        try {
            $requirements = $this->requirements;
            if ($requirements) {
                foreach ($requirements as $requirement) {
                    if ($error = $requirement->deleteRecord() !== true) {
                        return $error;
                    }
                }
            }
            $this->delete();
            return true;
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->client_id;
    }

    /**
     * @param int $client_id
     */
    public function setClientId(int $client_id): void
    {
        $this->client_id = $client_id;
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
     * @return string
     */
    public function getSizeName(): string
    {
        if ($this->size === SizesWishList::LARGE) {
            return SizesWishList::LABEL_LARGE;
        }

        if ($this->size === SizesWishList::MIDDLE) {
            return SizesWishList::LABEL_MIDDLE;
        }

        if ($this->size === SizesWishList::SMALL) {
            return SizesWishList::LABEL_SMALL;
        }
        return '';
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
     * @return string
     */
    public function getTypeCompanyName(): string
    {
        if ($this->getTypeCompany() === TypesCompanyWishList::INDUSTRIAL) {
            return TypesCompanyWishList::LABEL_INDUSTRIAL;
        }

        if ($this->getTypeCompany() === TypesCompanyWishList::AGRICULTURAL) {
            return TypesCompanyWishList::LABEL_AGRICULTURAL;
        }

        if ($this->getTypeCompany() === TypesCompanyWishList::RETAIL_TRADE) {
            return TypesCompanyWishList::LABEL_RETAIL_TRADE;
        }

        if ($this->getTypeCompany() === TypesCompanyWishList::WHOLESALE) {
            return TypesCompanyWishList::LABEL_WHOLESALE;
        }
        return '';
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
     * @return string
     */
    public function getTypeProductionName(): string
    {
        if ($this->type_production === TypesProductionWishList::MASS_PRODUCTION) {
            return TypesProductionWishList::LABEL_MASS_PRODUCTION;
        }

        if ($this->type_production === TypesProductionWishList::LARGE_PRODUCTION) {
            return TypesProductionWishList::LABEL_LARGE_PRODUCTION;
        }

        if ($this->type_production === TypesProductionWishList::MID_PRODUCTION) {
            return TypesProductionWishList::LABEL_MID_PRODUCTION;
        }

        if ($this->type_production === TypesProductionWishList::SMALL_PRODUCTION) {
            return TypesProductionWishList::LABEL_SMALL_PRODUCTION;
        }

        if ($this->type_production === TypesProductionWishList::SINGLE_PRODUCTION) {
            return TypesProductionWishList::LABEL_SINGLE_PRODUCTION;
        }
        return '';
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

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->created_at;
    }

    /**
     * @return int
     */
    public function getUpdatedAt(): int
    {
        return $this->updated_at;
    }

    /**
     * @return int|null
     */
    public function getCompletedAt(): ?int
    {
        return $this->completed_at;
    }

    /**
     * @param int|null $completed_at
     */
    public function setCompletedAt(?int $completed_at): void
    {
        $this->completed_at = $completed_at;
    }

    /**
     * @return bool
     */
    public function isReadyForCompletion(): bool
    {
        if (count($this->requirements) > 0 && !$this->getCompletedAt()) {
            return true;
        }
        return false;
    }
}