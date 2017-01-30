<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodule.kr)
 *
 * 배너그룹을 가져온다.
 * 
 * @file /modules/banner/process/@getGroups.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0.160910
 */
if (defined('__IM__') == false) exit;

$is_all = Request('is_all') == 'true';
$type = Request('type');
$lists = $this->db()->select($this->table->group);
$lists = $lists->orderBy('title','asc')->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$lists[$i]->banner = $this->db()->select($this->table->banner)->where('gidx',$lists[$i]->idx)->count();
	$lists[$i]->sort = $i;
}

if ($is_all == true) {
	$all = new stdClass();
	$all->idx = 0;
	$all->title = $this->getText('text/all_group');
	$all->sort = -1;
	
	$lists[] = $all;
}

$results->success = true;
$results->lists = $lists;
$results->total = count($lists);
?>