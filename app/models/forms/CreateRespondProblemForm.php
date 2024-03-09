<?php


namespace app\models\forms;

use app\models\ConfirmProblem;
use app\models\Problems;
use app\models\interfaces\ConfirmationInterface;
use app\models\Projects;
use app\models\RespondsProblem;
use app\models\Segments;
use app\models\User;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;

/**
 * Форма создания респондента на этапе подтверждения гипотезы проблемы
 *
 * Class CreateRespondProblemForm
 * @package app\models\forms
 */
class CreateRespondProblemForm extends FormCreateRespondent
{

    /**
     * CreateRespondProblemForm constructor.
     *
     * @param ConfirmProblem $confirm
     * @param array $config
     */
    public function __construct(ConfirmProblem $confirm, array $config = [])
    {
        $this->setCreatorAnswers();
        $this->setCacheManager();
        $this->setCachePathForm(self::getCachePath($confirm));
        if ($cache = $this->getCacheManager()->getCache($this->getCachePathForm(), self::CACHE_NAME)) {
            $className = explode('\\', self::class)[3];
            foreach ($cache[$className] as $key => $value) {
                $this[$key] = $value;
            }
        }

        parent::__construct($config);
    }

    /**
     * Получить путь к кэшу формы
     *
     * @param ConfirmationInterface $confirm
     * @return string
     */
    public static function getCachePath(ConfirmationInterface $confirm): string
    {
        /**
         * @var ConfirmProblem $confirm
         */
        $problem = Problems::findOne($confirm->getProblemId());
        $segment = Segments::findOne($problem->getSegmentId());
        $project = Projects::findOne($problem->getProjectId());
        $user = User::findOne($project->getUserId());
        return '../runtime/cache/forms/user-'.$user->getId().'/projects/project-'.$project->getId().'/segments/segment-'.$segment->getId().'/problems/problem-'.$problem->getId().'/confirm/formCreateConfirm/';
    }

    /**
     * @return RespondsProblem
     * @throws ErrorException
     * @throws NotFoundHttpException
     */
    public function create (): RespondsProblem
    {
        $model = new RespondsProblem();
        $model->setConfirmId($this->getConfirmId());
        $model->setName($this->getName());

        if ($model->save()) {
            // Добавление пустых ответов на вопросы для нового респондента
            $this->getCreatorAnswers()->create($model);
            // Удаление кэша формы создания
            $this->getCacheManager()->deleteCache($this->getCachePathForm());

            return $model;
        }
        throw new NotFoundHttpException('Ошибка. Неудалось добавить нового респондента');
    }

    /**
     * @param $attr
     */
    public function uniqueName($attr)
    {
        $models = RespondsProblem::findAll([
            'confirm_id' => $this->getConfirmId(),
            'task_id' => null,
            'contractor_id' => null
        ]);

        foreach ($models as $item){

            if (mb_strtolower(str_replace(' ', '', $this->getName())) === mb_strtolower(str_replace(' ', '',$item->getName()))){

                $this->addError($attr, 'Респондент с таким именем «'. $this->getName() .'» уже существует!');
            }
        }
    }

}