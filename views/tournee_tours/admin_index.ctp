<div id="tournee_tours" class="tournee index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('Add a tour', true), array('action' => 'add')); ?></li>
		</ul>
	</div>
	
	<table cellpadding="0" cellspacing="0">
		<?php
			$tableHeaders = $html->tableHeaders(array(
				$paginator->sort('id'),
				$paginator->sort('title'),
				$paginator->sort('Starts', 'start_date'),
				$paginator->sort('Ends', 'end_date'),
				$paginator->sort('updated'),
				$paginator->sort('created'),
				//$paginator->sort('created'),
				__('Actions', true),
			));
			echo $tableHeaders;
			
			$rows = array();
			foreach($tours AS $tour){
				$actions = $html->link(__('Edit', true), array('action' => 'edit', $tour['TourneeTour']['id']));
				$actions .= ' ' . $html->link(__('Delete', true), array(
					'action' => 'delete',
					$tour['TourneeTour']['id']
				), null, __('Are you sure you want delete this tour and all it\'s associated events?', true));
			
				$rows[] = array(
					$tour['TourneeTour']['id'],
					$html->link($tour['TourneeTour']['title'], array('action' => 'view', $tour['TourneeTour']['id'])),
					$tour['TourneeTour']['start_date'],
					$tour['TourneeTour']['end_date'],
					$tour['TourneeTour']['updated'],
					$tour['TourneeTour']['created'],
					$actions
				);
			}
			
			echo $html->tableCells($rows);
			echo $tableHeaders;
		?>
	</table>
</div>