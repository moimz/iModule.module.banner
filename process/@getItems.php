<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너항목 목록을 가져온다.
 * 
 * @file /modules/banner/process/@getItems.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.2.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;

$bid = Request('bid');
$lists = $this->db()->select($this->table->item.' i','i.*, b.title as banner_title')->join($this->table->banner.' b','b.bid=i.bid','LEFT');
if ($bid) $lists->where('b.bid',$bid);

$sort = Request('sort');
$dir = Request('dir');
$start = Request('start');
$limit = Request('limit');

$total = $lists->copy()->count();
$lists = $lists->orderBy($sort,$dir)->limit($start,$limit)->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$lists[$i]->banner_title = $lists[$i]->banner_title.' ('.$lists[$i]->bid.')';
	$lists[$i]->title = $lists[$i]->title;
	$lists[$i]->text = $lists[$i]->text;
	$lists[$i]->image = $lists[$i]->image > 0 ? $this->IM->getModule('attachment')->getFileInfo($lists[$i]->image) : null;
}

$results->success = true;
$results->lists = $lists;
$results->total = $total;
?>