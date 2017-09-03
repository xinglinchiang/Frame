<?php

final class  Ping
{
    static public function run()
    {
        self::_set_constant();
        defined('DEBUG') || define('DEBUG', false);
        if (DEBUG) {
            self::_import_file();
            self::_create_dir();
        }else{
            error_reporting(0);
            require TEMP_PATH.'/~boot.php';
        }
        Application::run();
    }

    static private function _set_constant()
    {
        $path = str_replace('\\','/',__FILE__);
        define('PING_PATH',dirname($path));
        define('CONFIG_PATH',PING_PATH.'/Config');
        define('DATA_PATH',PING_PATH.'/Data');
        define('LIB_PATH',PING_PATH.'/Lib');
        define('CORE_PATH',LIB_PATH.'/Core');
        define('FUNCTION_PATH',LIB_PATH.'/Function');

        define('ROOT_PATH',dirname(PING_PATH));
        //应用目录
        define('APP_PATH',ROOT_PATH.'/'.APP_NAME);
        define('APP_CONFIG_PATH',APP_PATH.'/Config');
        define('APP_CONTROLLER_PATH',APP_PATH.'/Controller');
        define('APP_TPL_PATH',APP_PATH.'/Tpl');
        define('APP_PUBLIC_PATH',APP_TPL_PATH.'/Public');


        //创建公共
        define('COMMON_PATH', ROOT_PATH.'/Common');
        //公共配置项
        define('COMMON_CONFIG_PATH', COMMON_PATH.'/Config');
        //公共模型
        define('COMMON_MODEL_PATH', COMMON_PATH.'/Model');
        //公共库，文件
        define('COMMON_LIB_PATH', COMMON_PATH.'/Lib');
        
         //扩展目录
        define('EXTENDS_PATH', PING_PATH.'/Extends');
        define('TOOL_PATH', EXTENDS_PATH.'/Tool');
        define('ORG_PATH', EXTENDS_PATH.'/Org');
        


        //临时目录
        define('TEMP_PATH',  ROOT_PATH.'/Temp');
        define('LOG_PATH',  TEMP_PATH.'/Log');

        define('IS_POST', $_SERVER['REQUEST_METHOD']=='POST'?true:false);
        if (isset($_SERVER['HTTP_X_REQUEST_WITH']) && $_SERVER['HTTP_X_REQUEST_WITH'] == 'XMLHttpRequest') {
            define('IS_AJAX', ture);
        }else{
            define('IS_AJAX', false);
        }
        define('APP_COMPILE_PATH', TEMP_PATH.'/'.APP_NAME.'/Compile');
        define('APP_CACHE_PATH',TEMP_PATH.'/'.APP_NAME.'/Cache');

    }

    static private function _create_dir()
    {
        $arr = array(
            APP_PATH,
            APP_CONFIG_PATH,
            APP_CONTROLLER_PATH,
            APP_TPL_PATH,
            APP_PUBLIC_PATH,
            TEMP_PATH,
            LOG_PATH,
            COMMON_LIB_PATH,COMMON_MODEL_PATH,COMMON_CONFIG_PATH,
            APP_COMPILE_PATH,APP_CACHE_PATH,

        );
        foreach ($arr as $v){
            is_dir($v) || mkdir($v,0777,true);
        }

        is_file(APP_TPL_PATH.'/success.html') || copy(DATA_PATH.'/Tpl/success.html', APP_TPL_PATH.'/success.html');
        is_file(APP_TPL_PATH.'/error.html') || copy(DATA_PATH.'/Tpl/error.html', APP_TPL_PATH.'/error.html');
        is_file(APP_TPL_PATH.'/halt.html') || copy(DATA_PATH.'/Tpl/error.html', APP_TPL_PATH.'/halt.html');
    }

    static private function _import_file()
    {
        $fileArr = array(
           
            ORG_PATH.'/Smarty/Smarty.class.php', 
            CORE_PATH.'/SmartyView.class.php',  
            CORE_PATH.'/Controller.class.php',  
            CORE_PATH.'/Log.class.php', 
            FUNCTION_PATH.'/Function.php',
            CORE_PATH.'/Application.class.php',
        );
        $str = '';

        foreach ($fileArr as $v){
            $str .= trim(substr(file_get_contents($v),5,-2))."\r\n";
            require_once  $v;
        }
        $str ="<?php\r\n".$str;
        file_put_contents(TEMP_PATH.'/~boot.php', $str) || die('access not allow');
    }

}

Ping::run();


?>