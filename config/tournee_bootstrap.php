<?php
	Croogo::hookRoutes('Tournee');
	#Croogo::hookBehavior('Node', 'Example.Example', array());
	Croogo::hookComponent('Nodes', 'Tournee.TourneeUpcomming');
	#Croogo::hookHelper('Nodes', 'Example.Example');
	Croogo::hookAdminMenu('Tournee');
	#Croogo::hookAdminRowAction('Nodes/admin_index', 'Example', 'plugin:example/controller:example/action:index/:id');
	#Croogo::hookAdminTab('Nodes/admin_add', 'Example', 'example.admin_tab_node');
	#Croogo::hookAdminTab('Nodes/admin_edit', 'Example', 'example.admin_tab_node');
	
	# Load Tournee config
	if(file_exists(APP.'plugins'.DS.'tournee'.DS.'config'.DS.'settings.yml')){
		 $settings = Spyc::YAMLLoad(file_get_contents(APP.'plugins'.DS.'tournee'.DS.'config'.DS.'settings.yml'));
	}
	
	foreach($settings AS $settingKey => $settingValue){
		Configure::write($settingKey, $settingValue);
	}
?>