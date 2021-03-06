<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$("#TourneeTourTitle").slug({
				slug: 'slug',
				hide: false
		});
	});
</script>
<div id="tournee_tours" class="tournee form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $form->create('TourneeTour', array('action' => 'add', 'enctype' => 'multipart/form-data')); ?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#tour-main"><span><?php __('Tour information'); ?></span></a></li>
				<li><a href="#tour-reviews"><span><?php __('Reviews'); ?></span></a></li>
			</ul>
			
			<div id="tour-main">
				<?php
					echo $form->input('title');
					echo $form->input('slug', array('class' => 'slug'));
					echo $form->input('alternative_title');
					echo $form->input('description');
					echo $form->input('start_date');
					echo $form->input('end_date');
					echo $form->input('file', array('label' => __('Picture/poster', true), 'type' => 'file'));
					echo $form->input('image_description');
					echo $form->input('image_creator');
					echo $form->input('show_on_index', array(
					    'label' => __('Show on tour index', true),
					    'checked' => 'checked'
					));
					echo $form->input('status', array(
						'label' => __('Published', true),
						'checked' => 'checked',
					));
				?>
			</div>
			
			<div id="tour-reviews">
				<?php echo $form->input('Node', array('label' => __('Reviews', true), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $reviews)); ?>
			</div>
		</div>
	</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>