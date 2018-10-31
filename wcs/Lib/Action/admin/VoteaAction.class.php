<?php
	
	/*
	 * 友情链接管理
	 */
	
	class VoteaAction extends ExtAction{

        protected $_config = array(
            'cid'=>         16,
            'class'=>       __CLASS__,
            'map'=>         array(),
            'title'=>       "投票选项",
            'pagesize'=>    '20',
            'sortable'=>    true,
            'hidden'=>      array(),
            'sortField'=>   "order",
            'filterable'=>  false,
            'filterField'=>"fid",
            'filterHtml'=>  "",
            'addable'=>     true,
            'deleteable'=>  true,
            'search'=>      array(
                'like'=>    array('title'=>'选项名','id'=>'选项ID'),
                'orderby'=> array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','order_desc'=>'排序字段 降序','order_asc'=>'排序字段 升序')
            ),
            'handleWidth'=> '150px',
        );


        public static function  getToolbar($param){
            $fid = $param['fid'];
            $m = M('Voteq');
            $arr = $m->where("id=".$fid)->find();
            if(empty($arr)){
                return "";
            }else{
                $url = '__GROUP__/voteq/show?fid='.$arr['fid'];
                return '<input type="submit" class="button" onclick="location.href=\''.$url.'\'"  value="返回问题列表" style=float:left;"" />';
            }
        }


        public static function  getHandle($arr){
             return $arr['fid'].'序号题项 ->';
        }



        public function show(){
            $fid = $this->_param('fid');
            $this->assign("fid",$fid);
            if(!empty($fid)) $this->config['map']['fid'] = array("eq",$fid);
            $this->config['hidden'] = array("fid"=>$fid);
            parent::show();
        }



        //如果你不想重写父类的add方法，可以只是通过该方法加入一些额外额form表单内容
        protected function addMoreForm(&$form){
            $fid = $this->_param("fid");
            $this->config['returnUrl']['add'] = "show?fid=$fid";
            $form->add('hidden','fid',$fid);
        }




	}






?>