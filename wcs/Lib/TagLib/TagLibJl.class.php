<?php
import('@.Class.TagLibHelper');

class TagLibJl extends TagLib
{
    // 标签定义
    protected $tags = array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'runphp' => array(),
        'query' => array('attr' => 'sql,field,index'),
        'pagequery' => array('attr' => 'process,field,index'),
        'loop' => array('attr' => 'row,field,table,orderby,orderway,where,limit,index,result'),
        'pageloop' => array('attr' => 'field,table,orderby,orderway,where,pagesize,class,result'),
        'flink' => array('attr' => 'row,field,typeid,orderby,orderway,titlelen,limit,index,result,where'),
        'arclist' => array('attr' => 'pagebreak,addfield,typeid,class,row,field,flag,orderby,orderway,title,channelid,limit,titlelen,desclen,color,index,where,result'),
        'arctype' => array('attr' => 'typeid,class,addfield,row,type,orderby,orderway,limit,titlelen,isparent,field,index,result,where', 'level' => 3),
        'pagelist' => array('attr' => 'addfield,typeid,class,orderby,orderway,titlelen,desclen,pagesize,color,index,flag,result,where'),
        'imglist' => array('attr' => 'aid,typeid,titlelen,row,limit,orderby,orderway,result,where'),
        'qqlist' => array('attr' => 'name,row')
    );

    public function _qqlist($attr,$content) {
        $tag      = $this->parseXmlAttr($attr,'qqlist');

        $count   = count(explode("\r\n", C("{$tag['name']}")));

        $arg['name'] = !empty($tag['name'])?$tag['name']:'';
        $arg['row'] = !empty($tag['row'])?$tag['row']:$count;

        $parseStr  = '<?php ';
        $parseStr .= ' $arr = explode("\r\n", C("'.$arg['name'].'")); ';
        $parseStr .= ' if(is_array($arr)){';
        $parseStr .= ' foreach($arr as $k =>$v){';
        $parseStr .= ' if($k <= '.$arg['row'].'-1){';
        $parseStr .= ' list($field["title"], $field["qq"]) = explode("：", $v);?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } } }?>';

        return $parseStr;
    }

    public function _runphp($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'runphp');
        //print_r($tag);
        $parseStr = '<?php ' . $content . ' ?>';
        return $parseStr;
    }

    public function _query($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'query');
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];

        //标签属性
        $arg['sql'] = !empty($tag['sql']) ? $tag['sql'] : '';

        $argLine = serialize($arg);
        //底层变量名
        $field = !empty($tag['field']) ? $tag['field'] : 'field';
        $index = !empty($tag['index']) ? $tag['index'] : 'index';

        //拼装代码
        $parseStr = '<?php ';
        $parseStr .= ' $m = new CommonModel();  $arr = $m->getQueryData(\'' . $argLine . '\'); ';
        $parseStr .= ' if(is_array($arr)){  ';
        $parseStr .= ' foreach($arr as $' . $index . '=>$' . $field . '){  ?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } }?>';

        return $parseStr;
    }

    public function _arclist($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'arclist');
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'arclist');

        //标签属性        
        $arg['desclen'] = $desclen = !empty($tag['desclen']) ? $tag['desclen'] : '';
        $arg['titlelen'] = $titlelen = !empty($tag['titlelen']) ? $tag['titlelen'] : '';
        $arg['addfield'] = $addfield = !empty($tag['addfield']) ? $tag['addfield'] : 'off';
        $arg['color'] = $color = !empty($tag['color']) ? $tag['color'] : 'off';
        $arg['row'] = $row = !empty($tag['row']) ? $tag['row'] : '';
        $arg['flag'] = $flag = isset($tag['flag']) ? $tag['flag'] : '';
        $arg['typeid'] = $typeid = isset($tag['typeid']) ? $tag['typeid'] : null;
        $arg['class'] = $class = !empty($tag['class']) ? $tag['class'] : '';
        $arg['orderby'] = $orderby = !empty($tag['orderby']) ? $tag['orderby'] : 'id';
        $arg['orderway'] = $orderway = !empty($tag['orderway']) ? $tag['orderway'] : 'desc';
        $arg['title'] = $title = !empty($tag['title']) ? $tag['title'] : '';
        $arg['channelid'] = $channelid = !empty($tag['channelid']) ? $tag['channelid'] : '';
        $arg['limit'] = $limit = !empty($tag['limit']) ? $tag['limit'] : '';
        $arg['pagebreak'] = $pagebreak = !empty($tag['pagebreak']) ? $tag['pagebreak'] : 'off';
        $arg['where'] = $limit = !empty($tag['where']) ? $tag['where'] : '';
        $arg['result'] = $result = !empty($tag['result']) ? $tag['result'] : '';

        $variables = array();
        foreach ($arg as $k => $v) {
            if (substr($v, 0, 1) === '$') {
                unset($arg[$k]);
                $variables[$k] = $v;
            }
        }

        if (!empty($variables)) {
            $temp_num = build_count_rand(1, 6);
            $temp = "\$variable" . $temp_num[0];
            $temp_str = ",serialize(\$variable" . $temp_num[0] . ")";
            $php_str = '';
            foreach ($variables as $k => $v) {
                $php_str .= " " . $temp . "['" . $k . "'] = (" . $v . "); ";
            }
        } else {
            $php_str = '';
            $temp_str = null;
        }

        $argLine = serialize($arg);

        //底层变量名
        $field = !empty($tag['field']) ? $tag['field'] : 'field';
        $index = !empty($tag['index']) ? $tag['index'] : 'index';
        //拼装代码
        $parseStr = '<?php ';
        $parseStr .= $php_str;
        $parseStr .= ' $m = D("Archives");  $arr = $m->getData(\'' . $argLine . '\'' . $temp_str . '); ';
        $parseStr .= ' foreach($arr as $' . $index . '=>$' . $field . '){  ?>';
        $parseStr .= '<?php if($' . $index . ' == 0 ) $isfirst = true;else $isfirst = false; ';
        $parseStr .= ' if($' . $index . ' == count($arr)-1 ) $islast = true;else $islast = false; ?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } ?>';
        return $parseStr;
    }

    public function _pagelist($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'pagelist');
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'pagelist');

        //标签属性
        $arg['addfield'] = $addfield = !empty($tag['addfield']) ? $tag['addfield'] : 'off';
        $arg['desclen'] = $desclen = !empty($tag['desclen']) ? $tag['desclen'] : '';
        $arg['color'] = $color = !empty($tag['color']) ? $tag['color'] : 'off';
        $arg['titlelen'] = $titlelen = !empty($tag['titlelen']) ? $tag['titlelen'] : '';
        $arg['typeid'] = $typeid = !empty($tag['typeid']) ? $tag['typeid'] : null;
        $arg['class'] = $class = !empty($tag['class']) ? $tag['class'] : '';
        $arg['flag'] = $flag = isset($tag['flag']) ? $tag['flag'] : '';
        $arg['orderby'] = $orderby = !empty($tag['orderby']) ? $tag['orderby'] : 'id';
        $arg['orderway'] = $orderway = !empty($tag['orderway']) ? $tag['orderway'] : 'desc';
        $arg['pagesize'] = $pagesize = !empty($tag['pagesize']) ? $tag['pagesize'] : '';
        $arg['where'] = $limit = !empty($tag['where']) ? $tag['where'] : '';
        $arg['result'] = $result = !empty($tag['result']) ? $tag['result'] : '';

        $variables = array();
        foreach ($arg as $k => $v) {
            if (substr($v, 0, 1) === '$') {
                unset($arg[$k]);
                $variables[$k] = $v;
            }
        }

        if (!empty($variables)) {
            $temp_num = build_count_rand(1, 6);
            $temp = "\$variable" . $temp_num[0];
            $temp_str = ",serialize(\$variable" . $temp_num[0] . ")";
            $php_str = '';
            foreach ($variables as $k => $v) {
                $php_str .= " " . $temp . "['" . $k . "'] = (" . $v . "); ";
            }
        } else {
            $php_str = '';
            $temp_str = null;
        }

        $argLine = serialize($arg);
        //底层变量名
        $field = !empty($tag['field']) ? $tag['field'] : 'field';
        $index = !empty($tag['index']) ? $tag['index'] : 'index';
        //拼装代码
        $parseStr = '<?php ';
        $parseStr .= $php_str;
        $parseStr .= ' $m = D("Archives");  $arr = $m->getPageList(\'' . $argLine . '\'' . $temp_str . ');  $pageline = $arr["pageline"]; $pageinfo = $arr["pageinfo"];  unset($arr["pageline"],$arr["pageinfo"]);  ';
        $parseStr .= ' foreach($arr as $' . $index . '=> $' . $field . '){  ?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } ?>';

        return $parseStr;
    }

    public function _arctype($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'arctype');
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'arctype');

        //标签属性
        $arg['addfield'] = $addfield = !empty($tag['addfield']) ? $tag['addfield'] : 'off';
        $arg['isparent'] = $isparent = !empty($tag['isparent']) ? $tag['isparent'] : '';
        $arg['titlelen'] = $titlelen = !empty($tag['titlelen']) ? $tag['titlelen'] : '';
        $arg['row'] = $row = !empty($tag['row']) ? $tag['row'] : '';
        $arg['type'] = $type = !empty($tag['type']) ? $tag['type'] : 'son';
        $arg['typeid'] = $typeid = isset($tag['typeid']) ? $tag['typeid'] : null;
        $arg['orderby'] = $orderby = !empty($tag['orderby']) ? $tag['orderby'] : 'order';
        $arg['orderway'] = $orderway = !empty($tag['orderway']) ? $tag['orderway'] : 'asc';
        $arg['limit'] = $limit = !empty($tag['limit']) ? $tag['limit'] : '';
        $arg['class'] = $class = !empty($tag['class']) ? $tag['class'] : '';
        $arg['result'] = $result = !empty($tag['result']) ? $tag['result'] : '';
        $arg['where'] = $where = !empty($tag['where']) ? $tag['where'] : '';

        $variables = array();
        foreach ($arg as $k => $v) {
            if (substr($v, 0, 1) === '$') {
                unset($arg[$k]);
                $variables[$k] = $v;
            }
        }

        if (!empty($variables)) {
            $temp_num = build_count_rand(1, 6);
            $temp = "\$variable" . $temp_num[0];
            $temp_str = ",serialize(\$variable" . $temp_num[0] . ")";
            $php_str = '';
            foreach ($variables as $k => $v) {
                $php_str .= " " . $temp . "['" . $k . "'] = (" . $v . "); ";
            }
        } else {
            $php_str = '';
            $temp_str = null;
        }

        $argLine = serialize($arg);

        //底层变量名
        $field = !empty($tag['field']) ? $tag['field'] : 'field';
        $index = !empty($tag['index']) ? $tag['index'] : 'index';
        //拼装代码
        $parseStr = '<?php ';
        $parseStr .= $php_str;
        $parseStr .= ' $m = D("Arctype");  $arr = $m->getData(\'' . $argLine . '\'' . $temp_str . '); ';
        $parseStr .= ' foreach($arr as $' . $index . '=>$' . $field . '){  ?>';
        $parseStr .= '<?php if($' . $index . ' == 0 ) $isfirst = true;else $isfirst = false; ';
        $parseStr .= ' if($' . $index . ' == count($arr)-1 ) $islast = true;else $islast = false; ?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php }  ?>';

        return $parseStr;
    }

    public function _flink($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'flink');
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'flink');

        //标签属性
        $arg['row'] = $row = !empty($tag['row']) ? $tag['row'] : '';
        $arg['typeid'] = $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $arg['orderby'] = $orderby = !empty($tag['orderby']) ? $tag['orderby'] : 'id';
        $arg['orderway'] = $orderway = !empty($tag['orderway']) ? $tag['orderway'] : 'desc';
        $arg['limit'] = $limit = !empty($tag['limit']) ? $tag['limit'] : '';
        $arg['titlelen'] = $titlelen = !empty($tag['titlelen']) ? $tag['titlelen'] : '';
        $arg['result'] = $result = !empty($tag['result']) ? $tag['result'] : '';
        $arg['where'] = $where = !empty($tag['where']) ? $tag['where'] : '';

        $argLine = serialize($arg);
        //dump($arg);
        //底层变量名
        $field = !empty($tag['field']) ? $tag['field'] : 'field';
        $index = !empty($tag['index']) ? $tag['index'] : 'index';
        //拼装代码
        $parseStr = '<?php ';
        $parseStr .= ' $m = D("Flink");  $arr = $m->getData(\'' . $argLine . '\'); ';
        $parseStr .= ' foreach($arr as $' . $index . '=>$' . $field . '){  ?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } ?>';

        return $parseStr;
    }

    public function _loop($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'loop');
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'loop');

        //标签属性
        $arg['typeid'] = $typeid = isset($tag['typeid']) ? $tag['typeid'] : null;
        $arg['row'] = $row = !empty($tag['row']) ? $tag['row'] : '';
        $arg['table'] = $table = !empty($tag['table']) ? $tag['table'] : '';
        $arg['orderby'] = $orderby = !empty($tag['orderby']) ? $tag['orderby'] : 'id';
        $arg['orderway'] = $orderway = !empty($tag['orderway']) ? $tag['orderway'] : 'desc';
        $arg['limit'] = $limit = !empty($tag['limit']) ? $tag['limit'] : '';
        $arg['where'] = $where = !empty($tag['where']) ? $tag['where'] : '';
        $arg['result'] = $result = !empty($tag['result']) ? $tag['result'] : '';

        $variables = array();
        foreach ($arg as $k => $v) {
            if (substr($v, 0, 1) === '$') {
                unset($arg[$k]);
                $variables[$k] = $v;
            }
        }

        if (!empty($variables)) {
            $temp_num = build_count_rand(1, 6);
            $temp = "\$variable" . $temp_num[0];
            $temp_str = ",serialize(\$variable" . $temp_num[0] . ")";
            $php_str = '';
            foreach ($variables as $k => $v) {
                $php_str .= " " . $temp . "['" . $k . "'] = (" . $v . "); ";
            }
        } else {
            $php_str = '';
            $temp_str = null;
        }
        
        $argLine = serialize($arg);

        //底层变量名
        $field = !empty($tag['field']) ? $tag['field'] : 'field';
        $index = !empty($tag['index']) ? $tag['index'] : 'index';
        //拼装代码
        $parseStr = '<?php ';
        $parseStr .= $php_str;
        $parseStr .= ' $m = new CommonModel(\'' . $table . '\');  $arr = $m->getData(\'' . $argLine . '\'' . $temp_str . '); ';
        $parseStr .= ' foreach($arr as $' . $index . '=>$' . $field . '){  ?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } ?>';

        return $parseStr;
    }

    public function _pageloop($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'pageloop');
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'pageloop');

        //标签属性
        $arg['table'] = $table = !empty($tag['table']) ? $tag['table'] : '';
        $arg['orderby'] = !empty($tag['orderby']) ? $tag['orderby'] : 'id';
        $arg['orderway'] = !empty($tag['orderway']) ? $tag['orderway'] : 'desc';
        $arg['where'] = !empty($tag['where']) ? $tag['where'] : '';
        $arg['pagesize'] = !empty($tag['pagesize']) ? $tag['pagesize'] : '';
        $arg['typeid'] = $typeid = isset($tag['typeid']) ? $tag['typeid'] : null;
        $arg['class'] = $class = !empty($tag['class']) ? $tag['class'] : '';
        $arg['result'] = $result = !empty($tag['result']) ? $tag['result'] : '';

        $variables = array();
        foreach ($arg as $k => $v) {
            if (substr($v, 0, 1) === '$') {
                unset($arg[$k]);
                $variables[$k] = $v;
            }
        }

        if (!empty($variables)) {
            $temp_num = build_count_rand(1, 6);
            $temp = "\$variable" . $temp_num[0];
            $temp_str = ",serialize(\$variable" . $temp_num[0] . ")";
            $php_str = '';
            foreach ($variables as $k => $v) {
                $php_str .= " " . $temp . "['" . $k . "'] = (" . $v . "); ";
            }
        } else {
            $php_str = '';
            $temp_str = null;
        }

        $argLine = serialize($arg);

        //底层变量名
        $field = !empty($tag['field']) ? $tag['field'] : 'field';
        $index = !empty($tag['index']) ? $tag['index'] : 'index';
        //拼装代码
        $parseStr = '<?php ';
        $parseStr .= $php_str;
        $parseStr .= ' $m = new CommonModel(\'' . $table . '\');  $arr = $m->getPageList(\'' . $argLine . '\'' . $temp_str . '); $pageline = $arr["pageline"]; $pageinfo = $arr["pageinfo"];  unset($arr["pageline"],$arr["pageinfo"]); ';
        $parseStr .= ' foreach($arr as $' . $index . '=>$' . $field . '){  ?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } ?>';

        return $parseStr;
    }

    public function _pagequery($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'pagequery');
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'pagequery');

        //标签属性
        $arg['process'] = $process = !empty($tag['process']) ? $tag['process'] : '';
        $arg['pagesize'] = !empty($tag['pagesize']) ? $tag['pagesize'] : '';
		
        $_SESSION['tid']= $tag['typeid']; 
		
        $argLine = serialize($arg);
        //底层变量名
        $field = !empty($tag['field']) ? $tag['field'] : 'field';
        $index = !empty($tag['index']) ? $tag['index'] : 'index';
        //拼装代码
        $parseStr = '<?php ';
        $parseStr .= ' $m = new CommonModel();  $arr = $m->getPageQueryData(\'' . $argLine . '\'); $pageline = $arr["pageline"]; $pageinfo = $arr["pageinfo"];  unset($arr["pageline"],$arr["pageinfo"]); ';
        $parseStr .= ' foreach($arr as $' . $index . '=>$' . $field . '){  ?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } ?>';

        return $parseStr;
    }

    public function _imglist($attr, $content)
    {
        $tag = $this->parseXmlAttr($attr, 'imglist');
        $cacheIterateId = md5($attr . $content);
        if (isset($_iterateParseCache[$cacheIterateId]))
            return $_iterateParseCache[$cacheIterateId];
        $tag = $this->parseXmlAttr($attr, 'imglist');

        //标签属性
        $arg['aid'] = $aid = !empty($tag['aid']) ? $tag['aid'] : '';
        $arg['typeid'] = $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $arg['row'] = $row = !empty($tag['row']) ? $tag['row'] : '';
        $arg['limit'] = $limit = !empty($tag['limit']) ? $tag['limit'] : '';
        $arg['orderby'] = $orderby = !empty($tag['orderby']) ? $tag['orderby'] : 'sort';
        $arg['orderway'] = $orderway = !empty($tag['orderway']) ? $tag['orderway'] : 'desc';
        $arg['titlelen'] = $titlelen = !empty($tag['titlelen']) ? $tag['titlelen'] : '';
        $arg['result'] = $result = !empty($tag['result']) ? $tag['result'] : '';
        $arg['where'] = $where = !empty($tag['where']) ? $tag['where'] : '';

        $variables = array();
        foreach ($arg as $k => $v) {
            if (substr($v, 0, 1) === '$') {
                unset($arg[$k]);
                $variables[$k] = $v;
            }
        }

        if (!empty($variables)) {
            $temp_num = build_count_rand(1, 6);
            $temp = "\$variable" . $temp_num[0];
            $temp_str = ",serialize(\$variable" . $temp_num[0] . ")";
            $php_str = '';
            foreach ($variables as $k => $v) {
                $php_str .= " " . $temp . "['" . $k . "'] = (" . $v . "); ";
            }
        } else {
            $php_str = '';
            $temp_str = null;
        }

        $argLine = serialize($arg);

        //底层变量名
        $field = !empty($tag['field']) ? $tag['field'] : 'field';
        $index = !empty($tag['index']) ? $tag['index'] : 'index';
        //拼装代码
        $parseStr = '<?php ';
        $parseStr .= $php_str;
        $parseStr .= ' $m = D("Images");  $arr = $m->getData(\'' . $argLine . '\'' . $temp_str . '); ';
        $parseStr .= ' foreach($arr as $' . $index . '=>$' . $field . '){  ?>';
        $parseStr .= $this->tpl->parse($content);
        $parseStr .= '<?php } ?>';

        return $parseStr;
    }
}
?>