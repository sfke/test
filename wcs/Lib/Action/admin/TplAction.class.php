<?php

/**
 * 模板管理
 * @author Administrator
 *
 */
class TplAction extends BaseAction
{


    protected function tplExtendChange()
    {


        $theme = C('SYS_DEFAULT_THEME');
        $path = TMPL_PATH . 'home/' . $theme . '/';
        $dir = opendir($path);
        $file = null;
        while (($file = readdir($dir)) != false) {

            $exted_str = extend($path . $file);

            if (!is_dir($file) && is_file($path . $file) && in_array($exted_str, C('SYS_TPL_EXTENDS')) && $exted_str != C('SYS_TPL_EXTEND')) {
                $newfilename = str_replace($exted_str, "", $file) . C('SYS_TPL_EXTEND');
                rename($path . $file, $path . $newfilename);
            }
        }
    }


    /**
     * 模板批量替换检查
     */
    public function tplReplaceCheck()
    {

        $this->tplExtendChange();

        $theme = C('SYS_DEFAULT_THEME');
        $path = TMPL_PATH . 'home/' . $theme . '/';
        $dir = opendir($path);
        //用于存放根目录的所有文件夹名
        $folderArr = array();
        while (($file = readdir($dir)) != false) {
            if (!is_dir($file) && !is_file($path . $file)) {
                $folderArr[] = $file;
            }
        }
        closedir($dir);
        if (empty($folderArr)) {
            $str = "&nbsp;&nbsp;&nbsp;模板文件不存在！请检查！";
        } else {
            $file = null;
            $dir = opendir($path);
            $findArr = array();
            while (($file = readdir($dir)) != false) {
                if (!is_dir($file) && is_file($path . $file) && in_array(extend($path . $file), C('SYS_TPL_EXTENDS'))) {
                    $findArr[$file] = array();
                    $tempHtml = file_get_contents($path . $file);
                    if (empty($tempHtml)) {
                        continue;
                    } else {
                        foreach ($folderArr as $v) {
                            //'#([^/]'.$v.'/.*)#i'
                            //"#(?<=[^/])($v/.*)#i"
                            preg_match_all('#([^/]' . $v . '/.*)#i', $tempHtml, $matches);
                            if (empty($matches)) {
                                continue;
                            } else {
                                $findArr[$file] = array_merge($findArr[$file], $matches[0]);
                            }
                        }
                    }


                }
            }
            closedir($dir);
            $rsArr = array();
            foreach ($findArr as $k => $v) {
                $rsArr[$k] = array_count_values($v);

            }
            if (empty($rsArr)) {

                $str = "&nbsp;&nbsp;&nbsp;模板文件不存在！请检查！";

            } else {

                $str = "<dl style='padding:0px;margin:0px;'>";
                foreach ($rsArr as $k => $v) {

                    $str .= "<dt style='font-size:12px; font-weight:bolder; margin-left:20px;' >文件名：$k ：" . "</dt>";
                    if (!empty($v)) {
                        foreach ($v as $k2 => $v2) {
                            $findLine = $k2;
                            $str .= "<dd style='font-size:10px; font-weight:normal; margin-left:40px; color:#FF00AE;' ><xmp>$v2 项： " . msubstr($findLine, 0, 150) . "</xmp></dd>";
                        }
                    } else {
                        $str .= "<dd style='font-size:10px; font-weight:normal; margin-left:40px; color:#FF00AE;' >&nbsp;&nbsp;该文件已没有可替换项</dd>";
                    }
                    $str .= "</dl>";
                }
                $str .= "<div>&nbsp;</div>";
            }

        }

        /*position指定以及一些问候信息*/
        $current = "模板外部资源分析 " . $path;
        $position = getPosition("模板外部资源分析");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->assign('str', $str);
        $this->display();

    }


    public function tplReplace()
    {

        /**
         * 模板批量替换
         */
        $theme = C('SYS_DEFAULT_THEME');
        $path = TMPL_PATH . 'home/' . $theme . '/';
        //die($path);
        $dir = opendir($path);
        //用于存放根目录的所有文件夹名
        $folderArr = array();
        while (($file = readdir($dir)) != false) {
            if (!is_dir($file) && !is_file($path . $file)) {
                $folderArr[] = $file;
            }
        }
        closedir($dir);
        if (empty($folderArr)) {
            $this->error("没有找到模板文件！请检查！");
        } else {
            $file = null;
            $dir = opendir($path);
            $findArr = array();
            while (($file = readdir($dir)) != false) {
                if (!is_dir($file) && is_file($path . $file)) {
                    $findArr[$file] = array();
                    $tempHtml = file_get_contents($path . $file);
                    if (empty($tempHtml)) {
                        continue;
                    } else {
                        //todo:正则有点优化
                        foreach ($folderArr as $v) {
                            //preg_match_all('#([^/]'.$v.'/.*)#i', $tempHtml, $matches);
                            $tempHtml = preg_replace("#(?<=[^/])($v/[^\\s]*)#i", "__BASE__/$1", $tempHtml);
                            /*<%@LANGUAGE="JAVASCRIPT" CODEPAGE="65001"%>*/
                            $tempHtml = preg_replace('#<%@\s?LANGUAGE="JAVASCRIPT"\s+CODEPAGE="65001"\s?%>#i', "", $tempHtml);
                            file_put_contents($path . $file, $tempHtml);
                        }

                    }


                }
            }
            closedir($dir);
        }

        $this->success("模板外部资源批量替换成功！", "__GROUP__/tpl/tplReplaceCheck");

    }


    /**
     * 模板包含文件检查
     */
    public function tplIncludeCheck()
    {

        $theme = C('SYS_DEFAULT_THEME');
        $path = TMPL_PATH . 'home/' . $theme . '/';
        //die($path);
        $dir = opendir($path);
        //用于存放根目录的所有文件夹名
        $folderArr = array();
        while (($file = readdir($dir)) != false) {
            if (!is_dir($file) && !is_file($path . $file)) {
                $folderArr[] = $file;
            }
        }
        closedir($dir);
        if (empty($folderArr)) {
            $str = "&nbsp;&nbsp;&nbsp;模板文件不存在！请检查！";
        } else {
            $file = null;
            $dir = opendir($path);
            $findArr = array();
            while (($file = readdir($dir)) != false) {
                if (!is_dir($file) && is_file($path . $file)) {
                    $findArr[$file] = array();
                    $tempHtml = file_get_contents($path . $file);
                    if (empty($tempHtml)) {
                        continue;
                    } else {

                        preg_match_all('/<!--\s*#include\s*file\s*=\s*[\'"](.*).asp[\'"]\s*-->/i', $tempHtml, $matches);
                        if (empty($matches)) {
                            continue;
                        } else {
                            $findArr[$file] = array_merge($findArr[$file], $matches[0]);
                        }

                    }


                }
            }
            closedir($dir);
            $rsArr = array();
            foreach ($findArr as $k => $v) {
                $rsArr[$k] = array_count_values($v);

            }
            if (empty($rsArr)) {

                $str = "&nbsp;&nbsp;&nbsp;模板文件不存在！请检查！";

            } else {

                $str = "<dl style='padding:0px;margin:0px;'>";
                foreach ($rsArr as $k => $v) {

                    $str .= "<dt style='font-size:12px; font-weight:bolder; margin-left:20px;' >文件名：$k ：" . "</dt>";
                    if (!empty($v)) {
                        foreach ($v as $k2 => $v2) {
                            $findLine = $k2;
                            $str .= "<dd style='font-size:10px; font-weight:normal; margin-left:40px; color:#FF00AE;' ><xmp>$v2 项： " . msubstr($findLine, 0, 150) . "</xmp></dd>";
                        }
                    } else {
                        $str .= "<dd style='font-size:10px; font-weight:normal; margin-left:40px; color:#FF00AE;' >&nbsp;&nbsp;该文件已没有可替换项</dd>";
                    }
                    $str .= "</dl>";
                }
                $str .= "<div>&nbsp;</div>";
            }

        }

        /*position指定以及一些问候信息*/
        $current = "模板包含文件分析 " . $path;
        $position = getPosition("模板包含文件替换");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());


        $this->assign('str', $str);
        $this->display();

    }


    public function tplInclude()
    {

        /**
         * 模板批量替换
         */

        $theme = C('SYS_DEFAULT_THEME');
        $path = TMPL_PATH . 'home/' . $theme . '/';
        //die($path);
        $dir = opendir($path);
        //用于存放根目录的所有文件夹名
        $folderArr = array();
        while (($file = readdir($dir)) != false) {
            if (!is_dir($file) && !is_file($path . $file)) {
                $folderArr[] = $file;
            }
        }
        closedir($dir);
        if (empty($folderArr)) {
            $this->error("没有找到模板文件！请检查！");
        } else {
            $file = null;
            $dir = opendir($path);
            $findArr = array();
            while (($file = readdir($dir)) != false) {
                if (!is_dir($file) && is_file($path . $file)) {
                    $findArr[$file] = array();
                    $tempHtml = file_get_contents($path . $file);
                    if (empty($tempHtml)) {
                        continue;
                    } else {
                        //$tempHtml = preg_replace('/<!--\s?#include file="(.*).asp"-->/i',"<include file='wcs/Tpl/home/default/$1.html'/>", $tempHtml);
                        $tempHtml = preg_replace('/<!--\s*#include\s*file\s*=\s*[\'"](.*).asp[\'"]\s*-->/i', "<include file='wcs/Tpl/home/" . $theme . "/$1.html'/>", $tempHtml);
                        file_put_contents($path . $file, $tempHtml);
                    }


                }
            }
            closedir($dir);
        }

        $this->success("模板包含文件批量替换成功！", "__GROUP__/tpl/tplIncludeCheck");

    }


    /*
     * 自定义模板替换
     */
    public function tplPreg()
    {
        $action = !empty($_POST['act']) ? $_POST['act'] : null;
        $preg_str = !empty($_POST['preg']) ? $_POST['preg'] : null;
        $preg_str2 = isset($_POST['preg2']) ? $_POST['preg2'] : null;

        //找到模板文件
        $theme = C('SYS_DEFAULT_THEME');
        $path = TMPL_PATH . 'home/' . $theme . '/';

        if ($action != null || $preg_str != null) {

            $dir = opendir($path);
            //用于存放根目录的所有文件夹名
            $folderArr = array();
            while (($file = readdir($dir)) != false) {
                if (!is_dir($file) && !is_file($path . $file)) {
                    $folderArr[] = $file;
                }
            }
            closedir($dir);

            if ($action == 'check') {

                if (empty($folderArr)) {
                    $str = "&nbsp;&nbsp;&nbsp;模板文件不存在！请检查！";
                } else {
                    $file = null;
                    $dir = opendir($path);
                    $findArr = array();
                    while (($file = readdir($dir)) != false) {
                        if (!is_dir($file) && is_file($path . $file)) {
                            $findArr[$file] = array();
                            $tempHtml = file_get_contents($path . $file);
                            if (empty($tempHtml)) {
                                continue;
                            } else {
                                preg_match_all($preg_str, $tempHtml, $matches);
                                if (empty($matches)) {
                                    continue;
                                } else {
                                    $findArr[$file] = array_merge($findArr[$file], $matches[0]);
                                }

                            }


                        }
                    }
                    closedir($dir);
                    $rsArr = array();
                    foreach ($findArr as $k => $v) {
                        $rsArr[$k] = array_count_values($v);

                    }
                    if (empty($rsArr)) {

                        $str = "&nbsp;&nbsp;&nbsp;模板文件不存在！请检查！";

                    } else {

                        $str = "<dl style='padding:0px;margin:0px;'>";
                        foreach ($rsArr as $k => $v) {

                            $str .= "<dt style='font-size:12px; font-weight:bolder; margin-left:20px;' >文件名：$k ：" . "</dt>";
                            if (!empty($v)) {
                                foreach ($v as $k2 => $v2) {
                                    $findLine = $k2;
                                    $str .= "<dd style='font-size:10px; font-weight:normal; margin-left:40px; color:#FF00AE;' ><xmp>$v2 项： " . msubstr($findLine, 0, 150) . "</xmp></dd>";
                                }
                            } else {
                                $str .= "<dd style='font-size:10px; font-weight:normal; margin-left:40px; color:#FF00AE;' >&nbsp;&nbsp;该处没有找到匹配项</dd>";
                            }
                            $str .= "</dl>";
                        }
                        $str .= "<div>&nbsp;</div>";
                    }

                }


            } else if ($action == 'replace') {
                if ($preg_str2 === null) {
                    $this->error("执行参数有误！");
                    return;
                } else {

                    if (empty($folderArr)) {
                        $this->error("没有找到模板文件！请检查！");
                    } else {
                        $file = null;
                        $dir = opendir($path);
                        $findArr = array();
                        while (($file = readdir($dir)) != false) {
                            if (!is_dir($file) && is_file($path . $file)) {
                                $findArr[$file] = array();
                                $tempHtml = file_get_contents($path . $file);
                                if (empty($tempHtml)) {
                                    continue;
                                } else {
                                    $tempHtml = preg_replace($preg_str, $preg_str2, $tempHtml);
                                    if ($tempHtml === null) {
                                        $this->error("替换过程中发生错误！");
                                    } else {
                                        file_put_contents($path . $file, $tempHtml);
                                    }
                                }
                            }
                        }
                        closedir($dir);
                    }
                    $str = "&nbsp;&nbsp;&nbsp;自定义替换成功！";
                }
            } else {
                $this->error("替换内容参数有误！");
                return;
            }
        }

        /*position指定以及一些问候信息*/
        $current = "自定义模板替换 " . $path;
        $position = getPosition("自定义模板替换");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());


        if (empty($str)) $str = "&nbsp;&nbsp;&nbsp;没有执行任何替换操作，结果为空！";
        $this->assign('str', $str);
        $preg_str = htmlspecialchars($preg_str);
        $preg_str2 = htmlspecialchars($preg_str2);
        $this->assign('preg_str', $preg_str);
        $this->assign('preg_str2', $preg_str2);
        $this->display();
    }


    /**
     * 文件管理器
     */
    public function fileManage()
    {

        $realpath = $GLOBALS['_SERVER']["DOCUMENT_ROOT"];
        /*position指定以及一些问候信息*/
        $current = "文件管理器";
        $position = getPosition("文件管理器");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
		$this->assign('realpath', str_replace("\\","/",$realpath));
        $this->display();
    }
	
	 /**
     * 模版文件修改保存
     */
	public function file_edit_save(){
		$moban_info = str_replace('< script','<script',$_POST['moban_info']);
		$moban_info = str_replace('></include>','/>',$moban_info);
		$moban_info = str_replace('< style','<style',$moban_info);
		$moban_info = str_replace('<else></else>','<else/>',$moban_info);
		$moban_info = str_replace('></elseif>','/>',$moban_info);
		$moban_info = str_replace('\\','',$moban_info);
		$stream = fopen($_SESSION['fpath'], "w+b");
        $r=fwrite($stream, $moban_info);
		fclose($stream);
		if($r==false){
			 $this->error('模版修改失败！');	
		}else{
			$this->success('模版修改成功！',U('Admin/tpl/fileManage'));	
		}
 
		
	}


}


?>