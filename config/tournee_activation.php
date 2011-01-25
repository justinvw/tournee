<?php
class TourneeActivation {
	public function beforeActivation(&$controller){
		return true;
	}
	
	public function onActivation(&$controller){
		// ACL: set ACOs with permissions
		#$controller->Croogo->addAco('Tournee');
		#$controller->Croogo->addAco('Example/admin_index'); // ExampleController::admin_index()
		#$controller->Croogo->addAco('Example/index', array('registered', 'public')); // ExampleController::index()
	}
	
	public function beforeDeactivation(&$controller){
		return true;
	}
	
	public function onDeactivation(&$controller){
		#$controller->Croogo->removeAco('Example');
	}
}
?>