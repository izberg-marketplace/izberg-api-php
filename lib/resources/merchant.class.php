<?php namespace Ice;

require_once("resource.class.php");

class Merchant extends Resource
{
	public function get_list($params, $accept_type) {
		return self::$Izberg->Call("application/" . self::$Izberg->getAppNamespace() . "/merchants/", 'GET', $params, $accept_type);
	}

	public function get_catalog($merchant_id = null, $params = null, $accept_type = 'Accept: application/xml')
	{
		if (!$merchant_id)
			$merchant_id = $this->id;
		$channel = $this->get_channel($merchant_id);
		return self::$Izberg->Call("product_channel/" . $channel->id . "/viewer/?format=xml", 'GET', $params, $accept_type);
	}

	public function get_channel($merchant_id = null, $params = null)
	{
		if (!$merchant_id)
			$merchant_id = $this->id;
		return self::$Izberg->Call("merchant/".$merchant_id."/active_products_channel/", 'GET', $params);
	}

	public function getCurrent()
	{
		if ($this->id)
			return $this;
		try
		{
			$seller = parent::$Izberg->Call('merchant/?api_key='.parent::$Izberg->getApiKey());
		}
		catch (Exception $e)
		{
			$seller = false;
		}
		if (!isset($seller->meta->total_count))
			$seller = false;
		else if ($seller->meta->total_count == 0)
			$seller = false;
		else
			$this->hydrate($seller->objects[0]);
		return $this;
	}

	public function getCatalog($params = null, $accept_type = 'Accept: application/xml')
	{
		return $this->get_catalog($this->id, $params , $accept_type);
	}
}

class MerchantImage extends Resource
{
}
