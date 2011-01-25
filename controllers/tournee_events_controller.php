<?php
class TourneeEventsController extends TourneeAppController {
	var $uses = array('Tournee.TourneeEvent');
	
	function admin_index(){
		$this->set('title_for_layout', __('Events', true));
		$events = $this->paginate('TourneeEvent');
		
		$this->set(compact('events'));
	}
	
	function admin_add(){
		$this->set('title_for_layout', __('Add an event', true));
		
		if(!empty($this->data)){
			$this->TourneeEvent->create();
			if($this->TourneeEvent->save($this->data)){
				$this->Session->setFlash(sprintf(__('Event saved!', true)));
                $this->redirect(array('controller' => 'tournee_events', 'action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('The event could not be saved. Please, try again.', true)));
			}
		}
		
		$tours = $this->TourneeEvent->TourneeTour->find('list');
		$locations = $this->TourneeEvent->TourneeLocation->find('list');
		
		$this->set(compact('tours', 'locations'));
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
				$this->Session->setFlash(sprintf(__('The event has been saved', true)));
                $this->redirect(array('controller' => 'tournee_events', 'action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('The event could not be saved. Please, try again.', true)));
			}
		}
		
		$this->data = $event;
			
		$tours = $this->TourneeEvent->TourneeTour->find('list');
		$locations = $this->TourneeEvent->TourneeLocation->find('list');
		
		$this->set(compact('tours', 'locations'));		
	}
	
	function admin_delete($id = null){
		if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		if($this->TourneeEvent->delete($id)){
			$this->Session->setFlash(__('Event deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
		}
		else {
			$this->Session->setFlash(__('Failed to delete the event', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
		}
	}
}
?>