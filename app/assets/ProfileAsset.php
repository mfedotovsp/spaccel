<?php


namespace app\assets;

use yii\web\AssetBundle;

class ProfileAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/style.css',
        'css/fonts.css',
        'css/simplebar.css',
        'css/croppie.css',
    ];
    public $js = [
        'js/jquery.cookie.js',
        'js/jquery.accordion.js',
        'js/main.js',
        'js/simplebar.min.js',
        'js/croppie.min.js',
        'js/message_connect.js',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD,];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}