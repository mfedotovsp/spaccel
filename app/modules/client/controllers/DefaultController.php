<?php

namespace app\modules\client\controllers;


/**
 * Default controller for the `client` module
 */
class DefaultController extends AppClientController
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
