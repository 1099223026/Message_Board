<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\assets\AppAsset;
/**
 * Created by PhpStorm.
 * User: 向国平
 * Date: 2016/11/20
 * Time: 18:18
 */
// 自动加载资源(js)
AppAsset::register( $this );
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>留言板</title>
    <?php echo Html::cssFile( '@web/css/message_board.css' ); ?>
    <!-- 导入js资源 -->
    <?php echo AppAsset::addPageScript($this,'@web/js/message_board.js'); ?>
</head>
<body>
    <?php $form = ActiveForm::begin([ 'id' => 'form-signup', 'action' => 'index.php?r=site/board-content-post' ]); ?>
        <h3 class="green">留言板</h3>
        <hr class="grey-bdt"/>
        <br/>
        <img src="img/user-img.jpg" class="user-img" alt="用户头像">
        <br/><br/>
        <div class="pst-rel">
            <?= $form->field($model, 'content')->textArea([ 'id' => 'tesxtArea', 'rows' => 4, 'placeholder' => '说点什么吧...', 'resize' => 'none' ])->label( false ); ?>
            <span class="limit-num text-muted">5/300</span>
        </div>
        <?= Html::submitButton( '提交评论', [ 'id' => 'submit', 'class' => 'btn btn-success pull-right', 'name' => 'signup-button' ] ); ?>
    <?php ActiveForm::end(); ?>
    <?php foreach( $message as $key => $data ){ ?>
        <hr class="grey-bdt wdt-percent pull-right"/>
        <!-- user-discuss -->
        <div class="pull-left wdt-percent">
            <div class="row">
                <div class="col-xs-2">
                    <img class="pull-right user-img" src="img/user-img2.jpg" alt="用户头像">
                </div>
                <div class="col-xs-9">
                    <div class="row">
                        <h5 class="col-xs-12 font-wgt-800">
                            <?= $data['userName']; ?>
                        </h5>
                        <div class="col-xs-12 mgn-btm-6">
                            <?= $data['content']; ?>
                        </div>
                        <div class="col-xs-12">
                            <span class="pdgl-clear col-xs-6 text-muted font-12">
                                时间:<?= $data['time']; ?>
                            </span>
                            <a class="thumb col-xs-5 pdg-clear text-right thumb" href="javascript:void(0);">
                                <span class="text-muted font-15 glyphicon glyphicon-thumbs-up"></span>
                            </a>
                            <span class="text-muted font-14 pdg-clear col-xs-1">
                                &nbsp;<span class="thumb-num"><?= $data['thumbNum']; ?></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1"></div>
            </div>
        </div>
    <?php } ?>
    <hr class="grey-bdt wdt-percent pull-right"/>
</body>
</html>