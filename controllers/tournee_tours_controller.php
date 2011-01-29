<?php
class TourneeToursController extends TourneeAppController {
	var $uses = array('Tournee.TourneeEvent', 'Tournee.TourneeTour');
	var $uploadsDir = 'uploads';
	
	function index(){
		$this->TourneeTour->recursive = 0;
		$running_tours = $this->TourneeTour->find('all', array(
			'conditions' => array(
				'end_date >=' => date('Y-m-d'),
				'status' => 1
			)
		));
		
		$past_tours = $this->TourneeTour->find('all', array(
			'conditions' => array(
				'end_date <' => date('Y-m-d'),
				'status' => 1
			)
		));
		
		
		$this->compact('running_tours', 'past_tours');
	}
	
	function view($id = null){
		$this->TourneeTour->recursive = 2;
		
		if(isset($this->params['named']['slug'])){
			$tour = $this->TourneeTour->find('first', array(
				'conditions' => array(
					'TourneeTour.slug' => $this->params['named']['slug'],
					'TourneeTour.status' => 1,
				)
			));
		}
		else {
			$tour = $this->TourneeTour->find('first', array(
				'conditions' => array(
					'TourneeTour.id' => $id,
					'TourneeTour.status' => 1
				)
			));
		}
		
		if(!isset($tour['TourneeTour']['id'])){
			$this->Session->setFlash(__('This tour does not exist!', true), 'default', array('class' => 'error'));
            $this->redirect(array('action', 'index'));
		}

		$this->set('title_for_layout', $tour['TourneeTour']['title']);
		$this->set(compact('tour'));
	}
	
	function admin_index(){
		$this->set('title_for_layout', __('Tours', true));
		
		$this->TourneeTour->recursive = 0;
		$tours = $this->paginate('TourneeTour');
		
		$this->set(compact('tours'));
	}
	
	function admin_view($id = null){
		if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$this->TourneeTour->recursive = 3;
		$tour = $this->TourneeTour->findById($id);
		if(!$tour) {
			$this->Session->setFlash(__('The tour does not exist.', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$this->set('title_for_layout', sprintf(__('Tour: %s', true), $tour['TourneeTour']['title']));
		$this->set(compact('tour'));
	}
	
	function admin_add(){
		$this->set('title_for_layout', __('Add a tour', true));
		
		if(!empty($this->data)){
			if(!empty($this->data['TourneeTour']['file']) && $this->data['TourneeTour']['file']['error'] == 0){
				$file = $this->data['TourneeTour']['file'];
				unset($this->data['TourneeTour']['file']);
				$destination = WWW_ROOT.$this->uploadsDir.DS.$file['name'];
				if(file_exists($destination)){
					$newFileName = String::uuid().'_'.$file['name'];
					$destination = WWW_ROOT.$this->uploadsDir.DS.$newFileName;
				}
				else {
					$newFileName = $file['name'];
				}
				$this->data['TourneeTour']['image_path'] = '/'. $this->uploadsDir.'/'.$newFileName;
			}
			
			$this->TourneeTour->create();
			
			if($this->TourneeTour->save($this->data)){
				if(isset($destination)){
					move_uploaded_file($file['tmp_name'], $destination);
				}
				
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['TourneeTour']['title']));
                $this->redirect(array('controller' => 'tournee_tours', 'action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('The tour could not be saved. Please, try again.', true)));
			}
		}
	}
	
	function admin_edit($id = null){
		if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$tour = $this->TourneeTour->findById($id);
		if(!$tour) {
			$this->Session->setFlash(__('The tour does not exist.', true));
            $this->redirect(array('action' => 'index'));
		}
		
		$this->set('title_for_layout', sprintf(__('Edit tour: %s', true), $tour['TourneeTour']['title']));
		
		if(!empty($this->data)){
			if(!empty($this->data['TourneeTour']['file']) && $this->data['TourneeTour']['file']['error'] == 0){
				$file = $this->data['TourneeTour']['file'];
				unset($this->data['TourneeTour']['file']);
				$destination = WWW_ROOT.$this->uploadsDir.DS.$file['name'];
				if(file_exists($destination)){
					$newFileName = String::uuid().'_'.$file['name'];
					$destination = WWW_ROOT.$this->uploadsDir.DS.$newFileName;
				}
				else {
					$newFileName = $file['name'];
				}
				$this->data['TourneeTour']['image_path'] = '/'. $this->uploadsDir.'/'.$newFileName;
			}
			
						
			$this->TourneeTour->id = $id;
			if($this->TourneeTour->save($this->data)){
				if(isset($destination)){
					unlink(WWW_ROOT.$tour['TourneeTour']['image_path']);
					move_uploaded_file($file['tmp_name'], $destination);
				}
				
				$this->Session->setFlash(sprintf(__('%s has been saved', true), $this->data['TourneeTour']['title']));
                $this->redirect(array('controller' => 'tournee_tours', 'action' => 'index'));
			}
			else {
				$this->Session->setFlash(sprintf(__('The tour could not be saved. Please, try again.', true)));
			}
		}
		
		$this->data = $tour;
	}
	
	function admin_delete($id = null){
		if(!$id){
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$tour = $this->TourneeTour->findById($id);
		
		if($this->TourneeTour->delete($id)){
			if(!empty($tour['TourneeTour']['image_path'])){
				unlink(WWW_ROOT.$tour['TourneeTour']['image_path']);
			}
			
			$this->Session->setFlash(__('Tour deleted', true), 'default', array('class' => 'success'));
            $this->redirect(array('action' => 'index'));
		}
		else {
			$this->Session->setFlash(__('Failed to remove the tour', true), 'default', array('class' => 'error'));
            $this->redirect(array('action' => 'index'));
		}
	}
}
?>