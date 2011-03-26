<div id="tournee_locations" class="tournee index">
	<h2><?php echo $title_for_layout; ?></h2>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('Add a location', true), array('action' => 'add')); ?></li>
		</ul>
	</div>

	<table cellpadding="0" cellspacing="0">
		<?php
			$tableHeaders = $html->tableHeaders(array(
				$paginator->sort('id'),
				$paginator->sort('name'),
				$paginator->sort('Address', 'address_street'),
				$paginator->sort('City', 'address_city'),
				$paginator->sort('updated'),
				$paginator->sort('created'),
				//$paginator->sort('created'),
				__('Actions', true),
			));
			echo $tableHeaders;
			
			$rows = array();
			foreach($locations AS $location){
				$actions = $html->link(__('Edit', true), array('action' => 'edit', $location['TourneeLocation']['id']));
				$actions .= ' ' . $html->link(__('Delete', true), array(
					'action' => 'delete',
					$location['TourneeLocation']['id']
				), null, __('Are you sure you want delete this location and all it\'s associated events?', true));
			
				$rows[] = array(
					$location['TourneeLocation']['id'],
					$location['TourneeLocation']['name'],
					$location['TourneeLocation']['address_street'].' '.$location['TourneeLocation']['address_number'],
					$location['TourneeLocation']['address_city'],
					$location['TourneeLocation']['updated'],
					$location['TourneeLocation']['created'],
					$actions
				);
			}
			
			echo $html->tableCells($rows);
			echo $tableHeaders;
		?>
	</table>
</div>
<div class="paging"><?php echo $paginator->numbers(); ?></div>
<div class="counter"><?php echo $paginator->counter(array('format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true))); ?></div>