﻿<?php
/**
 * 输出调试信息
 * @author XGP
 * @param $data 要输出的调试数据
 * @param $isStop 是否结束程序
 * */
class Debug
{
    function __construct()
    {
    }

    public static function debug( $data, $isStop = false )
    {
        echo "<hr/>";
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']
            === 'XMLHttpRequest' || isset($_GET['_isAjax']) || isset($_POST['_isAjax']);

        $trace = (new \Exception())->getTrace()[0];
        if($isAjax){
            header('Content-type:application/json;charset=utf-8');
            exit(json_encode(array(
                'file' => $trace['file'],
                'line' => $trace['line'],
                'dataStr' => var_export($data, true),
                'data' => $data,
            )));
        }else{
            echo '<br/>文件行号:' . $trace['file'] . ':' . $trace['line'];
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }

        $isStop && exit;
        echo "<hr/>";
    }
}