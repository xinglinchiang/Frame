<?php

/**
* 
*/
class UserModel extends Model
{
	
	public $table = 'user';

	public function get_all_data()
	{
		return  $this->all();
	}

}



?>