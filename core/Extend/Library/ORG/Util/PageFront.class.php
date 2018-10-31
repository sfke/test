<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Page.class.php 2712 2012-02-06 10:12:49Z liu21st $

class Page {
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow	;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    protected $config  =	array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'首页','last'=>'尾页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页  %first%  %upPage%  %linkPage%  %downPage%  %end%');
    // 默认分页变量名
    protected $varPage;

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows='',$parameter='') {
		//当站点ID=2为英文站可开启
		if(getSiteId()==2){
		 $this->config  =	array('header'=>'Records','prev'=>'Prev','next'=>'Next','first'=>'First','last'=>'Last','theme'=>' %totalRow% %header% %nowPage%/%totalPage% Page  %first%  %upPage%  %linkPage%  %downPage%  %end%');
		}
		
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->varPage = C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        if(!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
		if (C('JL_HTML_CACHE')) {
		 $_SESSION['sctotalPages']=$this->totalPages;
        }
		
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
		
        $this->nowPage  = !empty($_GET[$this->varPage])?intval($_GET[$this->varPage]):1;
		
		if (C('JL_HTML_CACHE')) {
		 $this->nowPage=!empty($_SESSION['scnowPage'])?$_SESSION['scnowPage']:1;
		 if($_SESSION['scnowPage']==$_SESSION['sctotalPages']){
			$_SESSION['sctotalPages']=""; 
		 }
        }
        
        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p = $this->varPage;
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            $upPage="&nbsp;<a href='".pageUrl('?'.$this->parameter.'&'.$p.'='.$upRow)."' class='prev'>".$this->config['prev']."</a>";
        }else{
            $upPage="&nbsp;<a href='#' class='prev'>".$this->config['prev']."</a>";
        }

        if ($downRow <= $this->totalPages){
            $downPage="&nbsp;<a href='".pageUrl('?'.$this->parameter.'&'.$p.'='.$downRow)."' class='next'>".$this->config['next']."</a>";
        }else{
            $downPage="&nbsp;<a href='#' class='next'>".$this->config['next']."</a>";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "<a href='".$url."&".$p."=$preRow' >上".$this->rollPage."页</a>";
            $theFirst = "<a href='".pageUrl('?'.$this->parameter.'&'.$p.'=1')."' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<a href='".$url."&".$p."=$nextRow' >下".$this->rollPage."页</a>";
            $theEnd = "&nbsp;<a href='".pageUrl('?'.$this->parameter.'&'.$p.'='.$theEndRow)."' >".$this->config['last']."</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){                     
                    $linkPage .= "&nbsp;<a href='".pageUrl('?'.$this->parameter.'&'.$p.'='.$page)."'>".$page."</a>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){                    
                    $linkPage .= "&nbsp;<a href='#' class='active'>".$page."</a>";
                }
            }
        }
		//默认样式
        /*$pageStr	 =	 str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
		*/
			
		//第2种样式
		 
		/*$pageStr = '<span>共 ' . $this->totalPages . ' 页&nbsp;&nbsp;&nbsp;&nbsp;页次&nbsp;&nbsp;<i>' . $this->nowPage . '</i>/' . $this->totalPages . ' 页</span>';
        $pageStr.=$theFirst . $upPage . $linkPage . $downPage . $theEnd;
        $pageStr.='<a href="#" class="turn">转到</a><select onchange="window.location.href=this.value">';
        for ($j = 1; $j <= $this->totalPages; $j++) {
                $pageStr.='<option value="' . pageUrl('?' . $this->parameter . '&' . $p . '=' . $j) . '">' . $j . '</option>';
        }
        $pageStr.='</select>';	
		*/
		
		//第3种样式
		$pageStr =$upPage . $linkPage . $downPage;
        return $pageStr;
    }

     public function searchshow() {
        if(0 == $this->totalRows) return '';
        $p = $this->varPage;
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
           $upPage="&nbsp;<a href='".$url."&".$p."=$upRow' class='prev'>".$this->config['prev']."</a>";
        }else{
            $upPage="&nbsp;<a href='#' class='prev'>".$this->config['prev']."</a>";
        }

        if ($downRow <= $this->totalPages){
        	$downPage="&nbsp;<a href='".$url."&".$p."=$downRow' class='next'>".$this->config['next']."</a>";
        }else{
            $downPage="&nbsp;<a href='#' class='next'>".$this->config['next']."</a>";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "<a href='".$url."&".$p."=$preRow' >上".$this->rollPage."页</a>";
            $theFirst = "<a href='".$url."&".$p."=1' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<a href='".$url."&".$p."=$nextRow' >下".$this->rollPage."页</a>";
            $theEnd = "&nbsp;<a href='".$url."&".$p."=$theEndRow' >".$this->config['last']."</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    $linkPage .= "&nbsp;<a href='".$url."&".$p."=$page'>".$page."</a>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= "&nbsp;<a href='#' class='active'>".$page."</a>";
                }
            }
        }
		//默认样式
        /*$pageStr	 =	 str_replace(
            array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
		*/
			
		//第2种样式
		
	   /* $pageStr = '<span>共 ' . $this->totalPages . ' 页&nbsp;&nbsp;&nbsp;&nbsp;页次&nbsp;&nbsp;<i>' . $this->nowPage . '</i>/' . $this->totalPages . ' 页</span>';
        $pageStr.=$theFirst . $upPage . $linkPage . $downPage . $theEnd;
        $pageStr.='<a href="#" class="turn">转到</a><select onchange="window.location.href=this.value">';
        for ($j = 1; $j <= $this->totalPages; $j++) {
                $pageStr.='<option value="' .$url.'&' . $p . '=' . $j . '">' . $j . '</option>';
        }
        $pageStr.='</select>';	
       */
	   
	   //第3种样式
		$pageStr =$upPage . $linkPage . $downPage;
        return $pageStr;
    }
    /**
    +----------------------------------------------------------
     * 自定义分页显示输出
    +----------------------------------------------------------
     * @access public
    +----------------------------------------------------------
     * @author CL019
     */
    function page_info(){
        if(0==$this->totalRows)  return "";
        $tid=$this->parameter;
        $p=$this->varPage;

        //确定显示在页面的页码，显示的页码数不大于5
        $mid=ceil($this->rollPage/2)-1;
        if($this->totalPages<$this->rollPage){
            $from=1;
            $to=$this->totalPages;
        }else{
            $from=$this->nowPage<=$mid?1:$this->nowPage-$mid+1;
            $to=$from+$this->rollPage-1;
            $to>$this->totalPages && $to=$this->totalPages;
        }

        //生成URL
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$tid;

        $parse = parse_url($url); //dump($parse);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }

        $page['page_links'] = array();
        $page['first_link'] = ''; // 首页链接
        $page['first_suspen'] = ''; // 首页省略号
        $page['last_link'] = ''; // 尾页链接
        $page['last_suspen'] = ''; // 尾页省略号

        for($i=$from;$i<=$to;$i++){
            $page['page_links'][$i]=pageUrl("?".$tid."&".$p."=".$i);
        }

        if (($this->nowPage - $from) < ($this->nowPage -1) && $this->totalPages > $this->rollPage)
        {
            $page['first_link'] = pageUrl("?".$tid."&".$p."=1");
            if (($this->nowPage -1) - ($this->nowPage - $from) != 1)
            {
                $page['first_suspen'] = '..';
            }
        }

        if (($to - $this->nowPage) < ($this->totalPages - $this->nowPage) && $this->totalPages > $this->rollPage)
        {
            $page['last_link'] = pageUrl("?".$tid."&".$p."=".$this->totalPages);
            if (($this->totalPages - $this->nowPage) - ($to - $this->nowPage) != 1)
            {
                $page['last_suspen'] = '..';
            }
        }

        $page['prev_link'] = $this->nowPage > $from ?  pageUrl("?".$tid."&".$p."=".($this->nowPage-1)) : "";
        $page['next_link'] = $this->nowPage< $to ?  pageUrl("?".$tid."&".$p."=".($this->nowPage+1)) : "";

        $page['curr_page']=$this->nowPage;
        $page['total_page']=$this->totalPages;
        $page['per_page']=$this->listRows;

        return $page;
    }

}