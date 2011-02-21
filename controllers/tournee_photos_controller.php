<?php
class TourneePhotosController extends TourneeAppController {
	var $uses = array('Tournee.TourneePhoto');
	var $uploadsDir = 'uploads';
	
	function admin_add($tour_id = null){
		if(!empty($this->data)){
			if(!empty($this->data['TourneePhoto']['file']) && $this->data['TourneePhoto']['file']['error'] == 0){
				$file = $this->data['TourneePhoto']['file'];
				unset($this->data['TourneePhoto']['file']);
				$destination = WWW_ROOT.$this->uploadsDir.DS.$file['name'];
				if(file_exists($destination)){
					$newFileName = String::uuid().'_'.$file['name'];
					$destination = WWW_ROOT.$this->uploadsDir.DS.$newFileName;
				}
				else {
					$newFileName = $file['name'];
				}
				$this->data['TourneePhoto']['image_path'] = '/'. $this->uploadsDir.'/'.$newFileName;
				$this->data['TourneePhoto']['tournee_tour_id'] = $tour_id;
				if($this->TourneePhoto->save($this->data)){
					if(isset($destination)){
						move_uploaded_file($file['tmp_name'], $destination);
					}
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
			unlink(WWW_ROOT.$photo['TourneePhoto']['image_path']);
			$this->redirect(array('controller' => 'tournee_tours', 'action' => 'edit', $photo['TourneePhoto']['tournee_tour_id'].'#tour-photos'));
		}
	}
}
?>