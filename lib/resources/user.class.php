<?php namespace Ice;

require_once("resource.class.php");

class User extends Resource
{
	public function getAddresses()
	{
		return parent::$Izberg->get_list("address", null, array("user" => $this->id), "Content-Type:Application/json");
	}

	public function getApplications()
	{
		return parent::$Izberg->get_list("application", null, array("contact_user" => $this->id), "Content-Type:Application/json");
	}

	public function getReviews()
	{
		return parent::$Izberg->get_list("review", null, array("user" => $this->id), "Content-Type:Application/json");

	}
	public function getProfile()
	{
		return parent::$Izberg->get("profile", $this->id."/profile/", null, null, $this->getName());
	}

	public function getInbox()
	{
		return parent::$Izberg->get_list("inbox", $this->id."/inbox/", null, null, $this->getName());
	}
}

class Inbox extends Resource
{
}

class profile extends Resource
{
}
