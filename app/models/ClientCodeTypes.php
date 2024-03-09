<?php

namespace app\models;

class ClientCodeTypes
{
    public const REGISTRATION_CODE_FOR_SIMPLE_USER = 59045234;
    public const REGISTRATION_CODE_FOR_TRACKER = 56345945;
    public const REGISTRATION_CODE_FOR_MANAGER = 23498654;
    public const REGISTRATION_CODE_FOR_EXPERT = 67856456;
    public const REGISTRATION_CODE_FOR_CONTRACTOR = 45656346;

    /**
     * @param int $type
     * @return int|null
     */
    public static function getUserRoleByType(int $type): ?int
    {
        if ($type === self::REGISTRATION_CODE_FOR_SIMPLE_USER) {
            return User::ROLE_USER;
        }
        if ($type === self::REGISTRATION_CODE_FOR_TRACKER) {
            return User::ROLE_ADMIN;
        }
        if ($type === self::REGISTRATION_CODE_FOR_MANAGER) {
            return User::ROLE_MANAGER;
        }
        if ($type === self::REGISTRATION_CODE_FOR_EXPERT) {
            return User::ROLE_EXPERT;
        }
        if ($type === self::REGISTRATION_CODE_FOR_CONTRACTOR) {
            return User::ROLE_CONTRACTOR;
        }
        return null;
    }
}