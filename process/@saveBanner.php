<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너그룹을 저장한다.
 * 
 * @file /modules/banner/process/@saveBanner.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.2.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;

$errors = array();

$oBid = Request('oBid');
$bid = Request('bid') ? Request('bid') : $errors['bid'] = $this->getErrorText('REQUIRED');
$title = Request('title') ? Request('title') : $errors['title'] = $this->getErrorText('REQUIRED');

if ($oBid) {
	if ($this->db()->select($this->table->banner)->where('bid',$bid)->where('bid',$oBid,'!=')->has() == true) {
		$errors['bid'] = $this->getErrorText('DUPLICATED');
	}
	
	if ($this->db()->select($this->table->banner)->where('title',$title)->where('bid',$oBid,'!=')->has() == true) {
		$errors['title'] = $this->getErrorText('DUPLICATED');
	}
	
	if (count($errors) == 0) {
		$this->db()->update($this->table->banner,array('bid'=>$bid,'title'=>$title))->where('bid',$oBid)->execute();
		$this->db()->update($this->table->item,array('bid'=>$bid))->where('bid',$oBid)->execute();
	}
} else {
	if ($this->db()->select($this->table->banner)->where('bid',$bid)->has() == true) {
		$errors['bid'] = $this->getErrorText('DUPLICATED');
	}
	
	if ($this->db()->select($this->table->banner)->where('title',$title)->has() == true) {
		$errors['title'] = $this->getErrorText('DUPLICATED');
	}
	
	if (count($errors) == 0) {
		$this->db()->insert($this->table->banner,array('bid'=>$bid,'title'=>$title))->execute();
	}
}

if (count($errors) == 0) {
	$results->success = true;
} else {
	$results->success = false;
	$results->errors = $errors;
}
?>