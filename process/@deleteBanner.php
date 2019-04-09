<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너를 삭제한다.
 * 
 * @file /modules/banner/process/@deleteBanner.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.1.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;

$bid = Request('bid') ? explode(',',Request('bid')) : array();
if (count($bid) > 0) {
	$this->db()->delete($this->table->banner)->where('bid',$bid,'IN')->execute();
	$items = $this->db()->select($this->table->item)->where('bid',$bid,'IN')->get();
	foreach ($items as $item) {
		if ($item->image > 0) $this->IM->getModule('attachment')->fileDelete($item->image);
	}
	$this->db()->delete($this->table->item)->where('bid',$bid,'IN')->execute();
}

$results->success = true;
?>