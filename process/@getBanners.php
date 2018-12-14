<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너목록을 가져온다.
 * 
 * @file /modules/banner/process/@getBanners.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 12. 14.
 */
if (defined('__IM__') == false) exit;

$gidx = Request('gidx');
$lists = $this->db()->select($this->table->banner.' b','b.*,g.idx as gidx, g.title as group_title')->join($this->table->group.' g','g.idx=b.gidx','LEFT');
if ($gidx) $lists->where('b.gidx',$gidx);

$sort = Request('sort');
$dir = Request('dir');
$start = Request('start');
$limit = Request('limit');

$total = $lists->copy()->count();
$lists = $lists->orderBy($sort,$dir)->limit($start,$limit)->get();

for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$lists[$i]->group_title = '['.$lists[$i]->gidx.'] '.$lists[$i]->group_title;
	$lists[$i]->text = ($lists[$i]->title ? '['.$lists[$i]->title.']' : '').$lists[$i]->text;
	$lists[$i]->image = $lists[$i]->image > 0 ? $this->IM->getModule('attachment')->getFileInfo($lists[$i]->image) : null;
}

$results->success = true;
$results->lists = $lists;
$results->total = $total;
?>