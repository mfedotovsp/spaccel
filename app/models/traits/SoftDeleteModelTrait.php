<?php

namespace app\models\traits;

use yii\db\ActiveQuery;

trait SoftDeleteModelTrait
{
    /**
     * @param bool $isOnlyNotDelete
     * @return ActiveQuery
     */
    public static function find(bool $isOnlyNotDelete = true): ActiveQuery
    {
        if ($isOnlyNotDelete) {
            return parent::find()->where([static::tableName() . '.deleted_at' => null]);
        }
        return parent::find();
    }

    /**
     * @param string|array $condition
     * @return int
     */
    public static function softDeleteAll($condition): int
    {
        return parent::updateAll([static::tableName() . '.deleted_at' => time()], $condition);
    }

    /**
     * @param string|array $condition
     * @return int
     */
    public function softDelete($condition): int
    {
        return parent::updateAll([static::tableName() . '.deleted_at' => time()], $condition);
    }

    /**
     * @param string|array $condition
     * @return int
     */
    public function recovery($condition): int
    {
        return parent::updateAll([static::tableName() . '.deleted_at' => null], $condition);
    }

    /**
     * @param string|array $condition
     * @return int
     */
    public static function recoveryAll($condition): int
    {
        return parent::updateAll([static::tableName() . '.deleted_at' => null], $condition);
    }
}