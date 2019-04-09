<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너를 가져온다.
 * 
 * @file /modules/banner/process/@getBanners.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;

$is_all = Request('is_all') == 'true';
$type = Request('type');
$lists = $this->db()->select($this->table->banner);
$lists = $lists->orderBy('title','asc')->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$lists[$i]->item = $this->db()->select($this->table->banner)->where('bid',$lists[$i]->bid)->count();
	$lists[$i]->sort = $i;
}

if ($is_all == true) {
	$all = new stdClass();
	$all->bid = '';
	$all->title = $this->getText('text/all');
	$all->sort = -1;
	
	$lists[] = $all;
}

$results->success = true;
$results->lists = $lists;
$results->total = count($lists);
?>