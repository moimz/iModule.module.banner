<?php
/**
 * 이 파일은 iModule 배너모듈 일부입니다. (https://www.imodules.io)
 *
 * 배너항목을 저장한다.
 * 
 * @file /modules/banner/process/@saveItem.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2019. 2. 5.
 */
if (defined('__IM__') == false) exit;

$errors = array();

$idx = Request('idx');
$bid = Request('bid') ? Request('bid') : $errors['bid'] = $this->getErrorText('REQUIRED');
$title = Request('title') ? Request('title') : '';
$title_color = Request('title_color_default') ? '' : Request('title_color');
$text = Request('text') ? Request('text') : '';
$sort = is_numeric(Request('sort')) == true ? Request('sort') : $errors['sort'] = $this->getErrorText('REQUIRED');
$text_color = Request('text_color_default') ? '' : Request('text_color');
$permission = Request('permission');
$url = Request('url') ? Request('url') : '#';
$target = Request('target') ? Request('target') : '_self';

$banner = $this->db()->select($this->table->banner)->where('bid',$bid)->getOne();
if ($banner == null) {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND');
	return;
}

if (count($errors) == 0) {
	if ($idx) {
		$data = $this->db()->select($this->table->item)->where('idx',$idx)->getOne();
		$imageIdx = $data->image;
	} else {
		$imageIdx = 0;
	}
	
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
	
	$insert = array();
	$insert['bid'] = $bid;
	$insert['title'] = $title;
	$insert['title_color'] = $title_color;
	$insert['text'] = $text;
	$insert['text_color'] = $text_color;
	$insert['image'] = $imageIdx;
	$insert['sort'] = $sort;
	$insert['permission'] = $permission;
	$insert['url'] = $url;
	$insert['target'] = $target;
	$insert['reg_date'] = time();
	
	if ($idx) {
		$this->db()->update($this->table->item,$insert)->where('idx',$idx)->execute();
	} else {
		$this->db()->insert($this->table->item,$insert)->execute();
	}
	
	$results->success = true;
} else {
	$results->success = false;
	$results->errors = $errors;
}
?>