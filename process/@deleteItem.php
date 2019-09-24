<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너를 삭제한다.
 * 
 * @file /modules/banner/process/@deleteItem.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.2.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;

$idx = Request('idx') ? explode(',',Request('idx')) : array();
if (count($idx) > 0) {
	$items = $this->db()->select($this->table->item)->where('idx',$idx,'IN')->get();
	foreach ($items as $item) {
		if ($item->image > 0) $this->IM->getModule('attachment')->fileDelete($item->image);
	}
	
	$this->db()->delete($this->table->item)->where('idx',$idx,'IN')->execute();
}

$results->success = true;
?>