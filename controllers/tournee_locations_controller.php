<?php
class TourneeLocationsController extends TourneeAppController {
	var $uses = array('Tournee.TourneeLocation');
	var $helpers = array('Tournee.Countries');
	
	function admin_index(){
		$this->set('title_for_layout', __('Locations', true));
		$locations = $this->paginate('TourneeLocation');
		
		$this->set(compact('locations'));
	}
	
	function admin_add(){
		$this->set('title_for_layout', __('Add a location', true));
		
		if(!empty($this->data)){
			$this->TourneeLocation->create();
			if($this->TourneeLocation->save($this->data)){
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['TourneeLocation']['name']));
                $this->redirect(array('controller' => 'tournee_locations', 'action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('The location could not be saved. Please, try again.', true)));
			}
		}
	}
	
	function admin_edit($id = null){
		if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$location = $this->TourneeLocation->findById($id);
		if(!$location) {
			$this->Session->setFlash(__('The location does not exist.', true));
            $this->redirect(array('action' => 'index'));
		}
		
		$this->set('title_for_layout', sprintf(__('Edit location: %s', true), $location['TourneeLocation']['name'].' ('.$location['TourneeLocation']['address_city'].', '.$location['TourneeLocation']['address_country'].')'));
		
		if(!empty($this->data)){
			$this->TourneeLocation->id = $id;
			if($this->TourneeLocation->save($this->data)){
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['TourneeLocation']['name']));
                $this->redirect(array('controller' => 'tournee_locations', 'action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('The location could not be saved. Please, try again.', true), $this->data['TourneeLocation']['name']));
			}
		}
		
		$this->data = $location;
	}
	
	function admin_delete($id = null){
		if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		if($this->TourneeLocation->delete($id)){
			$this->Session->setFlash(__('Location deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
		}
		else {
			$this->Session->setFlash(__('Failed to remove the location', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
		}
	}
}
?>
