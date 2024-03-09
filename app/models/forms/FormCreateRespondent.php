<?php


namespace app\models\forms;

use app\models\CreatorAnswersForNewRespond;
use app\models\interfaces\ConfirmationInterface;
use yii\base\Model;

/**
 * Форма создания респондента
 *
 * Class FormCreateRespondent
 * @package app\models\forms
 *
 * @property string $name                                               ФИО респондента
 * @property int $confirm_id                                            Идентификатор записи подтверждения гипотезы
 * @property CreatorAnswersForNewRespond $_creatorAnswers               Создатель пустых ответов на вопросы для нового респондента
 * @property CacheForm $_cacheManager                                   Менеджер кэширования
 * @property string $cachePath                                          Путь к файлу кэша
 */
abstract class FormCreateRespondent extends Model
{
    public const CACHE_NAME = 'formCreateRespondCache';

    public $name;
    public $confirm_id;
    public $_creatorAnswers;
    public $_cacheManager;
    public $cachePath;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'trim'],
            [['name'], 'uniqueName'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name' => 'Фамилия, имя, отчество',
        ];
    }

    /**
     * Получить путь к кэшу формы
     *
     * @param ConfirmationInterface $confirm
     * @return string
     */
    abstract public static function getCachePath(ConfirmationInterface $confirm): string;

    /**
     * Создать респондента
     *
     * @return mixed
     */
    abstract public function create();

    /**
     * Проверка уникального имени
     * респондента в данном подтверждении
     *
     * @param $attr
     * @return mixed
     */
    abstract public function uniqueName($attr);

    /**
     * @param int $id
     */
    public function setConfirmId(int $id): void
    {
        $this->confirm_id = $id;
    }

    /**
     * @return int
     */
    public function getConfirmId(): int
    {
        return $this->confirm_id;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CreatorAnswersForNewRespond
     */
    public function getCreatorAnswers(): CreatorAnswersForNewRespond
    {
        return $this->_creatorAnswers;
    }

    /**
     *
     */
    public function setCreatorAnswers(): void
    {
        $this->_creatorAnswers = new CreatorAnswersForNewRespond();
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
     * @return string
     */
    public function getCachePathForm(): string
    {
        return $this->cachePath;
    }

    /**
     * @param string $cachePath
     */
    public function setCachePathForm(string $cachePath): void
    {
        $this->cachePath = $cachePath;
    }
}