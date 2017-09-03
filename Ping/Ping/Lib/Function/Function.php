<?php

    //
    function K($model)
    {
        $model .= 'Model';
        return new $model;
    }

    //
    function M($table)
    {
        $obj = new Model($table);
        return $obj;
    }

    //打印常量
    function p_conost()
    {
        $conost = get_defined_constants(true);
        P($conost['user']);
    }


    function halt($error,$level="ERROR",$type=3,$dest=null)
    {
        if (is_array($error)) {
            Log::write($error['message'],$level,$type,$dest);
        }else{
            Log::write($error,$level,$type,$dest);
        }
        $e = array();
        if (DEBUG) {
            if (!is_array($error)) {
                $trace = debug_backtrace();
               // p($trace);
                $e['message'] = $error;
                $e['file'] = $trace[0]['file'];
                $e['line'] = $trace[0]['line'];
                $e['function'] = $trace[0]['function'];
                $e['class'] = isset($trace[0]['class'])?$trace[0]['class']:'';
                ob_start();
                debug_print_backtrace();
                $e['trace'] = htmlspecialchars(ob_get_clean());
            }else{
                $e = $error;
            }
        }else{
            $url = C('ERROR_URL');
            if ($url) {
                go($url);
            }else{
                $e['message'] = C('ERROR_MSG');
            }
        }
        include DATA_PATH.'/Tpl/halt.html';
        die;
    }

    //页面跳转
    function go($url,$time=0,$msg='')
    {
        if (!headers_sent()) {
            $time == 0?header('Location:'.$url):header("Refresh:{$time};url={$url}");
            die($msg);
        }else{

            echo "<meta http-equiv='Refresh' Content='{$time};url={$url}' >";
            if ($time) {
                die($msg);
            }
        }
    }

    function P($arr = null)
    {
        if (is_bool($arr)) {
            var_dump($arr);
        }else if(is_null($arr)) {
            var_dump($arr);
        }else{
            echo '<pre style="background: #f5f5f5;padding: 10px;list-style: none;border-radius: 5px;line-height: 18px;border:1px solid red;">';
            print_r($arr);
            echo '</pre>';
        }
    }

    //加载配置项
    //$sysConfig $userConfig
    //C('CODE_LEN')
    //C('CODE_LEN',10) 临时改变配置项
    //C() 读取所有配置项
    function C($var = null,$value=null)
    {
        static $config = array();
        if (is_array($var)){
            $config = array_merge($config,array_change_key_case($var,CASE_UPPER));
            return;
        }

        if (is_string($var)){
            $var = strtoupper($var);
            //改变配置项
            if (!is_null($value)){
                $config[$var] = $value;
                return;
            }
            return isset($config[$var])?$config[$var]:'';
        }
        if (is_null($var) && is_null($value)) {
            return $config;
        }

    }



?>