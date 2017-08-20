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
);

?>