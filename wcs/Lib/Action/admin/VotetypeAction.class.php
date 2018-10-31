<?php
	
	/*
	 * 友情链接管理
	 */
	
	class VotetypeAction extends ExtAction{

/*        protected $config = array(
            'title'=>"投票主题",
            'order_field'=>"",    //用于排序的字段
            'addable'=>true,
            'deleteable'=>true,
            'categoryable'=>false,
            'toolbar'=>array('width'=>140,'class'=>__class__)
        );*/

        protected $_config = array(
            'cid'=>         14,
            'class'=>       __CLASS__,
            'map'=>         array(),
            'title'=>       "投票主题",
            'pagesize'=>    '20',
            'sortable'=>    false,
            'hidden'=>      array(),
            'sortField'=>   "",
            'filterable'=>  false,
            'filterField'=>"",
            'filterHtml'=>  "",
            'addable'=>     true,
            'deleteable'=>  true,
            'search'=>      array(
                'like'=>    array('title'=>'主题名','id'=>'主题ID'),
                'orderby'=> array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','order_desc'=>'排序字段 降序','order_asc'=>'排序字段 升序')
            ),
            'handleWidth'=> '130px',

        );



        public static function  getHandle($arr){
            return '<a href="'.'__GROUP__/votetype/exttj?id='.$arr['id'].'" >留言</a> | <a href="'.'__GROUP__/votetype/tj?id='.$arr['id'].'" >统计</a> | ';
        }

        public static function  getToolbar($param){
            return "";
        }


        public function show(){
            parent::show();
        }


        public function add(){
            $this->config['returnUrl']['add'] = "__GROUP__/votetype/show";
            parent::add();
        }

        protected function addMoreForm($form){
            $form->add('hidden','time',time());
            return;
        }

        public function edit(){
            $this->config['returnUrl']['jump'] = "__GROUP__/votetype/show";
            $this->config['returnUrl']['edit'] = "__GROUP__/votetype/show";
            parent::edit();
        }

        protected function editMoreForm($form){
            $form->add('hidden','edittime',time());
            return;
        }








        //特有方法
        public function tj(){

            $mvotetype = M("Votetype");
            $id = $this->_param('id');
            $sdate = $this->_param('sdate');
            $edate = $this->_param('edate');

            if(empty($sdate)) $sdate = date('Y-m-d',time()-3600*24*30);
            if(empty($edate)) $edate = date('Y-m-d',time());


            $id = !empty($id)?$id:1;
            $votetypeArr =  $mvotetype->where("id=".$id)->find();
            if(empty($votetypeArr )){
                $this->error("该投票主题没有任何问题！");
                return;
            }

            /*position指定以及一些问候信息*/
            $current = $this->config['title']."统计";
            $position = getPosition($this->config['title']."统计");
            $this->assign('current',$current);
            $this->assign('position',$position);
            $this->assign('welcome',getWelcome());
            $this->assign('id',$id);
            $this->assign('sdate',$sdate);
            $this->assign('edate',$edate);
            $this->assign('votetypeArr',$votetypeArr);
           $this->display();
        }


        public function exttj(){
            import('ORG.Util.Page');
            $mvotetype = M("Votetype");
            $id = $this->_param('id');
            $sdate = $this->_param('sdate');
            $edate = $this->_param('edate');

            if(empty($sdate)) $sdate = date('Y-m-d',time()-3600*24*30);
            if(empty($edate)) $edate = date('Y-m-d',time());


            $id = !empty($id)?$id:1;
            $votetypeArr =  $mvotetype->where("id=".$id)->find();
            if(empty($votetypeArr )){
                $this->error("该投票主题没有任何问题！");
                return;
            }

            $map = array();
            $map['fid'] = array("eq",$id);
            $map['date'] = array("BETWEEN","$sdate,$edate");
            $m = M('Voteext');
            $count = $m->where($map)->count();
            $Page  = new Page($count,C('SYS_PAGE_SIZE'));
            $show  = $Page->show();
            $list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('list',$list);
            $this->assign('page',$show);


            /*position指定以及一些问候信息*/
            $current = $this->config['title']."意见或建议";
            $position = getPosition($this->config['title']."意见或建议");
            $this->assign('current',$current);
            $this->assign('position',$position);
            $this->assign('welcome',getWelcome());
            $this->assign('id',$id);
            $this->assign('sdate',$sdate);
            $this->assign('edate',$edate);
            $this->assign('votetypeArr',$votetypeArr);
            $this->display();
        }








}






?>