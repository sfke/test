<?php	return array ( 0 => 'id', 1 => 'channel', 2 => 'siteid', 3 => 'msg_title', 4 => 'msg_content', 5 => 'msg_company', 6 => 'msg_name', 7 => 'msg_tel', 8 => 'msg_qq', 9 => 'check', '_autoinc' => true, '_pk' => 'id', '_type' => array ( 'id' => 'int(11)', 'channel' => 'smallint(6)', 'siteid' => 'smallint(3) unsigned', 'msg_title' => 'varchar(250)', 'msg_content' => 'mediumtext', 'msg_company' => 'varchar(40)', 'msg_name' => 'varchar(40)', 'msg_tel' => 'varchar(40)', 'msg_qq' => 'int(11)', 'check' => 'enum(\'未审核\',\'已审核\')', ), );?>