<?php
App::import('Lib', 'Tournee.FB');
class TourneeEventsController extends TourneeAppController {
	var $uses = array('Tournee.TourneeEvent');

	function admin_index(){
		$this->set('title_for_layout', __('Events', true));
		$events = $this->paginate('TourneeEvent');
		
		if(Configure::read('Tournee.facebook_intergration') == 'enabled'){
			App::import('Vendor', 'Tournee.facebook/src/facebook');
			$facebook = new Facebook(array(
				'appId' => Configure::read('Tournee.facebook_app_id'),
				'secret' => Configure::read('Tournee.facebook_app_secret'),
				'cookie' => true
			));

			$session = $facebook->getSession();
			if($session){
				try {
				 	$fb_user = $facebook->api('/me');
				}
				catch(FacebookApiException $e){
					error_log($e);
				}
				
				if(isset($fb_user)){
					$this->set(compact('fb_user'));
				}
			}
		}
		
		$this->set(compact('events'));
	}
	
	function admin_add($tour_id = null){
		$this->set('title_for_layout', __('Add an event', true));
		
		if(!empty($this->data)){
			$this->TourneeEvent->create();
			if($this->TourneeEvent->save($this->data)){
				if(Configure::read('Tournee.facebook_intergration') == 'enabled'){
					$this->data['TourneeTour'] = $this->TourneeEvent->TourneeTour->findById($this->data['TourneeEvent']['tournee_tour_id']);
					$this->data['TourneeLocation'] = $this->TourneeEvent->TourneeLocation->findById($this->data['TourneeEvent']['tournee_location_id']);
					$fb_event = $this->__post_facebook_event($this->data);
					if(array_key_exists('id', $fb_event)){
						$this->TourneeEvent->read(null, $this->TourneeEvent->id);
						$this->TourneeEvent->set(array('facebook_event_id' => $fb_event['id']));
						$this->TourneeEvent->save();
						$flash_message = array(
							'message' => __('Event saved and posted to Facebook', true),
							'class' => 'success'
						);
					}
					else {
						$flash_message = array(
							'message' => __('Event saved, but there was a problem posting it to Facebook', true),
							'class' => 'error'
						);
					}
				}
				else {
					$flash_message = array(
						'message' => __('Event saved!', true),
						'class' => 'success'
					);
				}
				
				$this->Session->setFlash($flash_message['message'], 'default', array('class' => $flash_message['class']));
				$this->redirect(array('controller' => 'tournee_events', 'action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('The event could not be saved. Please, try again', true)), 'default', array('class' => 'error'));
			}
		}
		if(Configure::read('Tournee.facebook_intergration') == 'enabled'){
			$this->__connect_to_facebook();
		}

		$tours = $this->TourneeEvent->TourneeTour->find('list');
		$locations = $this->TourneeEvent->TourneeLocation->find('list');
		
		$this->set(compact('tours', 'locations', 'tour_id'));
	}
	
	function admin_edit($id = null){
		if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$event = $this->TourneeEvent->findById($id);
		if(!$event) {
			$this->Session->setFlash(__('The event does not exist.', true));
            $this->redirect(array('action' => 'index'));
		}
		
		$this->set('title_for_layout', sprintf(__('Edit event', true)));
		
		if(!empty($this->data)){
			$this->TourneeEvent->id = $id;
			if($this->TourneeEvent->save($this->data)){
				if(Configure::read('Tournee.facebook_intergration') == 'enabled'){
					$this->data['TourneeTour'] = $this->TourneeEvent->TourneeTour->findById($this->data['TourneeEvent']['tournee_tour_id']);
					$this->data['TourneeLocation'] = $this->TourneeEvent->TourneeLocation->findById($this->data['TourneeEvent']['tournee_location_id']);
					
					if($event['TourneeEvent']['facebook_event_id']){
						$fb_event = $this->__update_facebook_event($event['TourneeEvent']['facebook_event_id'], $this->data);
						if($fb_event == 1){
							$flash_message = array(
								'message' => __('Event updated and posted to Facebook', true),
								'class' => 'success'
							);
						}
						else {
							$flash_message = array(
								'message' => __('Event updated, but there was a problem posting it to Facebook', true),
								'class' => 'error'
							);
						}
					}
					else {
						$fb_event = $this->__post_facebook_event($this->data);
						if(array_key_exists('id', $fb_event)){
							$this->TourneeEvent->read(null, $this->TourneeEvent->id);
							$this->TourneeEvent->set(array('facebook_event_id' => $fb_event['id']));
							$this->TourneeEvent->save();
							$flash_message = array(
								'message' => __('Event updated and posted to Facebook', true),
								'class' => 'success'
							);
						}
						else {
							$flash_message = array(
								'message' => __('Event updated, but there was a problem posting it to Facebook', true),
								'class' => 'error'
							);
						}
					}
				}
				else {
					$flash_message = array(
						'message' => __('The event has been saved', true),
						'class' => 'success'
					);
				}
				
				$this->Session->setFlash($flash_message['message'], 'default', array('class' => $flash_message['class']));
                $this->redirect(array('controller' => 'tournee_events', 'action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('The event could not be saved. Please, try again.', true)));
			}
		}
		
		$this->data = $event;
		
		if(Configure::read('Tournee.facebook_intergration') == 'enabled'){
			$this->__connect_to_facebook();
		}
			
		$tours = $this->TourneeEvent->TourneeTour->find('list');
		$locations = $this->TourneeEvent->TourneeLocation->find('list');
		
		$this->set(compact('tours', 'locations'));		
	}
	
	function admin_delete($id = null){
		if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$event = $this->TourneeEvent->findById($id);
		
		if($this->TourneeEvent->delete($id)){
			if($event['TourneeEvent']['facebook_event_id'] != 0){
				$fb_event = $this->__delete_facebook_event($event['TourneeEvent']['facebook_event_id']);
				if($fb_event == 1){
					$flash_message = array(
						'message' => __('Event deleted from database and Facebook', true),
						'class' => 'success'
					);
				}
				else {
					$flash_message = array(
						'message' => __('Event deleted from database, but there was a problem deleting it from Facebook', true),
						'class' => 'error'
					);
				}
			}
			else {
				$flash_message = array(
					'message' => __('Event deleted', true),
					'class' => 'success'
				);
			}
			$this->Session->setFlash($flash_message['message'], 'default', array('class' => $flash_message['class']));
            $this->redirect(array('action' => 'index'));
		}
		else {
			$this->Session->setFlash(__('Failed to delete the event', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
		}
	}
	
	function admin_facebook_logout(){
		App::import('Vendor', 'Tournee.facebook/src/facebook');
		$facebook = new Facebook(array(
			'appId' => Configure::read('Tournee.facebook_app_id'),
			'secret' => Configure::read('Tournee.facebook_app_secret'),
			'cookie' => true
		));
		
		$session = $facebook->getSession();

		$logout_url = $facebook->getLogoutUrl(array('next' => Router::url(array('plugin' => 0, 'controller' => 'users', 'action' => 'logout'), true)));
		$this->redirect($logout_url);
	}
	
	function __connect_to_facebook(){
		App::import('Vendor', 'Tournee.facebook/src/facebook');
		$facebook = new Facebook(array(
			'appId' => Configure::read('Tournee.facebook_app_id'),
			'secret' => Configure::read('Tournee.facebook_app_secret'),
			'cookie' => true
		));
		
		$session = $facebook->getSession();
		$login_url = $facebook->getLoginUrl(array(
			'req_perms' => 'create_event, rsvp_event'
		));
		
		if(!empty($session)){
			$this->Session->write('uid', $session['uid']);
		}
		else {
			$this->redirect($login_url);
		}
	}
	
	function __delete_facebook_event($facebook_event_id = null){
		App::import('Vendor', 'Tournee.facebook/src/facebook');
		$facebook = new Facebook(array(
			'appId' => Configure::read('Tournee.facebook_app_id'),
			'secret' => Configure::read('Tournee.facebook_app_secret'),
			'cookie' => true
		));

		$session = $facebook->getSession();
		if(!empty($session)){
			return $facebook->api($facebook_event_id, 'DELETE');
		}
		else {
			return false;
		}
	}
	
	function __update_facebook_event($facebook_event_id = null, $event_data = null){
		App::import('Vendor', 'Tournee.facebook/src/facebook');
		$facebook = new Facebook(array(
			'appId' => Configure::read('Tournee.facebook_app_id'),
			'secret' => Configure::read('Tournee.facebook_app_secret'),
			'cookie' => true
		));

		$session = $facebook->getSession();

		if(!empty($session)){
			$this->Session->write('uid', $session['uid']);
			$fb_event_array = array(
				'name' => $event_data['TourneeTour']['TourneeTour']['title'],
				'description' => $event_data['TourneeTour']['TourneeTour']['description'],
				'start_time' => $event_data['TourneeEvent']['start_datetime']['year'].$event_data['TourneeEvent']['start_datetime']['month'].$event_data['TourneeEvent']['start_datetime']['day'].'T'.$event_data['TourneeEvent']['start_datetime']['hour'].$event_data['TourneeEvent']['start_datetime']['min'],
				'location' => $event_data['TourneeLocation']['TourneeLocation']['name'],
				'street' => $event_data['TourneeLocation']['TourneeLocation']['address_street'],
				'city' => $event_data['TourneeLocation']['TourneeLocation']['address_city'],
				'zip' => $event_data['TourneeLocation']['TourneeLocation']['address_zip'],
				'country' => $event_data['TourneeLocation']['TourneeLocation']['address_country'],
				'privacy' => 'OPEN'
			);
			return $facebook->api($facebook_event_id, 'POST', $fb_event_array);
		}
		else {
			return false;
		}
	}
	
	function __post_facebook_event($event_data = null){
		App::import('Vendor', 'Tournee.facebook/src/facebook');
		$facebook = new Facebook(array(
			'appId' => Configure::read('Tournee.facebook_app_id'),
			'secret' => Configure::read('Tournee.facebook_app_secret'),
			'cookie' => true
		));

		$session = $facebook->getSession();

		if(!empty($session)){
			$this->Session->write('uid', $session['uid']);
			$fb_event_array = array(
				'name' => $event_data['TourneeTour']['TourneeTour']['title'],
				'description' => $event_data['TourneeTour']['TourneeTour']['description'],
				'start_time' => $event_data['TourneeEvent']['start_datetime']['year'].$event_data['TourneeEvent']['start_datetime']['month'].$event_data['TourneeEvent']['start_datetime']['day'].'T'.$event_data['TourneeEvent']['start_datetime']['hour'].$event_data['TourneeEvent']['start_datetime']['min'],
				'location' => $event_data['TourneeLocation']['TourneeLocation']['name'],
				'street' => $event_data['TourneeLocation']['TourneeLocation']['address_street'],
				'city' => $event_data['TourneeLocation']['TourneeLocation']['address_city'],
				'zip' => $event_data['TourneeLocation']['TourneeLocation']['address_zip'],
				'country' => $event_data['TourneeLocation']['TourneeLocation']['address_country'],
				'privacy' => 'OPEN'
			);
			return $facebook->api('/me/events', 'POST', $fb_event_array);
		}
		else {
			return false;
		}
	}
}
?>