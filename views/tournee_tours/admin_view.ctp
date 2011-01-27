<?php echo $html->css('/tournee/css/admin.css', 'stylesheet', array('inline' => false)); ?>
<div id="tournee_tours" class="tournee index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('Add event', true), array('controller' => 'tournee_events', 'action' => 'add', $tour['TourneeTour']['id'])); ?>
			<li><?php echo $html->link(__('Edit tour', true), array('action' => 'edit', $tour['TourneeTour']['id'])); ?></li>
		</ul>
	</div>
	
	<div class="info ui-corner-all">
		<div class="grid_4">
			<?php echo $html->image($tour['TourneeTour']['image_path'], array('width' => '200px')); ?>
			<p><?php printf(__('Photo by: %s', true), $tour['TourneeTour']['image_creator']); ?></p>
		</div>
		<div class="grid_11">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><?php __('Title'); ?></td>
					<td><?php echo $tour['TourneeTour']['title']; ?></td>
				</tr>
				<tr>
					<td><?php __('Slug'); ?></td>
					<td><?php echo $tour['TourneeTour']['slug']; ?></td>
				</tr>
				<tr>
					<td><?php __('Alternative title'); ?>
					<td><?php echo $tour['TourneeTour']['alternative_title']; ?></td>
				</tr>
				<tr>
					<td><?php __('Description'); ?></td>
					<td><?php echo $tour['TourneeTour']['description']; ?></td>
				</tr>
				<tr>
					<td><?php __('Starts'); ?></td>
					<td><?php echo $tour['TourneeTour']['start_date']; ?></td>
				</tr>
				<tr>
					<td><?php __('Ends'); ?></td>
					<td><?php echo $tour['TourneeTour']['end_date']; ?></td>
				</tr>
				<tr>
					<td><?php __('Updated'); ?></td>
					<td><?php echo $tour['TourneeTour']['updated']; ?></td>
				</tr>
				<tr>
					<td><?php __('Created'); ?></td>
					<td><?php echo $tour['TourneeTour']['created']; ?></td>
				</tr>
			</table>
		</div>
	</div>
	
	<table cellpadding="0" cellspacing="0">
		<?php
			$tableHeaders = $html->tableHeaders(array(
				'ID',
				'Location',
				'Starts',
				'Ends',
				'Updated',
				'Created',
				//$paginator->sort('created'),
				__('Actions', true),
			));
			echo $tableHeaders;
			
			$rows = array();
			foreach($tour['TourneeEvent'] AS $event){
				$actions = $html->link(__('Edit', true), array('controller' => 'tournee_events', 'action' => 'edit', $event['id']));
				$actions .= ' ' . $html->link(__('Delete', true), array(
					'controller' => 'tournee_events',
					'action' => 'delete',
					$event['id']
				), null, __('Are you sure?', true));
			
				$rows[] = array(
					$event['id'],
					$event['TourneeLocation']['name'].' ('.$event['TourneeLocation']['address_city'].')',
					$event['start_datetime'],
					$event['end_datetime'],
					$event['updated'],
					$event['created'],
					$actions
				);
			}
			
			echo $html->tableCells($rows);
			echo $tableHeaders;
		?>
	</table>
</div>