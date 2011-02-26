<?php
class TourneeUpcommingComponent extends Object {
	public function startup(&$controller){
		$TourneeEvent = ClassRegistry::init('Tournee.TourneeEvent');
		$events = $TourneeEvent->find('all', array(
			'conditions' => array(
				'date(TourneeEvent.start_datetime) >=' => date('Y-m-d')
			),
			'order' => 'TourneeEvent.start_datetime DESC',
			'limit' => '0,10'
		));
		$controller->set('tournee_upcomming_events', $events);
	}
}
?>