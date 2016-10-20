<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author boostrap
 * @since 2.0
 */
class FileInputAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'plugins/fileinput/css/fileinput.css',
    ];
    public $js = [
        'plugins/fileinput/js/fileinput.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}