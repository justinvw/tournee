<?php
	Croogo::hookRoutes('Tournee');
	#Croogo::hookBehavior('Node', 'Example.Example', array());
	#Croogo::hookComponent('*', 'Example.Example');
	#Croogo::hookHelper('Nodes', 'Example.Example');
	Croogo::hookAdminMenu('Tournee');
	#Croogo::hookAdminRowAction('Nodes/admin_index', 'Example', 'plugin:example/controller:example/action:index/:id');
	#Croogo::hookAdminTab('Nodes/admin_add', 'Example', 'example.admin_tab_node');
	#Croogo::hookAdminTab('Nodes/admin_edit', 'Example', 'example.admin_tab_node');
?>