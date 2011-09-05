<div id="tournee_events" class="tournee form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $form->create('TourneeEvent', array('action' => 'edit')); ?>
	<fieldset>
	<?php
		echo $form->input('tournee_tour_id', array('label' => __('Tour', true), 'type' => 'select', 'options' => $tours, 'empty' => true));
		echo $form->input('tournee_location_id', array('label' => __('Location', true), 'type' => 'select', 'options' => $locations, 'empty' => true));
		echo $form->input('title', array('label' => __('Title (additonal to the tour\'s title)', true)));
		echo $form->input('description', array('label' => __('Description (additonal to the tour\'s description)', true)));
		echo $form->input('start_datetime', array('timeFormat' => '24'));
		echo $form->input('end_datetime', array('timeFormat' => '24'));
		echo $form->input('status', array('label' => __('Published', true)));
	?>
	</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>