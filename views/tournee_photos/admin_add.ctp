<?php
	echo $form->create('TourneePhoto', array('url' => array('action' => 'add', $tour_id), 'enctype' => 'multipart/form-data'));
	echo $form->input('file', array('label' => __('Photo', true), 'type' => 'file')); 
	echo $form->input('image_description');
	echo $form->input('image_creator');
	echo $form->end('Submit');
?>