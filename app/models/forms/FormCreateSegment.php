<?php


namespace app\models\forms;

use app\models\Projects;
use app\models\SegmentRequirement;
use app\models\Segments;
use Yii;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

/**
 * Форма создания сегмента
 *
 * Class FormCreateSegment
 * @package app\models\forms
 */
class FormCreateSegment extends FormSegment
{

    public $_cacheManager;
    public $cachePath;


    /**
     * FormCreateSegment constructor.
     * @param Projects $project
     * @param bool|null $useWishList
     * @param int|null $requirementId
     * @param array $config
     */
    public function __construct(Projects $project, bool $useWishList = null, int $requirementId = null, array $config = [])
    {
        $this->setProjectId($project->getId());
        $this->_cacheManager = new CacheForm();
        $this->cachePath = self::getCachePath($project->getId());
        $cacheName = 'formCreateHypothesisCache';
        if ($cache = $this->_cacheManager->getCache($this->cachePath, $cacheName)) {
            $className = explode('\\', self::class)[3];
            foreach ($cache[$className] as $key => $value) {
                if ($key === 'use_wish_list' && $useWishList !== null) {
                    $this->setUseWishList($useWishList ? Segments::USE_WISH_LIST : Segments::NOT_USE_WISH_LIST);
                } elseif ($key === 'requirement_id' && $requirementId !== null) {
                    $this->setRequirementId($requirementId);
                } else {
                    $this[$key] = $value;
                }
            }
        } else {
            $this->setUseWishList(Segments::NOT_USE_WISH_LIST);
        }

        if ($requirementId && !$this->getRequirementId()) {
            $this->setRequirementId($requirementId);
        }

        parent::__construct($config);
    }


    /**
     * @param int $projectId
     * @return string
     */
    public static function getCachePath(int $projectId): string
    {
        return '../runtime/cache/forms/user-'.Yii::$app->user->getId(). '/projects/project-'.$projectId.'/segments/formCreate/';
    }


    /**
     * Проверка заполнения полей формы
     * @return bool
     */
    public function checkFillingFields (): bool
    {
        if ($this->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C) {

            if (!empty($this->name) && !empty($this->description) && !empty($this->field_of_activity_b2c)
                && !empty($this->sort_of_activity_b2c) && !empty($this->age_from) && !empty($this->age_to)
                && !empty($this->gender_consumer) && !empty($this->education_of_consumer) && !empty($this->income_from)
                && !empty($this->income_to) && !empty($this->quantity)) {

                return true;
            }
            return false;

        }

        if ($this->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2B) {

            if (!empty($this->name) && !empty($this->description) && !empty($this->field_of_activity_b2b)
                && !empty($this->sort_of_activity_b2b) && !empty($this->company_products) && !empty($this->company_partner)
                && !empty($this->quantity_b2b) && !empty($this->income_company_from) && !empty($this->income_company_to)) {

                return true;
            }
            return false;
        }

        return false;
    }


    /**
     * @return Segments|bool
     * @throws NotFoundHttpException
     * @throws ErrorException
     */
    public function create()
    {
        if ($this->validate()){

            $segment = new Segments();
            $segment->setName($this->getName());
            $segment->setDescription($this->getDescription());
            $segment->setProjectId($this->getProjectId());
            $segment->setTypeOfInteractionBetweenSubjects($this->getTypeOfInteractionBetweenSubjects());

            if ($this->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2C){

                $segment->setUseWishList(Segments::NOT_USE_WISH_LIST);
                $segment->setFieldOfActivity($this->getFieldOfActivityB2c());
                $segment->setSortOfActivity($this->getSortOfActivityB2c());
                $segment->setAgeFrom($this->getAgeFrom());
                $segment->setAgeTo($this->getAgeTo());
                $segment->setGenderConsumer($this->getGenderConsumer());
                $segment->setEducationOfConsumer($this->getEducationOfConsumer());
                $segment->setIncomeFrom($this->getIncomeFrom());
                $segment->setIncomeTo($this->getIncomeTo());
                $segment->setQuantity($this->getQuantity());
                $segment->setMarketVolume(((($this->getIncomeFrom() + $this->getIncomeTo()) * 6) * $this->getQuantity()) / 1000000);
                $segment->setAddInfo($this->getAddInfoB2c());
                if ($this->getContractorId()) {
                    $segment->setContractorId($this->getContractorId());
                }
                if ($this->getTaskId()) {
                    $segment->setTaskId($this->getTaskId());
                }

                if ($segment->save()) {
                    $this->_cacheManager->deleteCache($this->cachePath); // Удаление кэша формы создания
                    return $segment;
                }
                throw new NotFoundHttpException('Ошибка. Неудалось сохранить сегмент');

            }elseif ($this->getTypeOfInteractionBetweenSubjects() === Segments::TYPE_B2B) {

                $segment->setUseWishList($this->getUseWishList());
                $segment->setFieldOfActivity($this->getFieldOfActivityB2b());
                $segment->setSortOfActivity($this->getSortOfActivityB2b());
                $segment->setCompanyProducts($this->getCompanyProducts());
                $segment->setQuantity($this->getQuantityB2b());
                $segment->setCompanyPartner($this->getCompanyPartner());
                $segment->setIncomeFrom($this->getIncomeCompanyFrom());
                $segment->setIncomeTo($this->getIncomeCompanyTo());
                $segment->setMarketVolume((($this->getIncomeCompanyFrom() + $this->getIncomeCompanyTo()) / 2) * $this->getQuantityB2b());
                $segment->setAddInfo($this->getAddInfoB2b());
                if ($this->getContractorId()) {
                    $segment->setContractorId($this->getContractorId());
                }
                if ($this->getTaskId()) {
                    $segment->setTaskId($this->getTaskId());
                }

                if ($segment->save()) {
                    if ($this->getRequirementId()) {
                        SegmentRequirement::create($segment->getId(), $this->getRequirementId());
                    }
                    $this->_cacheManager->deleteCache($this->cachePath); // Удаление кэша формы создания
                    return $segment;
                }
                throw new NotFoundHttpException('Неудалось сохранить сегмент');
            }

        }

        return false;
    }


    /**
     * @param $attr
     */
    public function uniqueName($attr): void
    {
        /** @var $models Segments[] */
        $models = Segments::find(false)
            ->andWhere(['project_id' => $this->getProjectId()])
            ->all();

        foreach ($models as $item){

            if (mb_strtolower(str_replace(' ', '', $this->getName())) === mb_strtolower(str_replace(' ', '',$item->getName()))){

                $this->addError($attr, 'Сегмент с названием «'. $this->getName() .'» уже существует!');
            }
        }
    }
}