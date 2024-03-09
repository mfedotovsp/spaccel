<?php


namespace app\models\forms;

use app\models\CreatorNewRespondsOnConfirmFirstStep;
use app\models\CreatorRespondsFromAgentsOnConfirmFirstStep;
use yii\base\Model;

/**
 * Форма создания подтверждения гипотезы
 *
 * Class FormCreateConfirm
 * @package app\models\forms
 *
 * @property int $hypothesis_id                                                     Идентификатор гипотезы
 * @property int $count_respond                                                     Количество респондентов
 * @property int $count_positive                                                    Количество респондентов, которые подтверждают гипотезу
 * @property int $add_count_respond                                                 Количество новых респондентов, добавленных к опросу на данном этапе
 * @property CreatorRespondsFromAgentsOnConfirmFirstStep $_creatorResponds          Создатель респондентов для подтверждения гипотезы из респондентов, которые подтвердили ранее идущий этап
 * @property CreatorNewRespondsOnConfirmFirstStep $_creatorNewResponds              Создатель новых респондентов для программы подтверждения
 * @property CacheForm $_cacheManager                                               Менеджер кэширования
 * @property string $cachePath                                                      Путь к файлу кэша
 */
abstract class FormCreateConfirm extends Model
{

    public const CACHE_NAME = 'formCreateConfirmCache';

    public $hypothesis_id;
    public $count_respond;
    public $count_positive;
    public $add_count_respond;
    protected $_creatorResponds;
    protected $_creatorNewResponds;
    public $_cacheManager;
    public $cachePath;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['count_respond', 'count_positive'], 'required'],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'min' => '1'],
            [['count_respond', 'count_positive'], 'integer', 'integerOnly' => TRUE, 'max' => '100'],
        ];
    }


    abstract public function create ();

    /**
     * @return int
     */
    public function getHypothesisId(): int
    {
        return $this->hypothesis_id;
    }

    /**
     * @param int $hypothesis_id
     */
    public function setHypothesisId(int $hypothesis_id): void
    {
        $this->hypothesis_id = $hypothesis_id;
    }

    /**
     * @return int
     */
    public function getCountRespond(): int
    {
        return $this->count_respond;
    }

    /**
     * @param int $count_respond
     */
    public function setCountRespond(int $count_respond): void
    {
        $this->count_respond = $count_respond;
    }

    /**
     * @return int
     */
    public function getCountPositive(): int
    {
        return $this->count_positive;
    }

    /**
     * @param int $count_positive
     */
    public function setCountPositive(int $count_positive): void
    {
        $this->count_positive = $count_positive;
    }

    /**
     * @return int
     */
    public function getAddCountRespond(): int
    {
        return $this->add_count_respond ?: 0;
    }

    /**
     * @param int $add_count_respond
     */
    public function setAddCountRespond(int $add_count_respond): void
    {
        $this->add_count_respond = $add_count_respond;
    }

    /**
     * @return CreatorRespondsFromAgentsOnConfirmFirstStep
     */
    public function getCreatorResponds(): CreatorRespondsFromAgentsOnConfirmFirstStep
    {
        return $this->_creatorResponds;
    }

    /**
     *
     */
    public function setCreatorResponds(): void
    {
        $this->_creatorResponds = new CreatorRespondsFromAgentsOnConfirmFirstStep();
    }

    /**
     * @return CreatorNewRespondsOnConfirmFirstStep
     */
    public function getCreatorNewResponds(): CreatorNewRespondsOnConfirmFirstStep
    {
        return $this->_creatorNewResponds;
    }

    /**
     *
     */
    public function setCreatorNewResponds(): void
    {
        $this->_creatorNewResponds = new CreatorNewRespondsOnConfirmFirstStep();
    }

    /**
     * @return CacheForm
     */
    public function getCacheManager(): CacheForm
    {
        return $this->_cacheManager;
    }

    /**
     *
     */
    public function setCacheManager(): void
    {
        $this->_cacheManager = new CacheForm();
    }

    /**
     * @param string $cachePath
     */
    public function setCachePathForm(string $cachePath): void
    {
        $this->cachePath = $cachePath;
    }

    /**
     * @return string
     */
    public function getCachePathForm(): string
    {
        return $this->cachePath;
    }
}