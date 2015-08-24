<?php namespace Ice;

require_once "resource.class.php";

class Product extends Resource
{
	public function reviews($id = null)
	{
		if ($id === null && $this->id)
			$id = $this->id;
		return parent::$Izberg->get("review", $params = array("product"=>$id));
	}
}

class ProductOffer extends Resource
{
}


class ProductVariation extends Resource
{
}


class OfferImage extends Resource
{
}
