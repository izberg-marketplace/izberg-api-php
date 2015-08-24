<?php namespace Ice;

require_once("resource.class.php");
require_once("merchant.class.php");

class Feed extends Resource
{
	/*
	* @Params :
	*     period: "weeks" | "days" | "hours" | "minutes"
	*     every: integer
	*     name: string
	*/
	public function getName()
	{
		return "merchant_catalog_feed";
	}

	public function __construct()
	{
		parent::__construct();
	}

	public function post($feed_url, $every, $period, $name, $source_type = "prestashop")
	{
		$merchant = new Merchant();
		$merchant->getCurrent();
		if (!$merchant->id)
			return false;
		$merchant_uri = "/v1/merchant/".$merchant->id."/";
		$data = array(
			'merchant'=>$merchant_uri,
			'every'=>$every,
			'period'=>$period,
			'name'=>$name,
			'source_type'=>$source_type,
			'feed_url'=>$feed_url
		);
		return parent::$Izberg->Call($this->getName()."/", "POST", $data, "Content-Type: application/json");
	}

}
