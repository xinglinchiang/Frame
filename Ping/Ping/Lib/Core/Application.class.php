<?php
final class  Application
{
    static public function run()
    {
        self::_init();
        set_error_handler(array(__CLASS__,'error'));
        register_shutdown_function(array(__CLASS__,'fatal_error'));
        self::_set_url();
        self::_user_import();
        spl_autoload_register(array(__CLASS__,'_autoload'));
        self::_create_demo();
        self::_app_run();
    }

    static public function fatal_error()
    {
        $e = error_get_last();
        if ($e) {
            self::error($e['type'],$e['message'],$e['file'],$e['line']);
        }
    }

    //错误处理
    static public function error($errorno,$error,$file,$line)
    {
        switch ($errorno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $msg = $error.$file."第{$line}行";
                halt($msg);
                break;

            case E_STRICT:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            default:
                if (DEBUG) {
                    include  DATA_PATH.'/Tpl/notice.html';
                }
                break;
        }
    }

    //导入用户Common中文件
    static private function _user_import()
    {
        $fileArr = C('AUTO_LOAD_FILE');
        if (is_array($fileArr) && !empty($fileArr)) {
            foreach ($fileArr as $v) {
                require_once COMMON_LIB_PATH.'/'.$v;
            }
        }
    }

    static private function _app_run()
    {
        $c = isset($_GET[C('VAR_CONTROLLER')])?$_GET[C('VAR_CONTROLLER')]:'Index';
        $a = isset($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:'index';
        define('CONTROLLER', $c);
        define('ACTION', $a);
        
        $c = $c.'Controller';
        if (class_exists($c)) {
            $obj = new $c();
        }else{
            $obj = new EmptyController();            
        }

        $obj->$a();


    }

    //创建demo
    static private function _create_demo()
    {
        $path = APP_CONTROLLER_PATH.'/IndexController.class.php';
        $str =<<<str
<?php
    class IndexController extends Controller
    {
        Public function index(){
           header('content-type:text/html;charset=utf-8;');
           echo 'On The Way ...';
        }
    }
?>
str;
    is_file($path) || file_put_contents($path,$str);
    }

    //自动载入
    static private function _autoload($className)
    {
         
        switch (true) {
            case strlen($className) >10 &&  substr($className, -10) == 'Controller':
                $path = APP_CONTROLLER_PATH.'/'.$className.'.class.php';

                if(!is_file($path)) {
                    $emptyPath = APP_CONTROLLER_PATH.'/EmptyController.class.php';
                    if (is_file($emptyPath)) {
                        include $emptyPath;
                        return;
                    }else{
                        halt('控制器未找到');
                    }
                }
                include $path;
                break;
            //UserModel
            case strlen($className)>5 && substr($className, -5) == 'Model':
                $path = COMMON_MODEL_PATH.'/'.$className.'.class.php';
                include $path;
                break;
            
            default:
                $path = TOOL_PATH.'/'.$className.'.class.php';
                if(!is_file($path)) halt('类未找到');
                include $path;
                break;
        }

         
    }

    static private function _set_url()
    {
        $path = 'http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['SCRIPT_FILENAME'];
        $path = str_replace('\\','/',$path);
        define('__APP__',$path);
        define('__ROOT__',dirname(__APP__));
        define('__TPL__',__ROOT__.'/Tpl');
        define('__PUBLIC__',__TPL__.'/Public');

    }
    //初始化
    static private function _init()
    {
        //加载系统默认配置项
        C(include CONFIG_PATH.'/Config.php');

        //加载公共配置项
        $commonPath = COMMON_CONFIG_PATH.'/Config.php';
        $commonConfig = <<<str
<?php
    return array(
        //'配置项' => '配置值'
    );
?>
str;
        is_file($commonPath) || file_put_contents($commonPath,$commonConfig);
         C(include $commonPath);

        //用户配置项
        $userPath = APP_CONFIG_PATH.'/Config.php';
        $userConfig = <<<str
<?php
    return array(
        //'配置项' => '配置值'
    );
?>
str;
        is_file($userPath) || file_put_contents($userPath,$userConfig);
        C(include $userPath);

        //设置默认时区
        date_default_timezone_set(C('DEFAULT_TIME_ZONE'));
        C('SESSION_AUTO_START') && session_start();
    }
}


?>