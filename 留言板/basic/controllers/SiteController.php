<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\BoardForm;
use yii\helpers\Url;
use Debug;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    // 点赞数控制器
    public function actionThumbPost(){
        // 接收前台post来的 点赞数 和 下标
        $request    = Yii::$app->request;
        $thumbNum   = $request->post( 'thumbNum' );
        $index      = $request->post( 'index' );
        // 根据获取的参数改变数据库中点赞数值
        $redis = Yii::$app->redis;
        $redis->lset( 'thumbNum', $index, $thumbNum );
        $thumbNum = $redis->lindex( 'thumbNum', $index );
        // 返回从数据库获取到的点赞数到前台
        $response = Yii::$app->response;
        $response->data = $thumbNum;
    }
    // 留言板控制器
    public function actionDefault()
    {
        // 加载utf-8文档头
        header( 'Content-type:text/html;charset=utf-8');
        $model = new BoardForm();
        // 获取留言内容
        $redis = Yii::$app->redis;
        $message = $model->getMessage( $redis );
        if ( $message == -1 ){
            // 1、输出提示 "redis数据库中各个list长度不匹配 => 记录不完全"
            echo 'redis数据库中各个list长度不匹配 => 记录不完全';
            // 2、通过邮件告知管理员
            exit;
        }
        // 将输入内容清空
        $model->content = '';
        return $this->render( 'message_board', [ 'model' => $model, 'message' => $message ] );
    }
    // test
    public function actionTest(){
        header( 'Content-type:text/html;charset=utf-8' );
        $redis = Yii::$app->redis;
        $model = new BoardForm();
        $data = $model->getMessage( $redis );
//        var_dump( $data );
        if ( $data == -1 ){
            // 1、输出提示 "redis数据库中各个list长度不匹配 => 记录不完全"
            echo 'redis数据库中各个list长度不匹配 => 记录不完全';
            // 2、通过邮件告知管理员
            exit;
        }
    }

    // 提交留言板内容
    public function actionBoardContentPost()
    {
        // 获取 model
        $model = new BoardForm();

        // 通过rules验证数据是否合法
        if( $model->load( Yii::$app->request->post() ) && $model->validate() ){
            // 验证成功
            // 建立数据库访问
            $redis = Yii::$app->redis;
            // 给model填充数据 ( content 已由用户提交到 model, thumbNum创建为0 )
            $model->time        = $model->presentTime; // 留言时间 -> list
            $model->userName    = 'test';  // 用户名 -> list
            $model->thumbNum    = '0';  // 点赞数 -> list
            // 输出将要保存至数据库的数据
//            Debug::debug( $model->time );
//            Debug::debug( $model->userName );
//            Debug::debug( $model->content );

            // 保存至redis数据库
            // -> list ( rpush：在末尾插入， lpush：相反 )
            $redis->rpush( 'userName', $model->userName );
            $redis->rpush( 'time', $model->time );
            $redis->rpush( 'content', $model->content );
            $redis->rpush( 'thumbNum', $model->thumbNum );

            // 返回到留言板控制器
            // 路由跳转
            $this->redirect( array( 'default' ) );
        } else{
            // 验证失败
            echo 'boardForm check error!!';
            exit;
        }
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
