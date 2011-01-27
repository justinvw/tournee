<?php
class TourneeLocation extends TourneeAppModel {
	var $name = 'TourneeLocation';
	var $useTable = 'tournee_locations';
	
	var $hasMany = array(
		'TourneeEvent' => array(
			'classname' => 'Trounee.TourEvent',
			'dependent' => true
		)
	);
	
	var $validate = array(
		'name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a name for this location.'
		),
		'website' => array(
			'rule' => 'url',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Please enter a valid URL.'
		),
		'email' => array(
			'rule' => 'email',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Please enter a valid email address'
		),
		'phone' => array(
			'rule' => 'numeric',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Only numbers are allowed!'
		),
		'fax' => array(
			'rule' => 'numeric',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'Only numbers are allowed!'
		),
		'address_city' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a city.'
		),
		'address_country' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please select a country.'
		)
	);
}
?>