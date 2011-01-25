<div id="tournee_tours" class="tournee form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $form->create('TourneeTour', array('action' => 'edit', 'enctype' => 'multipart/form-data')); ?>
	<fieldset>
	<?php
		echo $form->input('title');
		echo $form->input('alternative_title');
		echo $form->input('description');
		echo $form->input('start_date');
		echo $form->input('end_date');
		echo $form->input('file', array('label' => __('Picture/poster', true), 'type' => 'file'));
	?>
	</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>