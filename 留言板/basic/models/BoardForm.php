<?php
namespace app\models;
use yii;
use yii\base\Model;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/20
 * Time: 19:47
 */

class BoardForm extends Model
{
    public $content;    // 留言内容
    public $userName;   // 用户名
    public $thumbNum;   // 点赞数
    public $time;   // 留言时间

    public function rules(){
        return [
            [ 'content', 'string', 'length' => [ 5, 300 ] ],
        ];
    }

    // 获取当前时间，并赋给 $time 变量( 格式：Y-m-d H:i:s )
    public function getPresentTime(){
        // 使用 date 函数前，先转换为北京时区
        date_default_timezone_set('PRC');
        $time = date( "Y-m-d H:i:s", time() );
        return $time;
    }

    // 获取留言内容
    /**
     * @param $redis 传入redis小部件
     * @return 正常返回json格式数据, 否则 -1 list长度不匹配
     */
    public function getMessage( $redis ){
        // 获取list长度
        $contentLen     = $redis->llen( 'content' );
        $userNameLen    = $redis->llen( 'userName' );
        $thumbNumLen    = $redis->llen( 'thumbNum' );
        $timeLen        = $redis->llen( 'time' );
        //对比list的长度是否相等( 相等返回1，否则0 )
        $sign = ($contentLen == $userNameLen && $thumbNumLen == $timeLen && $contentLen == $userNameLen ) ? 1 : 0;
        if ( $sign == 1 ){
            // 建立一个保存数据的容器
            $data = array(); // 保存表
            $data1 = array(); // 保存记录
            // 获取各个 list 中的所用元素
            // 从后往前装载数据（ 最近评论在前 ）
            for ( $i = $timeLen-1; $i >= 0; $i-- ){
                $this->time     = $redis->lindex( 'time', $i );
                $this->content  = $redis->lindex( 'content', $i );
                $this->thumbNum = $redis->lindex( 'thumbNum', $i );
                $this->userName = $redis->lindex( 'userName', $i );
                // 保存到容器中( 形成一条记录 )
                $data1[ 'time' ]         = $this->time;
                $data1[ 'content' ]      = $this->content;
                $data1[ 'thumbNum' ]     = $this->thumbNum;
                $data1[ 'userName' ]     = $this->userName;
                // 形成一张表
                $data[ $i ] = $data1;
            }
        } else {
            // 返回错误代码
            return -1;
        }
        return $data;
    }
}