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
			    if(Configure::read('Tournee.facebook_intergration') == 'enabled' || Configure::read('Tournee.comedyapp_intergration')){
			        $flash_message = array();
			        			        
			        $this->data['TourneeTour'] = $this->TourneeEvent->TourneeTour->findById($this->data['TourneeEvent']['tournee_tour_id']);
					$this->data['TourneeLocation'] = $this->TourneeEvent->TourneeLocation->findById($this->data['TourneeEvent']['tournee_location_id']);
					
					if(Configure::read('Tournee.comedyapp_intergration') == 'enabled'){
					    $comedyapp_id = $this->__add_comedy_app_event($this->data);
					    $this->TourneeEvent->read(null, $this->TourneeEvent->id);
						$this->TourneeEvent->set(array('comedyapp_id' => $comedyapp_id));
						$this->TourneeEvent->save();
						
						$flash_message[] = array(
						    'code' => 'success',
						    'message' => 'Posted to ComedyApp.'  
						);
					}
					
					if(Configure::read('Tournee.facebook_intergration') == 'enabled'){
    					$fb_event = $this->__post_facebook_event($this->data);
    					if(array_key_exists('id', $fb_event)){
    						$this->TourneeEvent->read(null, $this->TourneeEvent->id);
    						$this->TourneeEvent->set(array('facebook_event_id' => $fb_event['id']));
    						$this->TourneeEvent->save();
    						    						
    						$flash_message[] = array(
    						    'code' => 'success',
    						    'message' => 'Posted to Facebook.'  
    						);
    					}
    					else {
    						$flash_message[] = array(
    						    'code' => 'error',
    						    'message' => 'Failed posting to Facebook.'
    						);
    					}
    				}
			    }
				
				$final_flash_message = array('message' => 'The event is saved. ', 'code' => 'success');
				foreach($flash_message as $message){
				    $final_flash_message['message'] .= ' '.$message['message'];
				    
				    if($message['code'] == 'error'){
				        $final_flash_message['code'] = 'error';
				    }
				}
				
				$this->Session->setFlash($final_flash_message['message'], 'default', array('class' => $final_flash_message['code']));
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
		$locations_available = $this->TourneeEvent->TourneeLocation->find('all', array(
			'fields' => array('id', 'name', 'address_country', 'address_city'),
			'order' => array('address_country', 'address_city', 'name')
		));
		
		$locations = array();
		foreach($locations_available as $location){
			$locations[$location['TourneeLocation']['id']] = $location['TourneeLocation']['name'].' ('.$location['TourneeLocation']['address_city'].')';
		}
		
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
			    if(Configure::read('Tournee.facebook_intergration') == 'enabled' || Configure::read('Tournee.comedyapp_intergration')){
			        $flash_message = array();
			        
			        $this->data['TourneeTour'] = $this->TourneeEvent->TourneeTour->findById($this->data['TourneeEvent']['tournee_tour_id']);
					$this->data['TourneeLocation'] = $this->TourneeEvent->TourneeLocation->findById($this->data['TourneeEvent']['tournee_location_id']);
					
					if(Configure::read('Tournee.comedyapp_intergration') == 'enabled'){
					    if($event['TourneeEvent']['comedyapp_id']){
					        $this->__edit_comedy_app_event($this->data, $event['TourneeEvent']['comedyapp_id']);
					        $flash_message[] = array(
    						    'code' => 'success',
    						    'message' => 'Updated ComedyApp entry.'
    						);
					    }
					    else {
					        $comedyapp_id = $this->__add_comedy_app_event($this->data);
    					    $this->TourneeEvent->read(null, $this->TourneeEvent->id);
    						$this->TourneeEvent->set(array('comedyapp_id' => $comedyapp_id));
    						$this->TourneeEvent->save();

    						$flash_message[] = array(
    						    'code' => 'success',
    						    'message' => 'Posted to ComedyApp.'  
    						);
					    }
					}
					
			        if(Configure::read('Tournee.facebook_intergration') == 'enabled'){
			            if($event['TourneeEvent']['facebook_event_id']){
			                $fb_event = $this->__update_facebook_event($event['TourneeEvent']['facebook_event_id'], $this->data);
    						if($fb_event == 1){
    						    $flash_message[] = array(
        						    'code' => 'success',
        						    'message' => 'Updated Facebook entry.'
        						);
    						}
    						else {
    							$flash_message[] = array(
        						    'code' => 'error',
        						    'message' => 'Failed posting to Facebook.'
        						);
    						}
						}
						else {
						    $fb_event = $this->__post_facebook_event($this->data);
    						if(array_key_exists('id', $fb_event)){
    							$this->TourneeEvent->read(null, $this->TourneeEvent->id);
    							$this->TourneeEvent->set(array('facebook_event_id' => $fb_event['id']));
    							$this->TourneeEvent->save();
    							$flash_message[] = array(
        						    'code' => 'success',
        						    'message' => 'Posted to Facebook.'  
        						);
    						}
    						else {
    							$flash_message[] = array(
        						    'code' => 'error',
        						    'message' => 'Failed posting to Facebook.'
        						);
    						}
						}
			        }
			    }
				
				$final_flash_message = array('message' => 'The event is saved. ', 'code' => 'success');
				foreach($flash_message as $message){
				    $final_flash_message['message'] .= ' '.$message['message'];
				    
				    if($message['code'] == 'error'){
				        $final_flash_message['code'] = 'error';
				    }
				}
				
				$this->Session->setFlash($final_flash_message['message'], 'default', array('class' => $final_flash_message['code']));
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
		$this->TourneeEvent->TourneeLocation->recursive = 0;
		$locations_available = $this->TourneeEvent->TourneeLocation->find('all', array(
			'fields' => array('id', 'name', 'address_country', 'address_city'),
			'order' => array('address_country', 'address_city', 'name')
		));
		
		$locations = array();
		foreach($locations_available as $location){
			$locations[$location['TourneeLocation']['id']] = $location['TourneeLocation']['name'].' ('.$location['TourneeLocation']['address_city'].')';
		}
		
		$this->set(compact('tours', 'locations'));		
	}
	
	function admin_delete($id = null){
		if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$event = $this->TourneeEvent->findById($id);
		
		if($this->TourneeEvent->delete($id)){
		    if($event['TourneeEvent']['comedyapp_id']){
		        $this->__delete_comedy_app_event($event['TourneeEvent']['comedyapp_id']);
		    }
		    
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
			'req_perms' => 'create_event, rsvp_event, manage_pages'
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
			'cookie' => true,
			'fileUpload' => true
		));

		$session = $facebook->getSession();
        
        if(strlen($event_data['TourneeTour']['TourneeTour']['title']) > 40){
		    App::import('Helper', 'Text');
    	    $texthelper = new TextHelper();
    	    
		    $event_data['TourneeTour']['TourneeTour']['title'] = $texthelper->truncate($event_data['TourneeTour']['TourneeTour']['title'], 40, array('ending' => '...', 'exact' => true, 'html' => false));
		}
		
		if($event_data['TourneeLocation']['TourneeLocation']['address_country'] =='nl'){
		    $event_data['TourneeLocation']['TourneeLocation']['address_country'] = 'Netherlands';
		}
        
		if(!empty($session)){
			$this->Session->write('uid', $session['uid']);
			$fb_event_array = array(
				'name' => $event_data['TourneeTour']['TourneeTour']['title'],
				'description' => strip_tags($event_data['TourneeTour']['TourneeTour']['description']),
				'start_time' => $event_data['TourneeEvent']['start_datetime']['year'].$event_data['TourneeEvent']['start_datetime']['month'].$event_data['TourneeEvent']['start_datetime']['day'].'T'.$event_data['TourneeEvent']['start_datetime']['hour'].$event_data['TourneeEvent']['start_datetime']['min'],
				'end_time' => $event_data['TourneeEvent']['end_datetime']['year'].$event_data['TourneeEvent']['end_datetime']['month'].$event_data['TourneeEvent']['end_datetime']['day'].'T'.$event_data['TourneeEvent']['end_datetime']['hour'].$event_data['TourneeEvent']['end_datetime']['min'],
				'location' => $event_data['TourneeLocation']['TourneeLocation']['name'],
				'street' => $event_data['TourneeLocation']['TourneeLocation']['address_street'].' '.$event_data['TourneeLocation']['TourneeLocation']['address_number'],
				'city' => $event_data['TourneeLocation']['TourneeLocation']['address_city'],
				'zip' => $event_data['TourneeLocation']['TourneeLocation']['address_zip'],
				'country' => $event_data['TourneeLocation']['TourneeLocation']['address_country'],
				'privacy' => 'OPEN',
			);
            
            if(!empty($event_data['TourneeTour']['TourneeTour']['image_path'])){
                $tour_img = APP.'webroot'.DS.'uploads'.DS.$event_data['TourneeTour']['TourneeTour']['image_path'];
                $fb_event_array[basename($tour_img)] = '@'.$tour_img;
            }
            
            $facebook_page_id = Configure::read('Tournee.facebook_page_id');
            if(!empty($facebook_page_id)){
                $fb_event_array['page_id'] = Configure::read('Tournee.facebook_page_id');
            }
            
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
			'cookie' => true,
			'fileUpload' => true
		));
        
		$session = $facebook->getSession();

		if(!empty($session)){
			$this->Session->write('uid', $session['uid']);
			
			if(strlen($event_data['TourneeTour']['TourneeTour']['title']) > 40){
			    App::import('Helper', 'Text');
        	    $texthelper = new TextHelper();
        	    
			    $event_data['TourneeTour']['TourneeTour']['title'] = $texthelper->truncate($event_data['TourneeTour']['TourneeTour']['title'], 40, array('ending' => '...', 'exact' => true, 'html' => false));
			}
			
			if($event_data['TourneeLocation']['TourneeLocation']['address_country'] =='nl'){
    		    $event_data['TourneeLocation']['TourneeLocation']['address_country'] = 'Netherlands';
    		}
			
			$fb_event_array = array(
				'name' => $event_data['TourneeTour']['TourneeTour']['title'],
				'description' => strip_tags($event_data['TourneeTour']['TourneeTour']['description']),
				'start_time' => $event_data['TourneeEvent']['start_datetime']['year'].$event_data['TourneeEvent']['start_datetime']['month'].$event_data['TourneeEvent']['start_datetime']['day'].'T'.$event_data['TourneeEvent']['start_datetime']['hour'].$event_data['TourneeEvent']['start_datetime']['min'],
				'end_time' => $event_data['TourneeEvent']['end_datetime']['year'].$event_data['TourneeEvent']['end_datetime']['month'].$event_data['TourneeEvent']['end_datetime']['day'].'T'.$event_data['TourneeEvent']['end_datetime']['hour'].$event_data['TourneeEvent']['end_datetime']['min'],
				'location' => $event_data['TourneeLocation']['TourneeLocation']['name'],
				'street' => $event_data['TourneeLocation']['TourneeLocation']['address_street'].' '.$event_data['TourneeLocation']['TourneeLocation']['address_number'],
				'city' => $event_data['TourneeLocation']['TourneeLocation']['address_city'],
				'zip' => $event_data['TourneeLocation']['TourneeLocation']['address_zip'],
				'country' => $event_data['TourneeLocation']['TourneeLocation']['address_country'],
				'privacy' => 'OPEN',
			);
			
			if(!empty($event_data['TourneeTour']['TourneeTour']['image_path'])){
                $tour_img = APP.'webroot'.DS.'uploads'.DS.$event_data['TourneeTour']['TourneeTour']['image_path'];
                $fb_event_array[basename($tour_img)] = '@'.$tour_img;
            }
			
			$facebook_page_id = Configure::read('Tournee.facebook_page_id');
			if(!empty($facebook_page_id)){
                $fb_event_array['page_id'] = $facebook_page_id;
            }
			
			return $facebook->api('/me/events', 'POST', $fb_event_array);
		}
		else {
			return false;
		}
	}
	
	function __comedy_app_request($context){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, Configure::read('Tournee.comedyapp_xml_rpc_address'));
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $context);
        
        $response = curl_exec($curl);
        return $response;
	}
	
	function __add_comedy_app_event($event_data){
	    App::import('Helper', 'Text');
	    $texthelper = new TextHelper();
	    
	    $request = xmlrpc_encode_request('comedy.addAgenda',
	        array(
	            Configure::read('Tournee.comedyapp_username'),
	            Configure::read('Tournee.comedyapp_password'),
	            $event_data['TourneeTour']['TourneeTour']['title'],
	            $event_data['TourneeTour']['TourneeTour']['description'],
	            $texthelper->truncate(strip_tags($event_data['TourneeTour']['TourneeTour']['description']), 75, array('ending' => '...', 'exact' => true, 'html' => false)),
	            $event_data['TourneeLocation']['TourneeLocation']['name'],
	            $event_data['TourneeLocation']['TourneeLocation']['address_street'].' '.$event_data['TourneeLocation']['TourneeLocation']['address_number'],
	            $event_data['TourneeLocation']['TourneeLocation']['address_city'],
	            $event_data['TourneeLocation']['TourneeLocation']['website'],
	            array(
	                array(
	                    'date' => $event_data['TourneeEvent']['start_datetime']['day'].'-'.$event_data['TourneeEvent']['start_datetime']['month'].'-'.$event_data['TourneeEvent']['start_datetime']['year'],
	                    'time' => $event_data['TourneeEvent']['start_datetime']['hour'].':'.$event_data['TourneeEvent']['start_datetime']['min']
	                ),
	                #array(
	                #    'date' => $event_data['TourneeEvent']['end_datetime']['day'].'-'.$event_data['TourneeEvent']['end_datetime']['month'].'-'.$event_data['TourneeEvent']['end_datetime']['year'],
	                #    'time' => $event_data['TourneeEvent']['end_datetime']['hour'].':'.$event_data['TourneeEvent']['end_datetime']['min']
	                #),
	            )			
	        ), array('encoding' => 'utf-8')
	    );
	    
	    return xmlrpc_decode($this->__comedy_app_request($request));
	}
	
	function __edit_comedy_app_event($event_data, $comedyapp_id){
	    App::import('Helper', 'Text');
	    $texthelper = new TextHelper();
	    
	    $request = xmlrpc_encode_request('comedy.editAgenda',
	        array(
	            Configure::read('Tournee.comedyapp_username'),
	            Configure::read('Tournee.comedyapp_password'),
	            $comedyapp_id,
	            $event_data['TourneeTour']['TourneeTour']['title'],
	            $event_data['TourneeTour']['TourneeTour']['description'],
	            $texthelper->truncate(strip_tags($event_data['TourneeTour']['TourneeTour']['description']), 75, array('ending' => '...', 'exact' => true, 'html' => false)),
	            $event_data['TourneeLocation']['TourneeLocation']['name'],
	            $event_data['TourneeLocation']['TourneeLocation']['address_street'].' '.$event_data['TourneeLocation']['TourneeLocation']['address_number'],
	            $event_data['TourneeLocation']['TourneeLocation']['address_city'],
	            $event_data['TourneeLocation']['TourneeLocation']['website'],
	            array(
	                array(
	                    'date' => $event_data['TourneeEvent']['start_datetime']['day'].'-'.$event_data['TourneeEvent']['start_datetime']['month'].'-'.$event_data['TourneeEvent']['start_datetime']['year'],
	                    'time' => $event_data['TourneeEvent']['start_datetime']['hour'].':'.$event_data['TourneeEvent']['start_datetime']['min']
	                ),
	                #array(
	                #    'date' => $event_data['TourneeEvent']['end_datetime']['day'].'-'.$event_data['TourneeEvent']['end_datetime']['month'].'-'.$event_data['TourneeEvent']['end_datetime']['year'],
	                #    'time' => $event_data['TourneeEvent']['end_datetime']['hour'].':'.$event_data['TourneeEvent']['end_datetime']['min']
	                #),
	            )			
	        ), array('encoding' => 'utf-8')
	    );
	    
	    return xmlrpc_decode($this->__comedy_app_request($request));
	}
	
	function __delete_comedy_app_event($comedyapp_id){
	    $request = xmlrpc_encode_request('comedy.remove_agenda',
	        array(
	            Configure::read('Tournee.comedyapp_username'),
	            Configure::read('Tournee.comedyapp_password'),
	            $comedyapp_id
	        )
	    );
	    
	    return xmlrpc_decode($this->__comedy_app_request($request));
	}
}
?>