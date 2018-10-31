<?php

	class ProductAction extends BaseAction{
		
		public function show(){
			/*position指定以及一些问候信息*/
			$current = "健康机产品管理";
			$position = getPosition("健康机产品管理");
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display();
		}
		
		public function data(){
			
			$maxDay = date('Y-m-d',time());
			$minDay = date('Y-m-d',time()-3600*24*30);
			$this->assign('maxDay',$maxDay);
			$this->assign('minDay',$minDay);
			/*position指定以及一些问候信息*/
			$current = "健康机数据管理";
			$position = getPosition($current);
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display();
		}
		
	}





























?>