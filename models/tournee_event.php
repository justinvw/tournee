<?php
class TourneeEvent extends TourneeAppModel {
	var $name = 'TourneeEvent';
	var $useTable = 'tournee_events';
	
	var $belongsTo = array('Tournee.TourneeTour', 'Tournee.TourneeLocation');
	
	var $validate = array(
		'tournee_tour_id' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please select a tour.'
		),
		'tournee_location_id' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please select a location.'
		)
	);
}
?>