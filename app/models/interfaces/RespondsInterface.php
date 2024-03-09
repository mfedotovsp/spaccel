<?php

namespace app\models\interfaces;

use yii\db\ActiveQuery;

/**
 * Interface RespondsInterface
 * @package app\models\interfaces
 */
interface RespondsInterface
{
    /**
     * Получить модель подтверждения
     * @return mixed|ActiveQuery
     */
    public function getConfirm();

    /**
     * Получить интевью респондента
     * @return mixed|ActiveQuery
     */
    public function getInterview();

    /**
     * Получить ответы респондента на вопросы
     * @return mixed|ActiveQuery
     */
    public function getAnswers();

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * Установить id подтверждения
     * @param int $confirmId
     */
    public function setConfirmId(int $confirmId);

    /**
     * Получить id подтверждения
     * @return int
     */
    public function getConfirmId(): int;

    /**
     * Установить имя респондента
     * @param string $name
     */
    public function setName(string $name);

    /**
     * Получить имя респондента
     * @return string
     */
    public function getName(): string;

    /**
     * @param array $params
     */
    public function setParams(array $params): void;

    /**
     * @return string
     */
    public function getInfoRespond(): string;

    /**
     * @param string $info_respond
     */
    public function setInfoRespond(string $info_respond);

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $email
     */
    public function setEmail(string $email);

    /**
     * @return int|null
     */
    public function getDatePlan(): ?int;

    /**
     * @param int $datePlan
     */
    public function setDatePlan(int $datePlan);

    /**
     * @return string
     */
    public function getPlaceInterview(): string;

    /**
     * @param string $place_interview
     */
    public function setPlaceInterview(string $place_interview);

    /**
     * @return int|null
     */
    public function getContractorId(): ?int;

    /**
     * @param int $contractor_id
     */
    public function setContractorId(int $contractor_id);

    /**
     * @return int|null
     */
    public function getTaskId(): ?int;

    /**
     * @param int $task_id
     */
    public function setTaskId(int $task_id);
}