<?php

namespace app\modules\contractor\controllers;

/**
 * Default controller for the `contractor` module
 */
class DefaultController extends AppContractorController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
