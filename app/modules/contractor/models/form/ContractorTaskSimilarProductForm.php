<?php

namespace app\modules\contractor\models\form;

use app\models\ContractorTaskSimilarProductParams;
use app\models\ContractorTaskSimilarProducts;
use yii\base\Model;

/**
 * Форма создания и редактирования продукта-аналога
 *
 * @property string $name
 * @property int $ownership_cost
 * @property int $price
 * @property array|null $params
 */
class ContractorTaskSimilarProductForm extends Model
{
    public $name;
    public $ownership_cost;
    public $price;
    public $params;

    /**
     * @param ContractorTaskSimilarProducts $product
     * @param string $action
     * @param array $config
     */
    public function __construct(ContractorTaskSimilarProducts $product, string $action = 'create', array $config = [])
    {
        if ($action === 'update') {
            $this->name = $product->getName();
            $this->ownership_cost = $product->getOwnershipCost();
            $this->price = $product->getPrice();
        }

        $productParamIds = ContractorTaskSimilarProductParams::find()->select(['id'])
            ->andWhere(['deleted_at' => null, 'task_id' => $product->getTaskId()
            ])->asArray()->all();

        if ($productParamIds) {
            $productParamIds = array_column($productParamIds, 'id');
            if (($action === 'update') && $productParams = $product->getParams()) {

                foreach ($productParamIds as $productParamId) {
                    $param = "";

                    if (array_key_exists($productParamId, $productParams)) {
                        $param = $productParams[$productParamId];
                    }

                    $this->params[$productParamId] = $param;
                }

            } else {
                foreach ($productParamIds as $productParamId) {
                    $this->params[$productParamId] = "";
                }
            }
        }

        parent::__construct($config);
    }


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name', 'ownership_cost', 'price'], 'required'],
            [['ownership_cost', 'price'], 'integer', 'min' => 1, 'max' => 1000000000],
            [['name'], 'string', 'max' => 255],
            ['name', 'trim'],
            ['params', 'safe']
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Наименование продукта',
            'price' => 'Цена продукта (в рублях)',
            'ownership_cost' => 'Стоимость владения (в рублях)',
        ];
    }
}