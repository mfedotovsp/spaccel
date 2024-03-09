<?php


namespace app\models\interfaces;


use app\models\forms\FormCreateConfirm;

interface CreateRespondsOnConfirmFirstStepInterface
{

    /**
     * Создание новых респондентов на первом шаге подтверждения
     * @param ConfirmationInterface $confirm
     * @param FormCreateConfirm $form
     */
    public function create(ConfirmationInterface $confirm, FormCreateConfirm $form);
}