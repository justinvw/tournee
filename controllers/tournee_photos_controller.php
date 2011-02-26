<?php
class TourneePhotosController extends TourneeAppController {
	var $uses = array('Tournee.TourneePhoto');
	var $components = array('Tournee.Imageupload');
		
	function admin_add($tour_id = null){
		if(!empty($this->data)){
			if(!empty($this->data['TourneePhoto']['file']) && $this->data['TourneePhoto']['file']['error'] == 0){
				$file = $this->data['TourneePhoto']['file'];
				$this->data['TourneePhoto']['image_path'] = $this->Imageupload->upload($file);
				unset($this->data['TourneePhoto']['file']);
				
				$this->data['TourneePhoto']['tournee_tour_id'] = $tour_id;
				if($this->TourneePhoto->save($this->data)){
					$this->redirect(array('controller' => 'tournee_tours', 'action' => 'edit', $tour_id));
				}
			}	
		}
		
		$this->set('tour_id', $tour_id);
	}
	
	function admin_edit($photo_id = null){
		
	}
	
	function admin_delete($id = null){
		if(!$id) {
			$this->Session->setFlash(__('Invalid content', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$photo = $this->TourneePhoto->findById($id);
		
		if($this->TourneePhoto->delete($id, true)){
			$this->Imageupload->delete($photo['TourneePhoto']['image_path']);
			$this->redirect(array('controller' => 'tournee_tours', 'action' => 'edit', $photo['TourneePhoto']['tournee_tour_id'].'#tour-photos'));
		}
	}
}
?>