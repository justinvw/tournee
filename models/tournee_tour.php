<?php
class TourneeTour extends TourneeAppModel {
	var $name = 'TourneeTour';
	var $useTable = 'tournee_tours';
	
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