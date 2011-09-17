<?php
class ComedyappUpdateShell extends Shell {
    var $uses = array('Tournee.TourneeEvent');
    
    function main(){
        $events_to_add = $this->TourneeEvent->find('all', array(
            'conditions' => array(
                'TourneeEvent.comedyapp_id' => 0
            )
        ));
        
        $events_to_update = $this->TourneeEvent->find('all', array(
            'conditions' => array(
                'TourneeEvent.comedyapp_id !=' => 0
            )
        ));
        
        print count($events_to_add)." events to add.\n";
        print count($events_to_update)." events to update.\n";
        
        foreach($events_to_add as $event){
            print "Adding event ".$event['TourneeEvent']['id']."\n";
            $comedyapp_id = $this->__add_comedy_app_event($event);
		    $this->TourneeEvent->read(null, $event['TourneeEvent']['id']);
		    $this->TourneeEvent->set(array('comedyapp_id' => $comedyapp_id));
		    $this->TourneeEvent->save();
        }
        
        foreach($events_to_update as $event){
            print "Updateing event ".$event['TourneeEvent']['id']."\n";
            $comedyapp_id = $this->__edit_comedy_app_event($event, $event['TourneeEvent']['comedyapp_id']);
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
	    App::import('Core', 'Helper');
	    App::import('Helper', 'Text');
	    $texthelper = new TextHelper();
	    
	    $request = xmlrpc_encode_request('comedy.addAgenda',
	        array(
	            Configure::read('Tournee.comedyapp_username'),
	            Configure::read('Tournee.comedyapp_password'),
	            $event_data['TourneeTour']['title'],
	            $event_data['TourneeTour']['description'],
	            $texthelper->truncate(strip_tags($event_data['TourneeTour']['description']), 75, array('ending' => '...', 'exact' => true, 'html' => false)),
	            $event_data['TourneeLocation']['name'],
	            $event_data['TourneeLocation']['address_street'].' '.$event_data['TourneeLocation']['address_number'],
	            $event_data['TourneeLocation']['address_city'],
	            $event_data['TourneeLocation']['website'],
	            array(
	                array(
	                    'date' => date('d-m-Y', strtotime($event_data['TourneeEvent']['start_datetime'])),
	                    'time' => date('H:i', strtotime($event_data['TourneeEvent']['start_datetime']))
	                ),
	                #array(
	                #    'date' => date('d-m-Y', strtotime($event_data['TourneeEvent']['end_datetime'])),
                    #    'time' => date('H:m', strtotime($event_data['TourneeEvent']['end_datetime']))
                    #),
	            )			
	        ), array('encoding' => 'utf-8')
	    );
	    
	    return xmlrpc_decode($this->__comedy_app_request($request));
	}
	
	function __edit_comedy_app_event($event_data, $comedyapp_id){
	    App::import('Core', 'Helper');
	    App::import('Helper', 'Text');
	    $texthelper = new TextHelper();
	    
	    $request = xmlrpc_encode_request('comedy.editAgenda',
	        array(
	            Configure::read('Tournee.comedyapp_username'),
	            Configure::read('Tournee.comedyapp_password'),
	            $comedyapp_id,
	            $event_data['TourneeTour']['title'],
	            $event_data['TourneeTour']['description'],
	            $texthelper->truncate(strip_tags($event_data['TourneeTour']['description']), 75, array('ending' => '...', 'exact' => true, 'html' => false)),
	            $event_data['TourneeLocation']['name'],
	            $event_data['TourneeLocation']['address_street'].' '.$event_data['TourneeLocation']['address_number'],
	            $event_data['TourneeLocation']['address_city'],
	            $event_data['TourneeLocation']['website'],
	            array(
	                array(
	                    'date' => date('d-m-Y', strtotime($event_data['TourneeEvent']['start_datetime'])),
	                    'time' => date('H:i', strtotime($event_data['TourneeEvent']['start_datetime']))
	                ),
	                #array(
	                #    'date' => date('d-m-Y', strtotime($event_data['TourneeEvent']['end_datetime'])),
                    #    'time' => date('H:i', strtotime($event_data['TourneeEvent']['end_datetime']))
                    #),
	            )			
	        ), array('encoding' => 'utf-8')
	    );
	    
	    return xmlrpc_decode($this->__comedy_app_request($request));
	}
}
?>