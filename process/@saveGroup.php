<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너그룹을 저장한다.
 * 
 * @file /modules/banner/process/@saveGroup.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 11. 22.
 */
if (defined('__IM__') == false) exit;

$errors = array();

$idx = Request('idx');
$title = Request('title') ? Request('title') : $errors['title'] = $this->getErrorText('REQUIRED');
$type = Request('type');

if ($idx) {
	if ($this->db()->select($this->table->group)->where('title',$title)->where('idx',$idx,'!=')->has() == true) {
		$errors['title'] = $this->getErrorText('DUPLICATED');
	}
	
	if (count($errors) == 0) {
		$this->db()->update($this->table->group,array('title'=>$title,'type'=>$type))->where('idx',$idx)->execute();
	}
} else {
	if ($this->db()->select($this->table->group)->where('title',$title)->has() == true) {
		$errors['title'] = $this->getErrorText('DUPLICATED');
	}
	
	if (count($errors) == 0) {
		$this->db()->insert($this->table->group,array('title'=>$title,'type'=>$type))->execute();
	}
}

if (count($errors) == 0) {
	$results->success = true;
} else {
	$results->success = false;
	$results->errors = $errors;
}
?>