<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
// 资源管理类
/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    //依赖包
    public $depends = [
        //这里写你的依赖包即可，没有就别写
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    //导入当前页的功能js文件，注意加载顺序，这个应该最后调用
    public static function addPageScript($view, $jsfile) {
        $view->registerJsFile($jsfile, [AppAsset::className(), 'depends' => 'app\assets\AppAsset']);
    }
}
