<?php

function usin_module_options(){
	return USIN_Module_Options::get_instance();
}

function usin_manager(){
	return USIN_Manager::get_instance();
}

function usin_options(){
	return usin_manager()->options;
}