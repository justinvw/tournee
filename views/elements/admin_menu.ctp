<a href="#"><?php __('Tournee'); ?></a>
<ul>
	<li><?php echo $html->link(__('Locations', true), array('plugin' => 'tournee', 'controller' => 'tournee_locations', 'action' => 'index')); ?></li>
	<li><?php echo $html->link(__('Tours', true), array('plugin' => 'tournee', 'controller' => 'tournee_tours', 'action' => 'index')); ?></li>
	<li><?php echo $html->link(__('Events', true), array('plugin' => 'tournee', 'controller' => 'tournee_events', 'action' => 'index')); ?></li>
	<li><?php echo $html->link(__('Settings', true), '#'); ?></li>
</ul>