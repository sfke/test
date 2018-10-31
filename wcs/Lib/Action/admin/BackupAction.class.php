<?php
/**
 * @version    JL_WCS 3.0
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * @Author:    LHY CL014
 */


 class BackupAction extends Action {
     public $config = '';                                                        //相关配置
     public $model = '';                                                         //实例化一个model
     public $content;                                                            //内容
     public $dbName = '';                                                        //数据库名
     public $dir_sep = '/';                                                      //路径符号

     //初始化数据
     function _initialize() {
         set_time_limit(0);                                                      //不超时
         ini_set('memory_limit','500M');
         $this->config = array(
             'path' => RUNTIME_PATH.'Backup/'
         );
         $this->dbName = C('DB_NAME');                                           //当前数据库名称
         $this->model = new Model();
     }



     public function show(){

         $path = $this->config['path'];
         $fileArr = $this->MyScandir($path);
         foreach ($fileArr as $key => $value) {
             if ($key > 1) {
                 //获取文件创建时间
                 $fileTime = date('Y-m-d H:i:s', filemtime($path . '/' . $value));
                 $fileSize = filesize($path . '/' . $value) / 1024;
                 //获取文件大小
                 $fileSize = $fileSize < 1024 ? number_format($fileSize, 2) . ' KB' :
                     number_format($fileSize / 1024, 2) . ' MB';
                 //构建列表数组
                 $list[] = array(
                     'name' => $value,
                     'time' => $fileTime,
                     'size' => $fileSize
                 );
             }
         }


         foreach($list as $k=>$v){
             $size[$k] = $v['size'];
             $time[$k] = $v['time'];
             $name[$k] = $v['name'];
         }

         array_multisort($time,SORT_DESC,SORT_STRING, $list);

         $this->assign('list', $list);

         /*position指定以及一些问候信息*/
         $current = "栏目备份与还原";
         $position = getPosition("栏目备份与还原");
         $this->assign('current',$current);
         $this->assign('position',$position);
         $this->assign('welcome',getWelcome());

         $this->display();
     }

     /*/
     *备份栏目表
     */
     public function backupArctype(){
         $table = array("jl_arctype");
         $rs = $this->backup($table);
         if($rs){
             $this->success("备份当前栏目成功！",U('Admin/backup/show'));
         }else{
             $this->success("备份当前栏目失败！",U('Admin/backup/show'));
         }
     }


     /*/
     *清除栏目备份文件
     */
     public function backupClean(){
         $path = $this->config['path'];
         $FilePath = opendir($path);
         while ($filename = readdir($FilePath)) {
            if(file_exists($path.$filename) && is_file($path.$filename) ){
                @unlink($path.$filename);
            }
         }
         $this->success("清空栏目备份成功！",U('Admin/backup/show'));
     }



     //还原数据库
     function recover() {
         $file = $this->_param('file');
         if ($this->recover_file($file)) {
             echo 1;
             //清除缓存
             if(is_dir(TEMP_PATH)){
                 mydel(TEMP_PATH);
             }
             return;
         } else {
             echo -1;
             return;
         }
     }

     //删除数据备份
     function deletebak() {
         $file = $this->_param('file');
         if (@unlink($this->config['path'] . $this->dir_sep . $file)) {
             echo 1; return;
         } else {
             echo -1; return;
         }
     }
     

     /* -
      * +------------------------------------------------------------------------
      * * @ 获取 目录下文件数组
      * +------------------------------------------------------------------------
      * * @ $FilePath 目录路径
      * * @ $Order    排序
      * +------------------------------------------------------------------------
      * * @ 获取指定目录下的文件列表，返回数组
      * +------------------------------------------------------------------------
      */
     private function MyScandir($FilePath = './', $Order = 0) {
         $FilePath = opendir($FilePath);
         while ($filename = readdir($FilePath)) {
             $fileArr[] = $filename;
         }
         $Order == 0 ? sort($fileArr) : rsort($fileArr);
         return $fileArr;
     }
     /*     * ******************************************************************************************** */
     /* -
      * +------------------------------------------------------------------------
      * * @ 读取备份文件
      * +------------------------------------------------------------------------
      * * @ $fileName 文件名
      * +------------------------------------------------------------------------
      */
     private function getFile($fileName) {
         $this->content = '';
         $fileName = $this->trimPath($this->config['path'] . $this->dir_sep . $fileName);
         if (is_file($fileName)) {
             $ext = strrchr($fileName, '.');
             if ($ext == '.sql') {
                 $this->content = file_get_contents($fileName);
             } elseif ($ext == '.gz') {
                 $this->content = implode('', gzfile($fileName));
             } else {
                 $this->error('无法识别的文件格式!');
             }
         } else {
             $this->error('文件不存在!');
         }
     }


/*     private function setFile() {
         $recognize = '';
         $recognize = $this->dbName;
         $fileName = $this->trimPath($this->config['path'] . $this->dir_sep . $recognize . '_' . date('YmdHis') . '_' . mt_rand(100000000, 999999999) . '.sql');
         $path = $this->setPath($fileName);
         if ($path !== true) {
             $this->error("无法创建备份目录目录 '$path'");
         }

         if (!file_put_contents($fileName, $this->content, LOCK_EX)) {
             $this->error('写入文件失败,请检查磁盘空间或者权限!');
         }

     }*/


     private function setFile() {
         $recognize = '';
         $recognize = $this->dbName;
         $fileName = $this->trimPath($this->config['path'] . $this->dir_sep . $recognize . '_' . date('YmdHis') . '.sql');
         $path = $this->setPath($fileName);
         if ($path !== true) {
             //$this->error("无法创建备份目录目录 '$path'");
             return false;
         }

         if (!file_put_contents($fileName, $this->content, LOCK_EX)) {
             return false;
         }

         return true;
     }


     private function trimPath($path) {
         return str_replace(array('/', '\\', '//', '\\\\'), $this->dir_sep, $path);
     }

     private function setPath($fileName) {
         $dirs = explode($this->dir_sep, dirname($fileName));
         $tmp = '';
         foreach ($dirs as $dir) {
             $tmp .= $dir . $this->dir_sep;
             if (!file_exists($tmp) && !@mkdir($tmp, 0777))
                 return $tmp;
         }
         return true;
     }


     private function backquote($str) {
         return "`{$str}`";
     }

     /* -
      * +------------------------------------------------------------------------
      * * @ 把传过来的数据 按指定长度分割成数组
      * +------------------------------------------------------------------------
      * * @ $array 要分割的数据
      * * @ $byte  要分割的长度
      * +------------------------------------------------------------------------
      * * @ 把数组按指定长度分割,并返回分割后的数组
      * +------------------------------------------------------------------------
      */
     private function chunkArrayByByte($array, $byte = 5120) {
         $i = 0;
         $sum = 0;
         $return = array();
         foreach ($array as $v) {
             $sum += strlen($v);
             if ($sum < $byte) {
                 $return[$i][] = $v;
             } elseif ($sum == $byte) {
                 $return[++$i][] = $v;
                 $sum = 0;
             } else {
                 $return[++$i][] = $v;
                 $i++;
                 $sum = 0;
             }
         }
         return $return;
     }
     /* -
      * +------------------------------------------------------------------------
      * * @ 备份数据 { 备份每张表、视图及数据 }
      * +------------------------------------------------------------------------
      * * @ $tables 需要备份的表数组
      * +------------------------------------------------------------------------
      */
     public function backup($tables) {
         if (empty($tables)) return false;
         $this->content = '/* This file is created by JL_MWCS ' . date('Y-m-d H:i:s') . ' */';
         foreach ($tables as $i => $table) {
             $table = $this->backquote($table);
             $tableRs = $this->model->query("SHOW CREATE TABLE {$table}");       //获取当前表的创建语句
             if (!empty($tableRs[0]["Create View"])) {
                 $this->content .= "\r\n /* 创建视图结构 {$table}  */";
                 $this->content .= "\r\n DROP VIEW IF EXISTS {$table};/* JL_MWCS Separation */ " . $tableRs[0]["Create View"] . ";/* JL_MWCS Separation */";
             }
             if (!empty($tableRs[0]["Create Table"])) {
                 $this->content .= "\r\n /* 创建表结构 {$table}  */";
                 $this->content .= "\r\n DROP TABLE IF EXISTS {$table};/* JL_MWCS Separation */ " . $tableRs[0]["Create Table"] . ";/* JL_MWCS Separation */";
                 $tableDateRow = $this->model->query("SELECT * FROM {$table}");
                 $valuesArr = array();
                 $values = '';
                 if (false != $tableDateRow) {
                     foreach ($tableDateRow as &$y) {
                         foreach ($y as &$v) {
                             if ($v=='')          //纠正empty 为0的时候  返回tree
                             $v = 'null';          //为空设为null
                             else
                                 $v = "'" . mysql_escape_string($v) . "'"; //非空 加转意符
                         }
                         $valuesArr[] = '(' . implode(',', $y) . ')';
                     }
                 }
                 $temp = $this->chunkArrayByByte($valuesArr);
                 if (is_array($temp)) {
                     foreach ($temp as $v) {
                         $values = implode(',', $v) . ';/* JL_MWCS Separation */';
                         if ($values != ';/* JL_MWCS Separation */') {
                             $this->content .= "\r\n /* 插入数据 {$table} */";
                             $this->content .= "\r\n INSERT INTO {$table} VALUES {$values}";
                         }
                     }
                 }
             }
         }

         if (!empty($this->content)) {
             return $this->setFile();
         }else{
             return false;
         }
     }
     /* -
      * +------------------------------------------------------------------------
      * * @ 还原数据
      * +------------------------------------------------------------------------
      * * @ $fileName 文件名
      * +------------------------------------------------------------------------
      */
     private function recover_file($fileName) {
         $this->getFile($fileName);
         if (!empty($this->content)) {
             $content = explode(';/* JL_MWCS Separation */', $this->content);
             foreach ($content as $i => $sql) {
                 $sql = trim($sql);
                 if (!empty($sql)) {
                     $mes = $this->model->execute($sql);
                     if (false === $mes) {  //如果 null 写入失败，换成 ''
                         $table_change = array('null' => '\'\'');
                         $sql = strtr($sql, $table_change);
                         $mes = $this->model->execute($sql);
                     }
                     if (false === $mes) {       //如果遇到错误、记录错误
                        //记录错误
                     }
                 }
             }
             return true;
         } else {
             return false;
         }
     }
 }