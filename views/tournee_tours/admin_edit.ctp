<?php echo $html->css('/tournee/css/admin.css', 'stylesheet', array('inline' => false)); ?>
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
	<?php echo $form->create('TourneeTour', array('action' => 'edit', 'enctype' => 'multipart/form-data')); ?>
	<fieldset>
		<div class="tabs">
			<ul>
				<li><a href="#tour-main"><span><?php __('Tour information'); ?></span></a></li>
				<li><a href="#tour-reviews"><span><?php __('Reviews'); ?></span></a></li>
				<li><a href="#tour-photos"><span><?php __('Photo\'s'); ?></span></a></li>
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
					echo $form->input('status', array('label' => __('Published', true)));
				?>
			</div>
			
			<div id="tour-reviews">
				<?php echo $form->input('Node', array('label' => __('Reviews', true), 'type' => 'select', 'multiple' => 'checkbox', 'options' => $reviews)); ?>
			</div>
			
			<div id="tour-photos">
				<div>
					<?php echo $html->link('Upload a photo', array('controller' => 'tournee_photos', 'action' => 'add', $this->data['TourneeTour']['id'])); ?>
					<ul>
					<?php foreach($this->data['TourneePhoto'] as $photo): ?>
						<li>
							<?php echo $html->image('/uploads/'.'small_'.$photo['image_path'], array('width' => '170px')); ?>
							<?php echo $html->link('Delete', array('controller' => 'tournee_photos', 'action' => 'delete', $photo['id'])); ?>
						</li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>
	</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>