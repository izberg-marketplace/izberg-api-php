<?php namespace Ice;

require_once("resource.class.php");

class Message extends Resource
{
	/*
	**	Mark message as read
	*/
	public function read()
	{
		return parent::$Izberg->Call($this->getName()."/read/", "POST");
	}	
}
