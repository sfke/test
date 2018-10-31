<?php
	
	/*
	 * 友情链接管理
	 */
	
	class VoteqAction extends ExtAction{

        protected $_config = array(
            'cid'=>         15,
            'class'=>       __CLASS__,
            'map'=>         array(),
            'title'=>       "投票问题",
            'pagesize'=>    '5',
            'sortable'=>    true,
            'hidden'=>      array(),
            'sortField'=>   "order",
            'filterable'=>  true,
            'filterField'=>"fid",
            'filterHtml'=>  "",
            'addable'=>     true,
            'deleteable'=>  true,
            'search'=>      array(
                'like'=>    array('title'=>'问题','id'=>'问题ID'),
                'orderby'=> array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','order_asc'=>'排序字段 降序','order_desc'=>'排序字段 升序')
            ),
            'handleWidth'=> '100px',
        );


        public static function  getToolbar($param){
            return "";
        }

        public static function  getHandle($arr){
            return '<a href="'.'__GROUP__/votea/show?fid='.$arr['id'].'" >选项</a> | ';
        }

        public function show(){

            $fid = $this->_param("fid");

            $m = M('Votetype');
            $allArr = $m->select();
            if(empty($allArr)) $this->error('请先添加投票主题！');

            if(empty($fid)) $fid = $allArr[0]['id'];

            $this->config['map']['fid'] = array("eq",$fid);

            $options = '';
            foreach($allArr as $v){
                if($v['id'] == $fid) $selected = 'selected="selected"';
                else $selected = '';
                $options .= '<option value="'.$v['id'].'" '.$selected.' >'.$v['title'].'</option>';
            }
            $this->config['filterHtml'] = $options;
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