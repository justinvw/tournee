<?php echo $html->css('/tournee/css/admin.css', 'stylesheet', array('inline' => false)); ?>
<div id="tournee_events" class="tournee index">
	<h2>
		<?php 
			echo '<span>'.$title_for_layout.'</span>';
			if(isset($fb_user)){
				echo '<div id="facebook_logout">';
				printf(__('Logged in to facebook as %s', true), $fb_user['name']);
				echo '<br />'.$html->link('Logout', array('action' => 'facebook_logout')).'</div>';
			}
		?>
	</h2>
	
	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('Add an event', true), array('action' => 'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
		<?php
			$tableHeaders = $html->tableHeaders(array(
				$paginator->sort('id'),
				$paginator->sort('Tour', 'TourneeTour.title'),
				$paginator->sort('Location', 'tournee_location_id'),
				$paginator->sort('Starts', 'start_datetime'),
				$paginator->sort('Ends', 'end_datetime'),
				$paginator->sort('updated'),
				$paginator->sort('created'),
				//$paginator->sort('created'),
				__('Actions', true),
			));
			echo $tableHeaders;
			
			$rows = array();
			foreach($events AS $event){
				$actions = $html->link(__('Edit', true), array('action' => 'edit', $event['TourneeEvent']['id']));
				$actions .= ' ' . $html->link(__('Delete', true), array(
					'action' => 'delete',
					$event['TourneeEvent']['id']
				), null, __('Are you sure?', true));
			
				$rows[] = array(
					$event['TourneeEvent']['id'],
					$event['TourneeTour']['title'],
					$event['TourneeLocation']['name'].' ('.$event['TourneeLocation']['address_city'].')',
					$event['TourneeEvent']['start_datetime'],
					$event['TourneeEvent']['end_datetime'],
					$event['TourneeEvent']['updated'],
					$event['TourneeEvent']['created'],
					$actions
				);
			}
			
			echo $html->tableCells($rows);
			echo $tableHeaders;
		?>
	</table>
</div>