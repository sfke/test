<?php
	
	/*
	 * 友情链接管理
	 */
	
		class FaqAction extends ExtAction{

            protected $_config = array(
                'cid'=>         19,
                'class'=>       __CLASS__,
                'map'=>         array(),
                'title'=>       "医患交流",
                'pagesize'=>    '20',
                'sortable'=>    false,
                'hidden'=>      array(),
                'sortField'=>   "",
                'filterable'=>  true,
                'filterField'=>"ksid",
                'filterHtml'=>  "",
                'addable'=>     true,
                'deleteable'=>  true,
                'search'=>      array(
                    'like'=>    array('question'=>'问题','id'=>'问题ID'),
                    'orderby'=> array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','check_asc'=>'审核 在前','check_desc'=>'未审核 在前')
                )
            );



            public static function  getToolbar($param){
                return "";
            }

            public static function  getHandle($arr){
                return '';
            }



			public function show(){
                $ksid = $this->_param('ksid');
                $arr = array("0"=>"全科室","1"=>"科室1","2"=>"科室2");

                //站点选择
                $siteArr = getAvailableSitesArr();
                $html = "<option value='0'>全部科室</option>";
                foreach($siteArr as $v){
                    if($ksid == $v['id']) $flag="selected='selected'"; else $flag="";
                    $html.='<option value="'.$v['id'].'" '.$flag.' >'.$v['name'].'</option>';
                }


                $this->config['filterHtml'] = $html;
                if(!empty($ksid)){
                    $this->config['map']['ksid'] = array('eq',$ksid);
                }
                $this->config['hidden'] = array("ksid"=>$ksid);
                parent::show();
			}
			

            public function add(){
                $this->config['returnUrl']['add'] = "__GROUP__/faq/show";
                parent::add();
            }

            protected function addMoreForm($form){
                $form->add('hidden','time',time());
                return;
            }

			public function edit(){
				$this->config['returnUrl']['jump'] = "__GROUP__/faq/show";
                $this->config['returnUrl']['edit'] = "__GROUP__/faq/show";
                parent::edit();
			}

            protected function editMoreForm($form){
                $form->add('hidden','edittime',time());
                return;
            }


		}

?>