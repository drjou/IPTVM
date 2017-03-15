<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/webfont.css',
        'css/iconfont.css',
        'css/site.css',
        'css/iptvm.css',
        'plugins/metisMenu/metisMenu.min.css',
        'plugins/font-awesome/css/font-awesome.min.css',
    ];
    public $js = [
        'js/iptvm.js',
        'plugins/metisMenu/metisMenu.min.js',
        'js/iconfont.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
