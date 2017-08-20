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

?>