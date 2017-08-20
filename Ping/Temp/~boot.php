<?php
/**
	* 父类Controller
	*/
	class Controller
	{
		
		private $var = array();

		public function __construct()
		{
			if (method_exists( $this, '__init')) {
				 $this->__init();
			}
			if (method_exists( $this, '__autoload')) {
				 $this->__autoload();
			}
		}
		protected function success($msg ='操作成功',$url =null,$time =0)
		{
			$url = $url?$url:"javascript:back(-1)";
			include APP_TPL_PATH.'/success.html';
		}

		protected function error($msg ='操作失败',$url =null,$time =0)
		{
			$url = $url?$url:"javascript:;";
			include APP_TPL_PATH.'/error.html';
		}

		protected function display($tpl = null)
		{
			if (is_null($tpl)) {
				$path = APP_TPL_PATH.'/'.CONTROLLER.'/'.ACTION.'.html';
			}else{
				$suffix = strrchr($tpl, '.');
				$tpl = empty($suffix)? $tpl.'.html':$tpl;
				$path =  APP_TPL_PATH.'/'.CONTROLLER.'/'.$tpl;
			}

			if(!is_file($path)) halt($path.'模板文件不存在');	
			$arr =  $this->var;
			extract($arr);
			include $path;
		}

		protected function assign($var,$value)
		{
			 $this->var[$var] = $value;
		}


	}
/**
* Log
*/
class Log
{
	
	static public function write($msg,$level="ERROR",$type=3,$dest=null)
	{
		if (!C('SAVE_LOG'))  return;
		if (is_null($dest)) {
			$dest = LOG_PATH.'/'.date('Y-m-d').".log";
		}
		if (is_dir(LOG_PATH))  error_log("[TIME]".date("Y-m-d H:i:s")." {$level}: {$msg}\r\n",$type,$dest);
	}
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
