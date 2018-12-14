<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너를 저장한다.
 * 
 * @file /modules/banner/process/@saveBanner.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 11. 22.
 */
if (defined('__IM__') == false) exit;

$errors = array();

$idx = Request('idx');
$gidx = Request('gidx') ? Request('gidx') : $errors['gidx'] = $this->getErrorText('REQUIRED');
$sort = is_numeric(Request('sort')) == true ? Request('sort') : $errors['sort'] = $this->getErrorText('REQUIRED');
$permission = Request('permission');
$url = Request('url') ? Request('url') : '#';
$target = Request('target') ? Request('target') : '_blank';

if (count($errors) == 0) {
	$group = $this->db()->select($this->table->group)->where('idx',$gidx)->getOne();
	if ($group == null) $errors['gidx'] = $this->getErrorText('NOT_FOUND');
	
	if (strpos($group->type,'TITLE') !== false) {
		$title = Request('title') ? Request('title') : $errors['title'] = $this->getErrorText('REQUIRED');
	} else {
		$title = null;
	}
	
	if (strpos($group->type,'TEXT') !== false) {
		$text = Request('text') ? Request('text') : $errors['text'] = $this->getErrorText('REQUIRED');
	} else {
		$text = null;
	}
	
	if ($idx) {
		$data = $this->db()->select($this->table->banner)->where('idx',$idx)->getOne();
		$imageIdx = $data->image;
	} else {
		$imageIdx = 0;
	}
	
	if (strpos($group->type,'IMAGE') !== false) {
		if (isset($_FILES['image']) == true && $_FILES['image']['tmp_name']) {
			if ($imageIdx == 0) {
				$imageIdx = $this->IM->getModule('attachment')->fileSave($_FILES['image']['name'],$_FILES['image']['tmp_name'],'banner','item','PUBLISHED',true);
			} else {
				$imageIdx = $this->IM->getModule('attachment')->fileReplace($imageIdx,$_FILES['image']['name'],$_FILES['image']['tmp_name'],true);
			}
		}
		
		if (Request('image_delete')) {
			if ($imageIdx > 0) $this->IM->getModule('attachment')->fileDelete($imageIdx);
			$imageIdx = 0;
		}
	} else {
		if ($imageIdx > 0) $this->IM->getModule('attachment')->fileDelete($imageIdx);
		$imageIdx = 0;
	}
	
	$insert = array();
	$insert['gidx'] = $gidx;
	$insert['sort'] = $sort;
	$insert['permission'] = $permission;
	$insert['url'] = $url;
	$insert['target'] = $target;
	$insert['reg_date'] = time();
	
	if ($title !== null) $insert['title'] = $title;
	if ($text !== null) $insert['text'] = $text;
	$insert['image'] = $imageIdx;
	
	if ($idx) $insert['idx'] = $idx;
	
	$this->db()->replace($this->table->banner,$insert)->execute();
	
	$results->success = true;
} else {
	$results->success = false;
	$results->errors = $errors;
}
?>