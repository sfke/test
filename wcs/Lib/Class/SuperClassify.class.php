<?php

/**
 * file: SuperClassify.class.php
 * intro: 
 * @date: 2012-8-21
 * @author: LHY
 * @version: 2.0
 */

class SuperClassify {
	private $date;
	public $id_name = array (); // 栏目名称和id的对应关系 方便调用
	public $nodes = array (); // 栏目树的原始节点
	public $treeArr = array (); // 加工后的树节点 已排好序
	public $flag = "---"; // 树分割符号
	public $cut = " > "; // 路由分割符号
	
	function __construct($data) {
		
		$this->createNode ( $data );
	}
	
	function __toString() {
		return " SuperClassify vesion:2.0 ";
	}
	
	private function createNode($data) {
		$i = 0; // 索引计数器
		foreach ( $data as $v ) {
			$arr = $v;
			$arr ['grade'] = sizeof ( explode ( '-', $v ['route'] ) ) - 1;
			$this->nodes [] = $arr;
			$this->id_name [$arr ['id']] = $i;
			$i ++;
		}
		
		return true;
	}
	
	/**
	 * 递归遍历树，展开所有
	 *
	 * @param $id 根节点的id       	
	 */
	public function create($id = 0) {
		if ($this->treeArr != null) {
			$this->treeArr = null;
		}
		if ($id == 0) {
			
			$this->createTree ( 0, 0, $this->flag );
		
		} else {
			
			$k = $this->getKey ( $id );
			$layer = $this->nodes [$k] ['grade'] + 1;
			$this->createTree ( $layer, $id, $this->flag );
		}
	}
	
	private function getKey($id) {
		if (isset ( $this->id_name [$id] ))
			return $this->id_name [$id];
		else
			return - 1;
	}
	
	public function createTree($layer = 3, $fid = 8, $flag = "---") {
		static $n = 0;
		foreach ( $this->nodes as $v ) {
			if ($v ['grade'] == $layer && $v ['fid'] == $fid) {
				$arr [$v ['order']] = $v;
			}
		}
		if (! isset ( $arr ))
			return false;
		ksort ( $arr );
		foreach ( $arr as $x ) {
			$this->treeArr [$n] ['name'] =  str_repeat ( $flag, $x ['grade'] ) . $x ['name'];
			$this->treeArr [$n] ['id'] = $x ['id'];
			$this->treeArr [$n] ['fid'] = $x ['fid'];
			$this->treeArr [$n] ['grade'] = $x ['grade'];
			$this->treeArr [$n] ['order'] = $x ['order'];
			$this->treeArr [$n] ['route'] = $x ['route'];
			$this->treeArr [$n] ['cid'] = $x ['channel'];
			$this->treeArr [$n] ['url'] = $x ['url'];
			$this->treeArr [$n] ['type'] = $x ['type'];
			$this->treeArr [$n] ['status'] = $x ['status'];
			$n ++;
			$this->createTree ( $layer + 1, $x ['id'], $flag );
		
		}
		
		return true;
	}
	

	public function routeLine($id) {
		$id = $this->getKey ( $id );
		$routeLine = '<a href="'.indexUrl().'index.php">首页</a>'.$this->cut;
		//当站点ID=2为英文站可开启
		if(getSiteId()==2){
		  $routeLine = '<a href="'.indexUrl().'">Home</a>'.$this->cut;
		}
		$fArr = explode ( "-", $this->nodes [$id] ['route'] );
		foreach ( $fArr as $v ) {
			$k = $this->getKey ( $v );
			if($k>=0){
                if(!empty($this->nodes[$k]['url'])){
                    $tempUrl = parseArctypeUrl($this->nodes[$k]['url']);
                    $routeLine .= "<a href='".$tempUrl."'>" . $this->nodes [$k] ['name'] . "</a>" . $this->cut;
                }else{
                    $routeLine .= "<a href='".listUrl($this->nodes[$k]['id'])."'>" . $this->nodes [$k] ['name'] . "</a>" . $this->cut;
                }

            }

		}
		 	
		$routeLine .= '<i>'.$this->nodes [$id] ['name'].'</i>';
		return $routeLine;
	}
	
	/**
	 * 获取ID对应的栏目名称
	 *
	 * @param $id int       	
	 * @return string
	 */
	public function getNameById($id) {
		$key = $this->getKey ( $id );
		return $this->nodes [$key] ['name'];
	
	}

}

?>