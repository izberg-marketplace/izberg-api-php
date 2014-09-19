<?php

require_once("resource.class.php");

class Merchant extends Resource 
{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_catalog($merchant_id = null, $params = null, $accept_type = 'Accept: application/xml')
	{
		if (!$merchant_id)
			$merchant_id = $this->id;
		return $this->get_list("merchant/".$merchant_d."/download_export", $params , $accept_type);
	}

	public function get_schema($params = null, $accept_type = 'Accept: application/json')
	{
		return $this->get("merchant", "schema", $params, $accept_type);
	}

	public function get_current()
	{
		try
		{
			$seller = $this->Call('merchant/?api_key='.self::$Iceberg->getApiKey());
		}
		catch (Exception $e)
		{
			$seller = false;
		}
		if (!isset($seller->meta->total_count))
			$seller = false;
		else if ($seller->meta->total_count == 0)
			$seller = false;
		return $seller;
	}
}