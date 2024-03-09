<?php


namespace app\models;

/**
 * Типы коммуникаций между администратором и экспертом
 *
 * Class CommunicationTypes
 * @package app\models
 */
class CommunicationTypes
{

    public const MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE = 100;
    public const MAIN_ADMIN_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE = 150;
    public const EXPERT_ANSWERS_QUESTION_ABOUT_READINESS_CONDUCT_EXPERTISE = 200;
    public const MAIN_ADMIN_APPOINTS_EXPERT_PROJECT = 300;
    public const MAIN_ADMIN_DOES_NOT_APPOINTS_EXPERT_PROJECT = 350;
    public const MAIN_ADMIN_WITHDRAWS_EXPERT_FROM_PROJECT = 400;

    public const USER_ALLOWED_PROJECT_EXPERTISE = 1000;
    public const USER_ALLOWED_SEGMENT_EXPERTISE = 1001;
    public const USER_ALLOWED_CONFIRM_SEGMENT_EXPERTISE = 1002;
    public const USER_ALLOWED_PROBLEM_EXPERTISE = 1003;
    public const USER_ALLOWED_CONFIRM_PROBLEM_EXPERTISE = 1004;
    public const USER_ALLOWED_GCP_EXPERTISE = 1005;
    public const USER_ALLOWED_CONFIRM_GCP_EXPERTISE = 1006;
    public const USER_ALLOWED_MVP_EXPERTISE = 1007;
    public const USER_ALLOWED_CONFIRM_MVP_EXPERTISE = 1008;
    public const USER_ALLOWED_BUSINESS_MODEL_EXPERTISE = 1009;

    public const USER_DELETED_PROJECT = 2000;
    public const USER_DELETED_SEGMENT = 2001;
    public const USER_DELETED_PROBLEM = 2003;
    public const USER_DELETED_GCP = 2005;
    public const USER_DELETED_MVP = 2007;


    /**
     * @return array
     */
    public static function getListTypes(): array
    {
        return [
            self::MAIN_ADMIN_ASKS_ABOUT_READINESS_CONDUCT_EXPERTISE,
            self::MAIN_ADMIN_WITHDRAWS_REQUEST_ABOUT_READINESS_CONDUCT_EXPERTISE,
            self::EXPERT_ANSWERS_QUESTION_ABOUT_READINESS_CONDUCT_EXPERTISE,
            self::MAIN_ADMIN_APPOINTS_EXPERT_PROJECT,
            self::MAIN_ADMIN_DOES_NOT_APPOINTS_EXPERT_PROJECT,
            self::MAIN_ADMIN_WITHDRAWS_EXPERT_FROM_PROJECT
        ];
    }
}