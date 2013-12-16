<?php

include("../../../wp-config.php");
weixin_robot_custom_replies_create_table();
weixin_robot_messages_create_table();
weixin_robot_credits_create_table();
weixin_robot_users_create_table();

echo '已经手工创建自定义回复和数据库统计所需要的数据表';