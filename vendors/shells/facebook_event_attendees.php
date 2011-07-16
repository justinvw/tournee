<?php
    class FacebookEventAttendeesShell extends Shell {
        var $uses = array('Tournee.TourneeEvent', 'Tournee.TourneeEventAttendee');
        
        function main(){
            $events = $this->TourneeEvent->find('all', array(
                'conditions' => array(
                    'TourneeEvent.facebook_event_id !=' => '0',
                    
                )
            ));
            
            App::import('Vendor', 'Tournee.facebook/src/facebook');
            $facebook = new Facebook(array(
    			'appId' => Configure::read('Tournee.facebook_app_id'),
    			'secret' => Configure::read('Tournee.facebook_app_secret'),
    		));
    		
    		foreach($events as $event){
    		    $attendees = $facebook->api('/'.$event['TourneeEvent']['facebook_event_id'].'/attending', 'GET');
    		    
    		    $this->TourneeEventAttendee->deleteAll(array(
    		        'tournee_event_id' => $event['TourneeEvent']['id']
    		    ));
    		    
    		    foreach($attendees['data'] as $attendee){    		        
    		        $this->TourneeEventAttendee->create();
    		        $this->TourneeEventAttendee->set(array(
    		            'tournee_event_id' => $event['TourneeEvent']['id'],
    		            'facebook_user_id' => $attendee['id'],
    		            'name' => $attendee['name']
    		        ));
    		        $this->TourneeEventAttendee->save();
    		    }
    		}    		
        }
    }
?>