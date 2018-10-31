<?php
	
	/*
	 * 留言管理
	 */
	
		class MsgAction extends ExtAction{

            protected $_config = array(
                'cid'=>         5,
                'class'=>       __CLASS__,
                'map'=>         array(),
                'title'=>       "留言",
                'pagesize'=>    '20',
                'sortable'=>    false,
                'hidden'=>      array(),
                'sortField'=>   "",
                'filterable'=>  false,
                'filterField'=>"",
                'filterHtml'=>  "",
                'addable'=>     true,
                'deleteable'=>  true,
				'exportexcel'=>  false,   //是否导出成Excel
                'search'=>      array(
                    'like'=>    array('msg_title'=>'主题','msg_name'=>'姓名'),
                    'orderby'=> array('id_desc'=>'ID 降序','id_asc'=>'ID 升序')
                )
            );



            public static function  getToolbar($param){
                return "";
            }


            public static function  getHandle($arr){
                return '';
				//return "<a href='__ROOT__/index.php/index-printtable-id-".$arr['id'].".html' target='_blank'>预览打印</a> | <br/>";
            }


			public function show(){
				parent::show();
			}


            public function add(){
                $this->config['returnUrl']['add'] = "__GROUP__/msg/show";
                parent::add();
            }
            
			public function export(){
                $this->config['returnUrl']['add'] = "__GROUP__/msg/show";
                parent::export();
            }

            protected function addMoreForm($form){

                return;
            }


            public function edit(){
                $this->config['returnUrl']['jump'] = "__GROUP__/msg/show";
                $this->config['returnUrl']['edit'] = "__GROUP__/msg/show";

                parent::edit();
            }


            protected function editMoreForm($form){

                return;
            }




		}






?>