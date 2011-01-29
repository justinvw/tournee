<?php
class TourneeActivation {
	public function beforeActivation(&$controller){
		return true;
	}
	
	public function onActivation(&$controller){
		$controller->Croogo->addAco('TourneeLocations');
		$controller->Croogo->addAco('TourneeLocations/admin_index');
		$controller->Croogo->addAco('TourneeLocations/admin_add');
		$controller->Croogo->addAco('TourneeLocations/admin_edit');
		$controller->Croogo->addAco('TourneeLocations/admin_delete');

		$controller->Croogo->addAco('TourneeEvents');
		$controller->Croogo->addAco('TourneeEvents/admin_index');
		$controller->Croogo->addAco('TourneeEvents/admin_add');
		$controller->Croogo->addAco('TourneeEvents/admin_edit');
		$controller->Croogo->addAco('TourneeEvents/admin_delete');
		$controller->Croogo->addAco('TourneeEvents/admin_facebook_logout');

		$controller->Croogo->addAco('TourneeTours');
		$controller->Croogo->addAco('TourneeTours/admin_index');
		$controller->Croogo->addAco('TourneeTours/admin_view');
		$controller->Croogo->addAco('TourneeTours/admin_add');
		$controller->Croogo->addAco('TourneeTours/admin_edit');
		$controller->Croogo->addAco('TourneeTours/admin_delete');
		$controller->Croogo->addAco('TourneeTours/index', array('registered', 'public'));
		$controller->Croogo->addAco('TourneeTours/view', array('registered', 'public'));
	}
	
	public function beforeDeactivation(&$controller){
		return true;
	}
	
	public function onDeactivation(&$controller){
		$controller->Croogo->removeAco('TourneeLocations');
		$controller->Croogo->removeAco('TourneeEvents');
		$controller->Croogo->removeAco('TourneeTours');
	}
}
?>