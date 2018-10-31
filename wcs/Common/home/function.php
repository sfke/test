<?php
//判断是否为内网
function isIntranet()
{
    $ip = get_client_ip();
    if (preg_match("#^((192\.168|172\.([1][6-9]|[2]\d|3[01]))(\.([2][0-4]\d|[2][5][0-5]|[01]?\d?\d)){2}|10(\.([2][0-4]\d|[2][5][0-5]|[01]?\d?\d)){3})$#", $ip)) {
        return true;
    } else {
        return false;
    }
}

function jldate($time, $format = 'Y-m-d')
{
    if (!empty($time)) {
        return date($format, $time);
    } else {
        return "";
    }
}

/*
 * 检查参数一是不是参数二的祖先
 */
function isForefather($fid, $tid)
{
    $m = new ArctypeModel();
    $fathers = $m->getAllParent($tid);
    if (!empty($fathers)) {
        if (in_array($fid, $fathers)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getSummary($txt, $length = "100")
{
    $txt = strip_tags($txt);
    $txt = msubstr($txt, 0, $length);
    return $txt;
}

function pageBreak($content)
{
    $pattern = '#' . C('SYS_PAGE_BREAK') . '#';
    $strSplit = preg_split($pattern, $content, -1, PREG_SPLIT_NO_EMPTY);
    $count = count($strSplit);
    $outStr = "";
    $i = 1;

    if ($count > 1) {
        $outStr = "<div class='page_break'>";
        foreach ($strSplit as $value) {
            if ($i <= 1) {
                $outStr .= "<div class='page_$i'>$value</div>";
            } else {
                $outStr .= "<div class='page_$i collapse'>$value</div>";
            }
            $i++;
        }

        $outStr .= "<div class='pagesize'>";
        for ($i = 1; $i <= $count; $i++) {
            $outStr .= "<a href='javascript:void(0);'>$i</a>";
        }
        $outStr .= "</div></div>";
        return '<script type="text/javascript" language="javascript" src="__JS__/jquery-1.7.2.min.js"></script><script type="text/javascript" language="javascript" src="__JS__/jspage.js"></script><link href="__CSS__/jspage.css" rel="stylesheet" />' . $outStr;
    } else {
        return $content;
    }
}

function counter(){
    $m = M('sysconfig');
    $hits = $m->where('id=44')->getField('value');

    if (!session('?page_visited_already'))
    {
        $hits++;
        $data['value'] = $hits;
        $m->where('id=44')->save($data);
        session('page_visited_already', 1);
    }
    return $hits;
}

//递归删除文件夹
function mydel($jia, $self = false)
{
    $jia = rtrim($jia, "/");
    $dir = opendir($jia);
    while ($f = readdir($dir)) {
        if ($f != '.' && $f != '..') {

            if (is_file($jia . DIRECTORY_SEPARATOR . $f)) { //是文件
                //echo '文件 : '.$jia.DIRECTORY_SEPARATOR.$f.'<BR/>';
                @unlink($jia . DIRECTORY_SEPARATOR . $f);
            }

            if (is_dir($jia . DIRECTORY_SEPARATOR . $f)) { //是文件夹
                //echo '文件夹 : '.$jia.DIRECTORY_SEPARATOR.$f.'<BR/>';
                mydel($jia . DIRECTORY_SEPARATOR . $f);
                @rmdir($jia . DIRECTORY_SEPARATOR . $f);
            }
        }
    }
    closedir($dir);
    if ($self) {
        @rmdir($jia);
    }
}
?>