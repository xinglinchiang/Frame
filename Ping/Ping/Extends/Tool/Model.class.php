<?php 

/**
* 
*/
class Model
{
	//保存链接信息

	public static $pdo = null;
	//保存表名

	protected  $table = null;
	//初始化表信息

	private $opt;
	//记录sql

	public static $sqls = array();

	
	function __construct($table = null)
	{
		 $this->table = is_null($table) ? C('DB_PREFIX').$this->table:C('DB_PREFIX').$table;
		//链接数据库

		 $this->pdo();
		 //初始化sql
		  $this->_opt();

	}

	public function delete()
	{
		if(empty($this->opt['where'])){halt("删除语句必须有where条件");}

		$sql = "DELETE FROM ".  $this->table .  $this->opt['where'] ;

		return $this->exec($sql);
	}

	public function exec($sql)
	{
		self::$sqls[] = $sql;

		$pdo = self::$pdo;

		$bool = $pdo->exec($sql);

		if(is_object($bool)){ halt('请用query方法按发送sql');}

		if ($bool) {
			return $bool;
		}else{
			halt('sql error' . $sql);
		}
	}

	public function query($sql)
	{
		self::$sqls[] = $sql;

		$pdo = self::$pdo;

		$result = $pdo->query($sql);

		$error = $pdo->errorInfo();
		//P($error);

		if ($error[2])  halt('sql错误:'.$error[1].'@'.$error[2].'<br/>SQL:'.$sql);

		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		//$result->free(); //TODO
		 $this->_opt();

		return $row;
	}

	public function limit($limit)
	{
		$this->opt['limit'] = " limit " . $limit;
		return  $this;
	}

	public function order($order)
	{
		$this->opt['order'] = " ORDER BY " . $order;
		return  $this;
	}

	public function where($where)
	{
		$this->opt['where'] = " WHERE " . $where;
		return  $this;
	}

	public function field($field)
	{
		$this->opt['field'] = $field;
		return  $this;
	}

	public function one()
	{
		return $this->find();
	}

	public function find()
	{
		$data = $this->limit(1)->all();
		return $data[0];
	}

	//查询所有数据
	public function all()
	{
		$sql = "SELECT " .  $this->opt['field'] ." FROM ".  $this->table . $this->opt['where'] .  $this->opt['group'] .  $this->opt['having'] .  $this->opt['order'] . $this->opt['limit'];

		//echo $sql;

		return $this->query($sql);
	}

	private function _opt()
	{
		 $this->opt = array(

			 	'field' => '*',

			 	'where' => '',

			 	'group' => '',

			 	'order' => '',

			 	'limit' => '',

			 	'having' => '',
		 	);
	}

	//连接数据库方法,使用PDO方式

	private function pdo()
	{
		if (is_null(self::$pdo)) {
			
			$db = C('DB_DATABASE');

			if (empty($db)) halt('请先配置数据库');

            $dns = C('DB_MS').':host='.C('DB_HOST').';dbname='.C('DB_DATABASE');

            try{

				$pdo = new PDO($dns ,C('DB_USER'),C('DB_PASSWORD'));

            }catch(PDOException $exception){

           		$re = $exception->getMessage();

				if ($re) halt('数据库连接失败，请检查配置项');

            }

            $pdo->query("SET NAMES utf8");

            self::$pdo = $pdo;

		}
	}


	private function _safe_str($str)
	{
		if (get_magic_quotes_gpc()) {
			$str = stripcslashes($str);
		}

		return addslashes($str);
	}

	public function add()
	{
		$data = $_POST;
		$fields = '';
		$values = '';

		foreach ($data as $f => $v) {
			$fields .= "`" .  $this->_safe_str($f)."`,";
			$values .= "'" .  $this->_safe_str($v)."',";
		}

		$fields = rtrim($fields,',');
		$values = rtrim($values,',');

		$sql = "INSERT INTO " . $this->table . "(" .$fields .") VALUES (" .$values .")";

		return  $this->exec($sql);
	}

	public function update($data = '')
	{
		$data = empty($data) ? $_POST :$data;
		$values = '';

		foreach ($data as $f => $v) {
			$values .= "`" .$this->_safe_str($f)."` = '".$this->_safe_str($v). "',";
		}

		$values = rtrim($values,',');
		$sql = "UPDATE ".  $this->table ." SET ". $values  .  $this->opt['where'];

		return $this->exec($sql);

	}


}
 ?>