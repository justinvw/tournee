<?php
class TourneeTour extends TourneeAppModel {
	var $name = 'TourneeTour';
	var $useTable = 'tournee_tours';
	
	var $hasMany = array(
		'TourneeEvent' => array(
			'classname' => 'Trounee.TourEvent',
			'dependent' => true
		)
	);
	
	var $hasAndBelongsToMany = array(
		'Node' => array(
			'className' => 'Node',
			'joinTable' => 'tournee_nodes_tours',
			'foreignKey' => 'tournee_tour_id',
			'associationForeignKey' => 'node_id',
		)
	);
	
	var $validate = array(
		'title' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'allowEmpty' => false,
			'message' => 'Please enter a name for this location.'
		),
		'slug' => array(
			'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'This slug has already been taken.',
            ),
            'minLength' => array(
                'rule' => array('minLength', 1),
                'message' => 'Slug cannot be empty.',
            ),
		)
	);
}
?>