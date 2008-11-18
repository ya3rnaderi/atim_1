<?php

class AnnouncementsController extends AppController {
	
	var $name = 'Announcements';
	var $uses = array('Announcement');
	
	var $components = array('Summaries');
	var $helpers = array('Summaries');
	
	function beforeFilter() {
		
		// $auth_conf array hardcoded in oth_auth component, due to plugins compatibility 
		$this->othAuth->controller = &$this;
		$this->othAuth->init();
		$this->othAuth->check();
		
		// CakePHP function to re-combine dat/time select fields 
		$this->cleanUpFields();
		
	}
	
	function index( $bank_id=0, $group_id=0, $user_id=0 ) {
		
		// set MENU varible for echo on VIEW 
		if ( $user_id ) {
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_86', $bank_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_86', 'core_CAN_89', $bank_id.'/'.$group_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_89', 'core_CAN_99', $bank_id.'/'.$group_id.'/'.$user_id );
		} else if ( $group_id ) { 
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_86', $bank_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_86', 'core_CAN_98', $bank_id.'/'.$group_id );
		} else {
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_96', $bank_id );
		}
		$this->set( 'ctrapp_menu', $ctrapp_menu );
		
		// set FORM variable, for HELPER call on VIEW 
		$this->set( 'ctrapp_form', $this->Forms->getFormArray('announcements') );
		
		// set SUMMARY varible from plugin's COMPONENTS 
		$this->set( 'ctrapp_summary', $this->Summaries->build( $this->othAuth->user('id') ) );
		
		// set SIDEBAR variable, for HELPER call on VIEW 
		// use PLUGIN_CONTROLLER_ACTION by default, but any ALIAS string that matches in the SIDEBARS datatable will do...
		$this->set( 'ctrapp_sidebar', $this->Sidebars->getColsArray( $this->params['plugin'].'_'.$this->params['controller'].'_'.$this->params['action'] ) );
		
		// set FORM variable, for HELPER call on VIEW 
		$this->set( 'bank_id', $bank_id );
		$this->set( 'group_id', $group_id );
		$this->set( 'user_id', $user_id );
		
			$criteria = array();
			$criteria[] = 'group_id="'.$group_id.'"';
			$criteria[] = 'user_id="'.$user_id.'"';
			$criteria = array_filter($criteria);
			
		list( $order, $limit, $page ) = $this->Pagination->init( $criteria );
		$this->set( 'announcements', $this->Announcement->findAll( $criteria, NULL, $order, $limit, $page ) );
		
	}
	
	function detail( $bank_id=0, $group_id=0, $user_id=0, $announcement_id ) {
		
		// set MENU varible for echo on VIEW 
		if ( $user_id ) {
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_86', $bank_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_86', 'core_CAN_89', $bank_id.'/'.$group_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_89', 'core_CAN_99', $bank_id.'/'.$group_id.'/'.$user_id );
		} else if ( $group_id ) { 
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_86', $bank_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_86', 'core_CAN_98', $bank_id.'/'.$group_id );
		} else {
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_96', $bank_id );
		}
		$this->set( 'ctrapp_menu', $ctrapp_menu );
		
		// set FORM variable, for HELPER call on VIEW 
		// $this->set( 'ctrapp_form', $this->Forms->getFormArray('participants') );
		$this->set( 'ctrapp_form', $this->Forms->getFormArray('announcements') );
		
		// set SUMMARY varible from plugin's COMPONENTS 
		$this->set( 'ctrapp_summary', $this->Summaries->build( $this->othAuth->user('id') ) );
		
		// set SIDEBAR variable, for HELPER call on VIEW 
		// use PLUGIN_CONTROLLER_ACTION by default, but any ALIAS string that matches in the SIDEBARS datatable will do...
		$this->set( 'ctrapp_sidebar', $this->Sidebars->getColsArray( $this->params['plugin'].'_'.$this->params['controller'].'_'.$this->params['action'] ) );
		
		// set FORM variable, for HELPER call on VIEW 
		$this->set( 'bank_id', $bank_id );
		$this->set( 'group_id', $group_id );
		$this->set( 'user_id', $user_id );
		$this->set( 'announcement_id', $announcement_id );
		
		$this->Announcement->id = $announcement_id;
		$this->set( 'data', $this->Announcement->read() );
	}
	
	function add( $bank_id=0, $group_id=0, $user_id=0 ) {
		
		// setup MODEL(s) validation array(s) for displayed FORM 
		foreach ( $this->Forms->getValidateArray('announcements') as $validate_model=>$validate_rules ) {
			$this->{ $validate_model }->validate = $validate_rules;
		}
		
		// set MENU varible for echo on VIEW 
		if ( $user_id ) {
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_86', $bank_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_86', 'core_CAN_89', $bank_id.'/'.$group_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_89', 'core_CAN_99', $bank_id.'/'.$group_id.'/'.$user_id );
		} else if ( $group_id ) { 
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_86', $bank_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_86', 'core_CAN_98', $bank_id.'/'.$group_id );
		} else {
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_96', $bank_id );
		}
		$this->set( 'ctrapp_menu', $ctrapp_menu );
		
		// set FORM variable, for HELPER call on VIEW 
		$this->set( 'ctrapp_form', $this->Forms->getFormArray('announcements') );
		
		// set SUMMARY varible from plugin's COMPONENTS 
		$this->set( 'ctrapp_summary', $this->Summaries->build( $this->othAuth->user('id') ) );
		
		// set SIDEBAR variable, for HELPER call on VIEW 
		// use PLUGIN_CONTROLLER_ACTION by default, but any ALIAS string that matches in the SIDEBARS datatable will do...
		$this->set( 'ctrapp_sidebar', $this->Sidebars->getColsArray( $this->params['plugin'].'_'.$this->params['controller'].'_'.$this->params['action'] ) );
		
		// set FORM variable, for HELPER call on VIEW 
		$this->set( 'bank_id', $bank_id );
		$this->set( 'group_id', $group_id );
		$this->set( 'user_id', $user_id );
		
		if ( !empty($this->data) ) {
			
			if ( $this->Announcement->save( $this->data ) ) {
				$this->flash( 'Your data has been saved.', '/announcements/index/'.$bank_id.'/'.$group_id.'/'.$user_id.'/' );
			}
			
		}
		
	}
	
	function edit( $bank_id=0, $group_id=0, $user_id=0, $announcement_id ) {
		
		// setup MODEL(s) validation array(s) for displayed FORM 
		foreach ( $this->Forms->getValidateArray('announcements') as $validate_model=>$validate_rules ) {
			$this->{ $validate_model }->validate = $validate_rules;
		}
		
		// set MENU varible for echo on VIEW 
		if ( $user_id ) {
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_86', $bank_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_86', 'core_CAN_89', $bank_id.'/'.$group_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_89', 'core_CAN_99', $bank_id.'/'.$group_id.'/'.$user_id );
		} else if ( $group_id ) { 
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_86', $bank_id );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_86', 'core_CAN_98', $bank_id.'/'.$group_id );
		} else {
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_41', 'core_CAN_73', '' );
			$ctrapp_menu[] = $this->Menus->tabs( 'core_CAN_73', 'core_CAN_96', $bank_id );
		}
		$this->set( 'ctrapp_menu', $ctrapp_menu );
		
		// set FORM variable, for HELPER call on VIEW 
		$this->set( 'ctrapp_form', $this->Forms->getFormArray('announcements') );
		
		// set SUMMARY varible from plugin's COMPONENTS 
		$this->set( 'ctrapp_summary', $this->Summaries->build( $this->othAuth->user('id') ) );
		
		// set SIDEBAR variable, for HELPER call on VIEW 
		// use PLUGIN_CONTROLLER_ACTION by default, but any ALIAS string that matches in the SIDEBARS datatable will do...
		$this->set( 'ctrapp_sidebar', $this->Sidebars->getColsArray( $this->params['plugin'].'_'.$this->params['controller'].'_'.$this->params['action'] ) );
		
		// set FORM variable, for HELPER call on VIEW 
		$this->set( 'bank_id', $bank_id );
		$this->set( 'group_id', $group_id );
		$this->set( 'user_id', $user_id );
		$this->set( 'announcement_id', $announcement_id );
		
		if ( empty($this->data) ) {
			
			$this->Announcement->id = $announcement_id;
			$this->data = $this->Announcement->read();
			$this->set( 'data', $this->data );
			
		} else {
			
			if ( $this->Announcement->save( $this->data['Announcement'] ) ) {
				$this->flash( 'Your data has been updated.','/announcements/detail/'.$bank_id.'/'.$group_id.'/'.$user_id.'/'.$announcement_id );
			}
			
		}
	}
	
	function delete( $bank_id=0, $group_id=0, $user_id=0, $announcement_id=0 ) {
		
		$this->Announcement->del( $announcement_id );
		$this->flash( 'Your data has been deleted.', '/announcements/index/'.$bank_id.'/'.$group_id.'/'.$user_id.'/' );
		
	}

}

?>