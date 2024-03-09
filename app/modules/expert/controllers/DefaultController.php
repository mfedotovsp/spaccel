<?php

namespace app\modules\expert\controllers;

/**
 * Default controller for the `expert` module
 */
class DefaultController extends AppExpertController
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
