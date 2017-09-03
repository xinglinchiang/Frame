<?php

return array(
    //验证码
    'CODE_LEN' => 5,
    //默认时区
    'DEFAULT_TIME_ZONE' =>'PRC',
    //是否开启会话
    'SESSION_AUTO_START' => TRUE,
    //
    'VAR_CONTROLLER' => 'c',
    //
    'VAR_ACTION' => 'a',
    //
    'SAVE_LOG' => TRUE,
    //跳转地址
    'ERROR_URL' => '',
    //
    'ERROR_MSG' => '网站出错了，请稍后再试',
    //自动加载COMMON的文件
    'AUTO_LOAD_FILE' => array(
                'func.php'
            ),

    //数据库配置
    'DB_MS' => 'mysql',
    'DB_CHARSET' => 'utf8',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => 3306,
    'DB_USER' => 'root',
    'DB_PASSWORD' => '',
    'DB_DATABASE' => '',
    'DB_PREFIX' => '',
    //是否开启smarty
    'SMARTY_ON' => true,
    //smarty配置项
    'LEFT_DELIMITER' => "{",
    'RIGHT_DELIMITER' => "}",
    'CACHE_ON' => true,
    'CACHE_TIME' => 60,




);


?>