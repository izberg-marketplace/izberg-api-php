<?php
namespace Izberg\Resource;
use Izberg\Resource;

class CartShippingChoice extends Resource
{
  /**
  * Select cart shipping choice
  * @return Boolean
  */
  public function select()
  {
    return parent::$Iceberg->Call("cart_shipping_choice/" . $this->id . "/select/" , "POST", array(), 'Content-Type: application/json');
  }
}
