<?php

class map_container_dtsp
{
	protected $db;
	protected $regions = array();
	protected $areas = array();
	protected $areas_static = array();
	protected $keys_all_area = false;
	protected $keys_area_by_region = false;

	public function __construct($db)
	{
		$this->db = $db;
		$this->load_static();
		$this->load_database();
	}

	public function load_database(){

		$areas = $this->db->select('areas', '*');
		if(!empty($areas)){
			$this->areas = Array();
			foreach($areas as $aval){
				$this->areas[$aval['_id']] = new area_dtsp($aval);
			}
		}
	}

	public function save_database(){
		$areas = $this->areas;
		if ($areas) {
			$maplist = Array();
			foreach ($this->areas as $mobj) {
				$maplist[] = Array(
					'_id' => $mobj->_id,
					'n' => $mobj->n,
					'c' => $mobj->c,
					'r' => $mobj->r,
					'info' => $mobj->info
				);
			}
			if(!empty($maplist)){
				$this->db->batch_update('areas',$maplist);
			}
		}
	}

	public function load_static()
	{//载入settings静态数据
		global $regioninfo, $mapinfo;
//		foreach ($mapinfo as $mval) {
//			$this->areas_static[$mval['_id']] = new area_dtsp($mval);
//		}
		foreach ($regioninfo as $rval) {
			$this->regions[$rval['_id']] = new region_dtsp($rval);
			foreach ($rval['group'] as $rgval) {
				foreach ($rgval['mapinfo'] as $mival){
					$mival['r'] = $rval['_id'];
					$this->areas_static[$mival['_id']] = new area_dtsp($mival);
				}
//				foreach ($rgval['arealist'] as $lval) {
//					if (isset($this->areas_static[$lval])) {
//						$this->areas_static[$lval]->r = $rval['_id'];
//					}
//				}
			}
		}
	}

//	public function load_active($maplist = array())
//	{//把gameinfo动态数据和静态数据合并，废弃中
//		global $g;
//		if (empty($maplist)) {
//			$maplist = $g->gameinfo['maplist'];
//		}
//		if ($maplist) {
//			$this->areas=array();
//			foreach ($maplist as $mval) {
//				$this->areas[$mval['_id']] = new area_dtsp(array_merge($this->areas_static[$mval['_id']]->data, $mval));
//			}
//		}
//	}

	public function reset_active()
	{
		//global $g, $map_size;
		$maplist = array();
		foreach ($this->regions as $robj) {
			foreach ($robj->group as $rgval) {
				if ($rgval['num'] < 0) {//先把全部固定地图标注完毕
					foreach ($rgval['mapinfo'] as $mival){
						$map_coordinates[] = $this->areas_static[$mival['_id']]->c;
						$maplist[] = $this->areas_static[$mival['_id']]->data;
					}
//					foreach ($rgval['arealist'] as $lval) {
//						$map_coordinates[] = $this->areas_static[$lval]->c;
//						$maplist[] = $this->areas_static[$lval]->data;
//					}
				}
			}
		}
		foreach ($this->regions as $robj) {//之后分配随机地图。这个参数是0的地图完全不放置
			$map_coordinates = array();
			foreach ($robj->group as $rgval) {
				if ($rgval['num'] > 0) {
					$list = array();
					foreach ($rgval['mapinfo'] as $mival){
						$list[] = $mival['_id'];
					}
//					$list = $rgval['arealist'];
					shuffle($list);
					$mlist = array_slice($list, 0, $rgval['num']);
					foreach ($mlist as $lval) {
						$sub = $this->areas_static[$lval];
						$i = 0;
						do {
							shuffle($rgval['randomcoors']);
							$mcoor = $rgval['randomcoors'][0];
							//$mcoor = $g->random(0, $map_size[0]) . '-' . $g->random(0, $map_size[1]);
							if ($i >= 1000) {
								throw_error('Initiating maps failed. '.$mival['_id']);
							}
							$i++;
						} while (in_array($mcoor, $map_coordinates));
						$sub->c = $mcoor;
						$map_coordinates[] = $sub->c;
						$maplist[] = $sub->data;
					}
				}
			}
		}
		$this->areas = Array();
		foreach ($maplist as $mval) {
			$this->areas[$mval['_id']] = new area_dtsp(array_merge($this->areas_static[$mval['_id']]->data, $mval));
		}
//		file_put_contents('a.txt',serialize($maplist));
		//$this->load_active($maplist);
		$this->save_database();

		//$this->set_active();
	}

//	public function set_active()
//	{//把动态数据写入gameinfo，已废弃
//		global $g;
//		if ($this->areas) {
//			$maplist = Array();
//			foreach ($this->areas as $mobj) {
//				$maplist[] = Array(
//					'_id' => $mobj->_id,
//					'c' => $mobj->c
//				);
//			}
//			$g->gameinfo['maplist'] = $maplist;
//		}
//	}

	public function ar($c = 'allkeys', $p = '')
	{//自动识别是地图编号还是坐标，并返回地图对象
		if ($c === 'allkeys') {
			if(!$this->keys_all_area){//暂存常用的读取以减少无谓的运算
				$this->keys_all_area = array_keys($this->areas);
			}
			return $this->keys_all_area;
		} elseif ($c === 'all') {
			return $this->areas;
		} elseif ($c === '_id' && is_numeric($p)) {
			return $this->areas[$p];
		} elseif ($c === 'c' && strpos($p,'-')!==false) {
			foreach ($this->areas as $aobj) {
				if ($aobj->c === $p) {
					return $aobj;
				}
			}
		} elseif ($c === 'r' && is_numeric($p)) {
			if(!$this->keys_area_by_region){
				foreach ($this->areas as $aobj) {
					$this->keys_area_by_region[$aobj->r][$aobj->_id] = $aobj;
				}
			}
			return $this->keys_area_by_region[$p];
		}
	}

	public function rg($c = 'allkeys', $p = '')
	{
		if ($c === 'allkeys') {
			return array_keys($this->regions);
		} elseif ($c === 'all') {
			return $this->regions;
		} elseif ($c === '_id' && is_numeric($p)) {
			return $this->regions[$p];
		} elseif ($c === 'type' && in_array($p, Array('start', 'normal', 'end'))) {
			$regions = array();
			foreach ($this->regions as $rkey => $robj) {
				if ($robj->type == $p) {
					$regions[] = $robj;
				}
			}
			if (sizeof($regions) == 0) {
				return false;
			} elseif (sizeof($regions) == 1) {
				return $regions[0];
			} else {
				return $regions;
			}
		}
	}
	public function get_region_maps($region){//打包传给前端
		global $img_dir;
		$return = array();
		$areas = $this->ar('r',$region);
		foreach ($areas as $aobj){
			$return['areas'][] = array('_id' => $aobj->_id, 'n'=> $aobj->n, 'c'=>$aobj->c);
		}
		$robj = $this->rg('_id',$region);
		$return['regioninfo'] = array('displaysize' => $robj->displaysize, 'background' => 'img/'.$img_dir.'/'.$robj->background);
		return $return;
	}

	public function get_region_access($region)
	{
		global $g, $m;
		$destination = $this->rg('_id',$region)->access;
		if(!$destination || ($destination >= 0 && !$m->ar('_id',$destination))){
			$cplayer = $g->current_player();
			$cplayer->error('destination:' .$destination. ' 跨区移动参数错误2');
			return;
		}
		if($destination < 0){//该等级随机
			$dlist = array();
			foreach($m->ar('all') as $dobj){
				if($dobj->r == $region){
					$dlist[] = $dobj;
				}
			}
			shuffle($dlist);
			$destination = $dlist[0]->_id;
		}

		return $destination;
	}
}

class area_dtsp
{
	protected $data;

	public function __construct($data)
	{
		$this->data['_id'] = $data['_id'];
		$this->data['n'] = $data['n'];
		$this->data['c'] = isset($data['c']) ? $data['c'] : false;
		$this->data['r'] = isset($data['r']) ? $data['r'] : false;
		$this->data['info'] = isset($data['info']) ? $data['info'] : false;
	}

	public function update($data)
	{
		if ($this->data['_id'] == $data['_id']) {
			foreach ($data as $dkey => $dval) {
				if ($dkey != '_id') {
					$this->data[$dkey] = $dval;
				}
			}
		}
	}

	public function &__get($p)
	{
		if ($p === 'data') {
			return $this->data;
		}
		if (false === isset($this->data[$p])) {
			throw_error('Undefined property in area_dtsp: ' . $p);

		}
		return $this->data[$p];
	}

	public function __set($p, $v)
	{
		if ($p === 'data' && is_array($v)) {
			return $this->data = $v;
		}
//		if(false === isset($this->data[$p])){
//			throw_error('Undefined property in setting area_dtsp: '.$p);
//		}
		return $this->data[$p] = $v;
	}
}

class region_dtsp
{
	protected $data;

	public function __construct($data)
	{
		$this->data['_id'] = $data['_id'];
		$this->data['name'] = $data['name'];
		$this->data['destination'] = $data['destination'];
		$this->data['type'] = $data['type'];
		$this->data['access'] = $data['access'];
		$this->data['duration'] = $data['duration'];
		$this->data['displaysize'] = $data['displaysize'];
		$this->data['background'] = $data['background'];
		$this->data['group'] = $data['group'];
	}

	public function update($data)
	{
	}

	public function &__get($p)
	{
		if ($p === 'data') {
			return $this->data;
		}
		if (false === isset($this->data[$p])) {
			throw_error('Undefined property in region_dtsp: ' . $p);
		}
		return $this->data[$p];
	}

	public function __set($p, $v)
	{
		if ($p === 'data') {
			return $this->data;
		}
		return $this->data[$p] = $v;
	}
}