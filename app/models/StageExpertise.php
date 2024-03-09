<?php


namespace app\models;


/**
 * Класс, который хранит значения
 * этапов экспертизы по проекту
 *
 * Class StageExpertise
 * @package app\models
 */
class StageExpertise
{

    public const PROJECT = 0;
    public const SEGMENT = 1;
    public const CONFIRM_SEGMENT = 2;
    public const PROBLEM = 3;
    public const CONFIRM_PROBLEM = 4;
    public const GCP = 5;
    public const CONFIRM_GCP = 6;
    public const MVP = 7;
    public const CONFIRM_MVP = 8;
    public const BUSINESS_MODEL = 9;

    /**
     * @var array
     */
    private static $list = array(
        self::PROJECT => 'project',
        self::SEGMENT => 'segment',
        self::CONFIRM_SEGMENT => 'confirm_segment',
        self::PROBLEM => 'problem',
        self::CONFIRM_PROBLEM => 'confirm_problem',
        self::GCP => 'gcp',
        self::CONFIRM_GCP => 'confirm_gcp',
        self::MVP => 'mvp',
        self::CONFIRM_MVP => 'confirm_mvp',
        self::BUSINESS_MODEL => 'business_model'
    );

    /**
     * @var array
     */
    private static $listClasses = [
        'project' => Projects::class,
        'segment' => Segments::class,
        'confirm_segment' => ConfirmSegment::class,
        'problem' => Problems::class,
        'confirm_problem' => ConfirmProblem::class,
        'gcp' => Gcps::class,
        'confirm_gcp' => ConfirmGcp::class,
        'mvp' => Mvps::class,
        'confirm_mvp' => ConfirmMvp::class,
        'business_model' => BusinessModel::class
    ];

    /**
     * @return array
     */
    public static function getList(): array
    {
        return self::$list;
    }

    /**
     * @param int|string $value
     * @return false|int|string
     */
    public static function getKey($value)
    {
        return array_search($value, self::$list, false);
    }

    /**
     * @return array
     */
    public static function getListClasses(): array
    {
        return self::$listClasses;
    }

    /**
     * Получить класс объекта,
     * по которому проводится экспертиза,
     * по параметру stage из url
     *
     * @param $stage string
     * @return mixed
     */
    public static function getClassByStage(string $stage)
    {
        return self::getListClasses()[$stage];
    }


    /**
     * Массив с названиями этапов проекта
     *
     * @return array
     */
    private static $listTitle = [
        'project' => 'описание проекта',
        'segment' => 'генерация гипотезы целевого сегмента',
        'confirm_segment' => 'подтверждение гипотезы целевого сегмента',
        'problem' => 'генерация гипотезы проблемы сегмента',
        'confirm_problem' => 'подтверждение гипотезы проблемы сегмента',
        'gcp' => 'разработка гипотезы ценностного предложения',
        'confirm_gcp' => 'подтверждение гипотезы ценностного предложения',
        'mvp' => 'разработка MVP',
        'confirm_mvp' => 'подтверждение MVP',
        'business_model' => 'генерация бизнес-модели'
    ];


    /**
     * Получить название этапа экспертизы
     *
     * @param string $stage
     * @param int $stageId
     * @return string
     */
    public static function getTitle(string $stage, int $stageId): string
    {
        $title = '';
        $class = self::getClassByStage($stage);
        $obj = $class::find(false)
            ->andWhere(['id' => $stageId])
            ->one();

        if ($obj instanceof Projects) {
            $title = self::$listTitle[$stage] . '</br> «' . $obj->getProjectName() . '»';
        } elseif ($obj instanceof Segments) {
            $title = self::$listTitle[$stage] . '</br> «' . $obj->getName() . '»';
        } elseif ($obj instanceof ConfirmSegment) {
            /** @var $segment Segments */
            $segment = Segments::find(false)->andWhere(['id' => $obj->getSegmentId()])->one();
            $title = self::$listTitle[$stage] . '</br> «' . $segment->getName() . '»';
        } elseif ($obj instanceof Problems) {
            $title = self::$listTitle[$stage] . '</br> «' . $obj->getTitle() . '»';
        } elseif ($obj instanceof ConfirmProblem) {
            /** @var $problem Problems */
            $problem = Problems::find(false)->andWhere(['id' => $obj->getProblemId()])->one();
            $title = self::$listTitle[$stage] . '</br> «' . $problem->getTitle() . '»';
        } elseif ($obj instanceof Gcps) {
            $title = self::$listTitle[$stage] . '</br> «' . $obj->getTitle() . '»';
        } elseif ($obj instanceof ConfirmGcp) {
            /** @var $gcp Gcps */
            $gcp = Gcps::find(false)->andWhere(['id' => $obj->getGcpId()])->one();
            $title = self::$listTitle[$stage] . '</br> «' . $gcp->getTitle() . '»';
        } elseif ($obj instanceof Mvps) {
            $title = self::$listTitle[$stage] . '</br> «' . $obj->getTitle() . '»';
        } elseif ($obj instanceof ConfirmMvp) {
            /** @var $mvp Mvps */
            $mvp = Mvps::find(false)->andWhere(['id' => $obj->getMvpId()])->one();
            $title = self::$listTitle[$stage] . '</br> «' . $mvp->getTitle() . '»';
        } elseif ($obj instanceof BusinessModel) {
            /** @var $mvp Mvps */
            $mvp = Mvps::find(false)->andWhere(['id' => $obj->getMvpId()])->one();
            $title = self::$listTitle[$stage] . '</br> для «' . $mvp->getTitle() . '»';
        }

        return $title;
    }

}