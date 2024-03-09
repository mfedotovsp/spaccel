<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Класс, который хранит информацию об образовании исполнителей
 *
 * Class ContractorProject
 * @package app\models
 *
 * @property int $id                                    Идентификатор записи
 * @property int $contractor_id                         Идентификатор исполнителя
 * @property string $educational_institution            Учебное заведение
 * @property string $faculty                            Факультет
 * @property string $course                             Курс
 * @property int|null $finish_date                      Дата окончания
 */
class ContractorEducations extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'contractor_educations';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['contractor_id', 'educational_institution', 'faculty'], 'required'],
            [['contractor_id'], 'integer'],
            ['finish_date', 'safe'],
            [['educational_institution', 'faculty', 'course'], 'trim'],
            [['educational_institution', 'faculty', 'course'], 'string', 'max' => 255],
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'educational_institution' => 'Учебное заведение',
            'faculty' => 'Факультет',
            'course' => 'Курс',
            'finish_date' => 'Дата окончания',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getContractor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'contractor_id']);
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
    public function getContractorId(): int
    {
        return $this->contractor_id;
    }

    /**
     * @param int $contractor_id
     */
    public function setContractorId(int $contractor_id): void
    {
        $this->contractor_id = $contractor_id;
    }

    /**
     * @return string
     */
    public function getEducationalInstitution(): string
    {
        return $this->educational_institution;
    }

    /**
     * @param string $educational_institution
     */
    public function setEducationalInstitution(string $educational_institution): void
    {
        $this->educational_institution = $educational_institution;
    }

    /**
     * @return string
     */
    public function getFaculty(): string
    {
        return $this->faculty;
    }

    /**
     * @param string $faculty
     */
    public function setFaculty(string $faculty): void
    {
        $this->faculty = $faculty;
    }

    /**
     * @return string
     */
    public function getCourse(): string
    {
        return $this->course;
    }

    /**
     * @param string $course
     */
    public function setCourse(string $course): void
    {
        $this->course = $course;
    }

    /**
     * @return int|null
     */
    public function getFinishDate(): ?int
    {
        return $this->finish_date;
    }

    /**
     * @param string $finish_date
     */
    public function setFinishDate(string $finish_date): void
    {
        if ($finish_date !== '') {
            $this->finish_date = strtotime($finish_date);
        }
    }
}