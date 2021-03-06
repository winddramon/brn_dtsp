<?php

class item_dtsp extends item_bra
{
	
	public function apply($param = array())
	{
		$k = str_split($this->k);
		
		switch($k[0]){
			case 'S':
				if($k[1] == 'W'){
					$this->player->attack_by_weapon($this->data, false, true);
					$this->consume(1);
				}else if($k[1] == 'Y'){
					$this->special($param);
				}
				break;
			
			default:
				parent::apply($param);
				break;
		}
		
	}
	
	public function consume($num = 0, $rearrange = true)
	{
		global $g;
		if($num == 0 && $this->data['k'] == 'WG'){
			foreach($this->player->buff as &$buff){
				switch($buff['type']){
					//琪露诺套三件效果
					case 'cirno_suit':
						if($buff['param']['quantity'] >= 3){
							$this->player->notice('使用了冰晶子弹，没有消耗弹药');
							return;
						}
						break;
					
					default:
						break;
				}
			}
		}
		
		if($num == 0 && $this->data['k'] == 'WC'){
			foreach($this->player->buff as &$buff){
				switch($buff['type']){
					//十六夜套五件效果
					case 'sakuya_suit':
						if($buff['param']['quantity'] >= 5){
							if($g->determine(15)){
								$this->player->notice('回收了 '.$this->data['n']);
								return;
							}
						}
						break;
					
					default:
						break;
				}
			}
		}
		
		if($num == 0 && ($this->data['k'] == 'WP' || $this->data['k'] == 'WK' || $this->data['k'] == 'DA' || $this->data['k'] == 'DB' || $this->data['k'] == 'DF' || $this->data['k'] == 'DH')){
			foreach($this->player->buff as &$buff){
				switch($buff['type']){
					//霖之助套四件效果
					case 'rinnosuke_suit':
						if($buff['param']['quantity'] >= 4){
							$this->player->notice('由于使用得当， '.$this->data['n'].' 丝毫没有磨损');
						}
						break;
					
					default:
						break;
				}
			}
		}
		
		return parent::consume($num, $rearrange);
	}
	
	protected function special($param)
	{
		global $g;
		if($this->data['k'] === 'YU'){
			do{
				$this->apply_upgrade();
				$this->consume();
			}while($this->data['s'] > 0);
			return;
		}
		
		if($this->data['k'] === 'YS'){
			$this->apply_summon();
			$this->consume();
			return;
		}
		
		if($this->data['k'] === 'YR'){
			$this->apply_region_move();
			$this->consume();
			return;
		}
		
		$success = true;
		switch($this->data['n']){
			case '空气针管':
				$this->apply_suicide();
				$success = false;
				break;
				
			case 'BUFF状态切换器':
				$bufftype = $this->data['sk'][0];
				foreach($this->player->data['buff'] as &$bval){
					if($bval['type'] == $bufftype){
						if($bval['time'] > 0){
							$this->player->freeze_buff($bufftype);
						}elseif(isset($bval['param']['duration'])){
							$this->player->unfreeze_buff($bufftype);
						}
					}
				}
				
				$success = false;
				break;
			case '御神签':
				do{
					$this->apply_dice();
					$this->consume();
				}while($this->data['s'] > 0);
				$success = false;
				break;
			
			case '结界解除钥匙':
				$g->game_end('eliminate', $this->player->_id);
				return true;
				break;
			
			case '虚拟结界Bug':
				$g->game_end('destory', $this->player->_id);
				return true;
				break;
			
			case '魔法催化剂':
				$success = $this->apply_battery($GLOBALS['param']);
				break;
			
			case '结界干扰器':
				$success = $this->apply_hacker();
				break;
			
			case '凯夫拉纤维':
				$success = $this->apply_armor_enhancer('G', 0.6);
				break;
			
			case '血之精华':
				if($success = $this->apply_blood_knife()){
					return;
				}
				break;
			
			case '萌':
				$success = $this->apply_moe();
				break;
			
			case '境符「四重結界」':
				$this->consume();
				$success = $this->apply_package_amplifier(300, 4);
				return;
				break;
			
			case '怪力药丸':
				$success = $this->apply_att_buff(300, 100);
				break;
			
			case '忍耐药丸':
				$success = $this->apply_def_buff(300, 100);
				break;
			
			case '「スペル増幅」':
				$success = $this->apply_recover_sp_buff(100, 1, true);
				break;
			
			case '伊吹瓢':
				$success = $this->apply_recover_sp_buff(0, 1, true);
				break;
			
			case '「体力回復」':
				$success = $this->apply_recover_hp_buff(100, 1, true);
				break;
			
			case '病気平癒守':
				$success = $this->apply_recover_hp_buff(0, 1, true);
				break;
			
			case '緋想の剣':
				$success = $this->apply_weatherod();
				break;
			
			case '龍星':
				$success = $this->apply_invincible_potion(60);
				break;
			
			case '日符「ロイヤルフレア」':
				$success = $this->apply_royal_flare();
				break;
			
			case '月符「サイレントセレナ」':
				$success = $this->apply_silent_serena();
				break;
			
			case '足軽「スーサイドスクワッド」':
				$success = $this->apply_shield(300, 1000, 0.5);
				break;
			
			case '長視「赤月下」':
				$success = $this->apply_infrared_moon();
				break;
			
			case '短視「超短脳波」':
				$success = $this->apply_ultrashort_EEG();
				break;
			
			case '毒煙幕「瓦斯織物の玉」':
				$success = $this->apply_gas_woven_orb();
				break;
			
			case '身代わり人形':
				$success = $this->apply_scapegoat_dummy();
				break;
			
			case '制御棒':
				$success = $this->apply_control_rod();
				break;
			
			case '生薬「国士無双の薬」':
				$success = $this->apply_grand_patriots_elixir();
				break;
			
			case '禁薬「蓬莱の薬」':
				$success = $this->apply_medicine_of_horai();
				break;
			
			case '「夢想天生」':
				$success = $this->apply_fantasy_nature();
				break;
			
			default:
				if(strpos($this->data['n'], '钉') !== false){
					do{
						$this->apply_nail();
						$this->consume();
					}while($this->data['s'] > 0);
					$success = false;
					$this->player->calculate_battle_info();
					$this->player->ajax('item', array('equipment' => $this->player->parse_equipment()));
				}else if(strpos($this->data['n'], '磨刀石') !== false){
					do{
						$this->apply_whetstone();
						$this->consume();
					}while($this->data['s'] > 0);
					$success = false;
					$this->player->calculate_battle_info();
					$this->player->ajax('item', array('equipment' => $this->player->parse_equipment()));
				}else{
					return parent::special($param); //此处一定要返回，否则会重复消耗物品
				}
				break;
		}
		
		if($success){
			$this->consume();
		}
		
		return;
	}
	
	protected function apply_suicide(){
		$this->player->feedback('你把空气打进了你的静脉。一阵痉挛之后，你失去了知觉。');
		$this->player->sacrifice(array('type' => 'suicide'));
		return;
	}
	
	protected function apply_region_move()
	{
		global $m, $shopmap;
		if(!isset($this->data['sk']['region'])){
			$this->player->error('跨区移动参数错误1');
			return;
		}
		$region = $this->player->region+1;
		$destination = $m->get_region_access($region);
//		$mapname = $m->ar($destination);
		$this->player->feedback('你使用了'.$this->data['n'].'，跨越了区域的限制。');
		$this->player->move($destination, true, true);
//		$this->player->area = $destination;

//		$this->player->ajax('location', array('name' => $mapname, 'shop' => in_array(intval($this->player->area), $shopmap, true)));
		return;
	}
	
	protected function apply_upgrade()
	{
		global $a,$g;
		$up = $this->data['e'];
		$uphp = $g->random(0, $up);
		$upatt = $g->random(0, $up);
		$updef = $g->random(0, $up);
		
		$log = '【命】+'.$uphp.' 【攻】+'.$upatt.' 【防】+'.$updef;
		
		$this->player->mhp += $uphp;
		$this->player->att += $upatt;
		$this->player->def += $updef;
		
		$this->player->calculate_battle_info();
		$this->player->feedback('使用了'.$this->data['n'].'，浑身清爽。'.$log);
		
		$a->action('max_health', array('mhp' => $this->player->mhp, 'msp' => $this->player->msp));
	}
	
	protected function apply_summon()
	{
		global $g;
		$npc = $g->summon_npc($this->data['sk']['id']);
		
		$this->player->feedback($this->data['n'].' 消失了');
		$this->player->notice($GLOBALS['map'][$npc['area']].' 传来了奇怪的声音');
		
		return true;
	}
	
	protected function apply_battery($param)
	{
		$avaliable = array();
		$avaliable_name = array();
		foreach($this->player->package as $iid => $pitem){
			if($pitem['n'] == '移动PC' || $pitem['n'] == '雷达' || $pitem['n'] == '结界干扰器'){
				$avaliable[] = $iid;
				$avaliable_name[] = $pitem['n'];
			}
		}
		
		if(sizeof($avaliable) === 0){
			$this->player->error($this->data['n'].' 该怎么使用呢？');
			return false;
		}
		
		if(sizeof($avaliable) === 1){
			$param['target'] = $avaliable[0];
		}
		
		if(false === isset($param['target'])){
			$this->player->ajax('item_param', array(
				'id' => $this->id,
				'input' => array(array(
					'key' => '请选择要充电的物品',
					'name' => 'target',
					'type' => 'radio',
					'value' => $avaliable,
					'content' => $avaliable_name
					))
				));
			return false;
		}else{
			if($this->player->package[$param['target']]['n'] !== '移动PC' && $this->player->package[$param['target']]['n'] !== '雷达' && $this->player->package[$param['target']]['n'] !== '结界干扰器'){
				$this->player->error($this->player->package[$param['target']]['n'].' 不能充电');
				return false;
			}
			
			$this->player->data['package'][$param['target']]['s'] += $this->data['e'];
			$this->player->feedback($this->data['n'].' 使用成功， '.$this->player->package[$param['target']]['n'].'的耐久变成了 '.$this->player->package[$param['target']]['s']);
			return true;
		}
	}
	
	protected function apply_hacker()
	{
		global $g,$a;
		if($this->data['s'] == 0){
			$this->player->error($this->data['n'].' 没能量了，无法使用', false);
			return false;
		}
		
		if($g->determine(in_array('Hacker', $this->player->skill) ? 95 :50)){
			global $m;
			$all_map = array();
			$target_map = array();
			foreach($m->ar() as $mid){
				$all_map[] = $mid;
				if($mid !== 1){
					$target_map[] = $mid;
				}
			}
			
			$g->gameinfo['forbiddenlist'] = array();
			$g->moving_NPC($all_map, $target_map);
			$a->action('area_info', $g->get_areainfo(), true);
			$this->player->feedback('结界中和完毕，禁区解开了！');
		}else{
			$this->player->feedback('结界中和失败了');
		}
		
		return true;
	}
	
	protected function apply_weatherod()
	{
		global $g;
		$weather = $g->random(0, sizeof($GLOBALS['weatherinfo']) - 1);
		$g->gameinfo['weather'] = $weather;
		$this->player->feedback('天候棒使用成功，天气变成了'.$GLOBALS['weatherinfo'][$weather]);
		$g->action('weather', array('name' => $GLOBALS['weatherinfo'][$weather]), true);
		return true;
	}
	
	protected function apply_armor_enhancer($kind, $effect)
	{
		if($this->player->data['equipment']['arb']['n'] == ''){
			$this->player->error('未穿着体部护具');
			return false;
		}
		$this->player->data['equipment']['arb']['sk']['anti-'.$kind] = $effect;
		return true;
	}

	protected function apply_moe()
	{
		foreach($this->player->proficiency as &$pro){
			$pro += $this->data['e'];
		}
		$this->player->feedback('你感受到了'.$this->data['n'].'的精华，各项熟练度提高了！');
		$this->player->ajax('proficiency', array('proficiency' => $this->player->proficiency));

		return true;
	}

	protected function apply_package_amplifier($duration, $ettect)
	{
		$this->player->buff('extra_package', $duration, array('effect' => $ettect));
		$this->player->feedback('使用 '.$this->data['n'].' 成功，背包容量增加了 '.$ettect);
		return true;
	}

	protected function apply_att_buff($duration, $ettect)
	{
		$this->player->buff('att_buff', $duration, array('effect' => $ettect));
		$this->player->feedback('使用 '.$this->data['n'].' 成功，攻击力增加了 '.$ettect);
		return true;
	}

	protected function apply_def_buff($duration, $ettect)
	{
		$this->player->buff('def_buff', $duration, array('effect' => $ettect));
		$this->player->feedback('使用 '.$this->data['n'].' 成功，防御力增加了 '.$ettect);
		return true;
	}

	protected function apply_recover_hp_buff($duration, $ettect, $interrupt)
	{
		$this->player->buff('recover_hp', $duration, array('effect' => $ettect, 'interrupt' => $interrupt));
		$this->player->feedback('使用 '.$this->data['n'].' 成功，正在高速回复生命中');
		return true;
	}

	protected function apply_recover_sp_buff($duration, $ettect, $interrupt)
	{
		$this->player->buff('recover_sp', $duration, array('effect' => $ettect, 'interrupt' => $interrupt));
		$this->player->feedback('使用 '.$this->data['n'].' 成功，正在高速回复体力中');
		return true;
	}

	protected function apply_invincible_potion($duration)
	{
		$this->player->buff('invincible', $duration);
		$this->player->feedback('使用 '.$this->data['n'].' 成功，进入霸体状态');
		return true;
	}

	protected function apply_royal_flare()
	{
		$players_data = $GLOBALS['db']->select('players', '*', array('type' => GAME_PLAYER_USER, 'hp' => array('$gt' => 0)));

		foreach($players_data as &$player_data){
			if($player_data['uid'] == $this->player->uid){
				continue; //地图炮不伤害玩家自身
			}

			$player = new_player($player_data);

			if(false === $player->is_alive()){
				continue; //地图炮不打死人
			}

			$damage = $player->damage(100, array('pid' => $this->player->_id, 'weapon' => $this->data['n'], 'type' => 'weapon_d'));
			$player->feedback('你被 '.$this->data['n'].' 击中，造成 '.$damage.'点伤害');
		}

		$this->player->feedback('发动 '.$this->data['n'].' 中，整个天空闪耀着炽烈的光芒，灼烧着大地');

		return true;
	}

	protected function apply_silent_serena()
	{
		$players_data = $GLOBALS['db']->select('players', '*', array('area' => $this->player->area, 'hp' => array('$gt' => 0)));

		foreach($players_data as &$player_data){
			if($player_data['uid'] == $this->player->uid){
				continue; //地图炮不伤害玩家自身
			}

			$player = new_player($player_data);

			if(false === $player->is_alive()){
				continue; //地图炮不打死人
			}

			$damage = $player->damage(100, array('pid' => $this->player->_id, 'weapon' => $this->data['n'], 'type' => 'weapon_d'));
			$player->feedback('你被 '.$this->data['n'].' 击中，造成 '.$damage.'点伤害');

		}

		$this->player->feedback('发动 '.$this->data['n'].' 中，整个天空充斥着静谧却致命的白光');

		return true;
	}

	protected function apply_shield($duration, $amount, $effect)
	{
		$this->player->buff('shield', $duration, array('amount' => $amount, 'effect' => $effect));
		$this->player->feedback('使用 '.$this->data['n'].' 成功，如有神护');
		return true;
	}

	protected function apply_infrared_moon()
	{
		$this->player->buff('infrared_moon', 120);
		$this->player->feedback('发动 '.$this->data['n'].' 成功，现在敌人无法瞄准你了');
		return true;
	}

	protected function apply_ultrashort_EEG()
	{
		$this->player->buff('ultrashort_EEG', 120);
		$this->player->feedback('发动 '.$this->data['n'].' 成功，你的幻影获得了实体');
		return true;
	}

	protected function apply_gas_woven_orb()
	{
		$players_data = $GLOBALS['db']->select('players', '*', array('type' => GAME_PLAYER_USER, 'hp' => array('$gt' => 0)));
		
		foreach($players_data as &$player_data){
			if($player_data['uid'] == $this->player->uid){
				continue; //地图毒不伤害玩家自身
			}
			
			$player = new_player($player_data);
			
			if(false === $player->is_alive()){
				continue; //地图毒不打死人
			}
			
			$damage = $player->buff('poison', 300);
			$player->feedback('你受到了 '.$this->data['n'].' 的影响，中毒了');
			
		}
		
		$this->player->feedback('发动 '.$this->data['n'].' 中，整个大地都笼罩在了诡异而危险的绿雾之中');
		
		return true;
	}
	
	protected function apply_scapegoat_dummy()
	{
		$this->player->buff('scapegoat_dummy', 0);
		$this->player->feedback('使用 '.$this->data['n'].' 成功，虽然行动有些不便，但是好像护甲更加坚固了');
		return true;
	}
	
	protected function apply_control_rod()
	{
		$this->player->buff('control_rod', 0);
		$this->player->feedback('使用 '.$this->data['n'].' 成功，你感受到无尽的热血，侵略性更强了');
		return true;
	}
	
	protected function apply_grand_patriots_elixir()
	{
		$GPE_num = 0;
		foreach($this->player->data['buff'] as &$buff){
			if($buff['type'] === 'grand_patriots_elixir'){
				$GPE_num ++;
			}
		}
		
		if($GPE_num >= 3){
			//炸弹人
			if($this->player->action && isset($this->player->action['battle'])){
				$weapon = array(
					'n' => $this->data['n'],
					'e' => 1000,
					'k' => 'SW',
					's' => 0,
					'sk' => array('accurate' => true)
					);
				$this->player->attack_by_weapon($weapon, true, true);
				//取消国药效果
				foreach($this->player->buff as $key => &$buff){
					if($buff['type'] === 'grand_patriots_elixir'){
						$this->player->remove_buff($key);
					}
				}
				$this->player->feedback('一阵剧烈的爆炸后， '.$this->data['n'].' 的效果消失了');
				return true;
			}else{
				$this->player->error('再饮用 '.$this->data['n'].' 的话，也许会变成永琳所说的「那种东西」吧', false);
				return false;
			}
		}
		
		$this->player->buff('grand_patriots_elixir', 0);
		$this->player->feedback('饮用了 '.$this->data['n'].' ，体内涌动着无尽的能量');
		return true;
	}
	
	protected function apply_medicine_of_horai()
	{
		$this->player->buff('horai');
		$this->player->feedback('发动 '.$this->data['n'].' ，寿命的流逝停止了！');
		return true;
	}
	
	protected function apply_fantasy_nature()
	{
		$this->player->buff('fantasy_nature', 60, array('hits' => 0));
		$this->player->feedback('发动 '.$this->data['n'].' ！');
		return true;
	}
	
	protected function apply_blood_knife()
	{
		$target_item = false;
		
		if($this->player->equipment['wep']['n'] == '咲夜·红魔银刃'){
			$target_item = new_item($this->player, $this->player->equipment['wep'], 'wep');
		}else{
			foreach($this->player->package as $iid => &$item){
				if($item['n'] == '咲夜·红魔银刃'){
					$target_item = new_item($this->player, $item, $iid);
					break;
				}
			}
		}
		
		if(false === $target_item){
			return false;
		}
		
		$item_s = min($this->data['s'], $target_item->data['s']);
		
		$knife = array(
			'n' => '咲夜·红魔血刃',
			'k' => 'WC',
			'e' => $target_item->data['e'] + $this->data['e'],
			's' => $item_s,
			'sk' => array('suit' => 'sakuya')
			);
		
		if($this->data['s'] < $target_item->data['s']){
			//新物品
			if(isset($this->player->package[0])){
				return $this->player->error('请先决定如何处理拾取到的物品');
			}
			
			$this->player->package[0] = $knife;
			$target_item->consume($item_s, false);
		}else{
			//直接替换武器
			$target_item->data['n'] = $knife['n'];
			$target_item->data['k'] = $knife['k'];
			$target_item->data['e'] = $knife['e'];
			$target_item->data['s'] = $knife['s'];
			$target_item->data['sk'] = $knife['sk'];
		}
		$this->player->feedback($this->data['n'].' 使用成功， 咲夜·红魔银刃 变成了 咲夜·红魔血刃');
		$this->consume($item_s);
		$this->player->ajax('item', array('equipment' => $this->player->parse_equipment(), 'package' => $this->player->parse_package()));
		
		return true;
	}
	
	protected function get_trap_effect()
	{
		//爆系熟练关联
		$effect = intval(parent::get_trap_effect() * (1 + (sqrt(2500 + $this->player->data['proficiency']['d']) - 50) / 2));
		$modulus = 1;
		
		foreach($this->player->buff as &$buff){
			switch($buff['type']){
				//琪露诺套四件效果
				case 'cirno_suit':
					if($buff['param']['quantity'] >= 4){
						$modulus *= 2;
					}
					break;
					
				//爱丽丝套四件效果
				case 'alice_suit':
					if($buff['param']['quantity'] >= 4){
						$modulus *= 1.5;
					}
					break;
				
				default:
					break;
			}
		}
		
		return $effect * $modulus;
	}
	
	protected function set_trap()
	{
		if($this->data['k'] !== 'TN'){
			return $this->player->error('无法设置此陷阱');
		}
		
		parent::set_trap();
		
		$this->player->data['proficiency']['d'] += 4; //算上BRN里的+1一共是+5
	}
	
	protected function heal($kind)
	{
		global $tolerance;
		
		$in_tolerance = false;
		foreach($this->player->buff as &$buff){
			switch($buff['type']){
				//耐药性效果
				case 'tolerance':
					$in_tolerance = true;
					break;
					
				default:
					break;
			}
		}
//		if($in_tolerance){
//			$modulus = $tolerance['modulous'];
//			$this->data['e'] *= $tolerance['modulous'];
//		}else{
//			$modulus = 1;
//		}

		$heal_type = array('H' => 'hp', 'S' => 'sp', 'B' => 'all');
		$modulus = $this->player->get_potion_effect();
		if($in_tolerance){
			$modulus['hp'] *= $tolerance['modulous'];
			$modulus['sp'] *= $tolerance['modulous'];
		}
		if(isset($heal_type[$kind])){
			$type = $heal_type[$kind];
			global $healthinfo;
			if($type === 'all'){
				if($this->player->data['hp'] == $this->player->data['mhp'] && $this->player->data['sp'] == $this->player->data['msp']){
					global $healthinfo;
					$this->player->error($healthinfo['hp'].'与'.$healthinfo['sp'].'已经达到最大值，无需补充');
				}else{
					if(isset($this->data['sk']['poison'])){
						//毒
						$damage_source = array('type' => 'poison');
						if(isset($this->data['sk']['poison-applier'])){
							$damage_source['pid'] = $this->data['sk']['poison-applier'];
						}
						$damage = $this->player->damage($this->data['e'], $damage_source);
						$this->player->buff('poison', $this->data['sk']['poison']);
						$this->player->feedback('糟糕，'.$this->data['n'].'有毒，你中毒了，并失去了'.$damage.'点'.$healthinfo['hp']);
					}else{

						$hp_add = $this->player->heal('hp', $this->data['e'] * $modulus['hp']);
						$sp_add = $this->player->heal('sp', $this->data['e'] * $modulus['sp']);
						$this->player->feedback($this->data['n'].'使用成功，'.$healthinfo['hp'].'增加了'.strval($hp_add).'，'.$healthinfo['sp'].'增加了'.strval($sp_add));
					}
				}

			}else if(isset($this->player->data[$type])){
				if($this->player->data[$type] >= $this->player->data['m'.$type]){
					$this->player->error($healthinfo[$type].'已经达到最大值，无需补充');
				}else{
					if(isset($this->data['sk']['poison'])){
						//毒
						$damage_source = array('type' => 'poison');
						if(isset($this->data['sk']['poison-applier'])){
							$damage_source['pid'] = $this->data['sk']['poison-applier'];
						}
						$damage = $this->player->damage($this->data['e'], $damage_source);
						$this->player->buff('poison', $this->data['sk']['poison']);
						$this->player->feedback('糟糕，'.$this->data['n'].'有毒，你中毒了，并失去了'.$damage.'点'.$healthinfo['hp']);
					}else{
						$point_add = $this->player->heal($type, $this->data['e'] * $modulus[$type]);
						$this->player->feedback($this->data['n'].'使用成功，'.$healthinfo[$type].'增加了'.strval($point_add));
					}
				}
			}else{
				$this->player->feedback('这补给品似乎是补充某种未知能力的');
			}
			$this->player->ajax('health', array('hp' => $this->player->hp, 'sp' => $this->player->sp));
		}else{
			$this->player->feedback('这补给品似乎没有效果');
		}

		$this->consume();
		
		foreach($this->player->buff as &$buff){
			switch($buff['type']){
				//永琳套五件效果
				case 'eirin_suit':
					if($buff['param']['effect'] >= 5){
						$modulus['hp'] *= 2;
					}
					$is_poisoning = false;
					foreach($this->player->buff as $key => $buff){
						if($buff['type'] === 'poison'){
							$this->player->remove_buff($key);
							$is_poisoning = true;
						}
					}
					if($is_poisoning){
						$this->player->feedback('毒状态解除了');
					}
					break;
				
				default:
					break;
			}
		}
		
//		if($in_tolerance && $this->data['s']){
//			$this->data['e'] /= $tolerance['modulous'];
//		}
		
		//副作用实现
		if(isset($this->data['sk']['side-effect'])){
			$se = &$this->data['sk']['side-effect'];
			$duration = &$se['duration'];
			$log = $this->data['n'].' 产生了副作用！';
			if(isset($se['att'])){
				if($se['att'] > 0){
					$this->player->feedback('攻击提升了 '.$se['att'].' ');
					$this->player->buff('att_buff', $duration, array('effect' => $se['att']));
				}else{
					$this->player->feedback('攻击降低了 '.$se['att'].' ');
					$this->player->buff('att_debuff', $duration, array('effect' => $se['att']));
				}
			}
			if(isset($se['def'])){
				if($se['def'] > 0){
					$this->player->feedback('防御提升了 '.$se['def'].' ');
					$this->player->buff('def_buff', $duration, array('effect' => $se['def']));
				}else{
					$this->player->feedback('防御降低了 '.$se['def'].' ');
					$this->player->buff('def_debuff', $duration, array('effect' => $se['def']));
				}
			}
		}
		
		//耐药性实现
		if($tolerance['last'] > 0){
			$this->player->feedback('急速的回复使你的身体产生了耐药性，需要一段时间才能消除。');
			$duration = $this->data['e'] * $tolerance['last'];
			$this->player->buff('tolerance', $duration);
		}
		
		return;
	}
}