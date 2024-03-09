<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/style.css',
        'css/fonts.css',
        'css/simplebar.css',
    ];
    public $js = [
        'js/jquery.cookie.js',
        'js/jquery.accordion.js',
        'js/main.js',
        'js/simplebar.min.js',
        'js/message_connect.js',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD,];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
