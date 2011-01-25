<div id="tournee_locations" class="tournee form">
	<h2><?php echo $title_for_layout; ?></h2>
	<?php echo $form->create('TourneeLocation', array('url' => array('action' => 'add'))); ?>
	<fieldset>
	<?php
		echo $form->input('name');
		echo $form->input('info');
		echo $form->input('website');
		echo $form->input('email');
		echo $form->input('phone');
		echo $form->input('fax');
		echo $form->input('address_street', array(
			'label' => __('Street', true)
		));
		echo $form->input('address_number', array(
			'label' => __('Number', true)
		));
		echo $form->input('address_zip', array(
			'label' => __('ZIP', true)
		));
		echo $form->input('address_city', array(
			'label' => __('City', true)
		));
		echo $form->input('address_country', array(
			'label' => __('Country', true),
			'type' => 'select',
			'options' => $countries->getCountries(),
			'selected' => 'nl',
			'empty' => false
		));
	?>
	</fieldset>
	<?php echo $form->end('Submit'); ?>
</div>