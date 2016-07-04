<?php
namespace Izberg\Resource;
use Izberg\Resource;

class Cart extends Resource
{
    /**
    * get current cart items
    *
    * @return Object Array
    */
    public function getItems($params = null, $accept_type = "Accept: application/json")
    {
        $list = self::$Izberg->Call("cart/".$this->id."/items/", 'GET', $params, $accept_type);
        $object_list = array();
        if (!isset($list->objects))
          return null;
        foreach ($list->objects as $object)
        {
          $obj = new CartItem();
          $obj->hydrate($object);
          $object_list[] = $obj;
        }
        if (!isset($this->items)) {
          $this->items = array();
        }
        $this->items = array_merge($this->items, $object_list);
        return $object_list;
    }

    /**
    * add an item to a cart
    *
    * @return Array
    */
    public function addItem($params = null, $accept_type = 'Content-Type: application/json')
    {
        // Params:
        //   offer_id: Integer
        //   variation_id: Integer
        //   quantity: Integer
        //   gift: Boolean
        //   bundled: Boolean
        $response = self::$Izberg->Call("cart/".($this->id ? $this->id : "none")."/items/", 'POST', $params, $accept_type);
        $object = new CartItem();
        $object->hydrate($response);
        $this->items[] = $object;
        return $object;
    }

    /**
    * update an item to a cart
    *
    * @return Array
    */
    public function updateItem($id, $params = null, $accept_type = 'Accept: application/json')
    {
        // Params:
        //   offer_id: Integer
        //   variation_id: Integer
        //   quantity: Integer
        //   gift: Boolean
        //   bundled: Boolean
        $object = new CartItem();
        $response = parent::$Izberg->Call($object->getName()."/".$id."/", "PUT", $params, $accept_type);
        $object->hydrate($response);
        return $object;
    }

    /**
    * Set cart shipping address
    *
    * @return StdObject
    */
    public function setShippingAddress($id)
    {
        $params["shipping_address"] = "/v1/address/".$id."/";
        $this->shipping_address = "/v1/address/".$id."/";
        return parent::$Izberg->update('Cart', $this->id, $params);
    }


    /**
    * Set cart Billing address
    *
    * @return StdObject
    */
    public function setBillingAddress($id)
    {
        $params["billing_address"] = "/v1/address/".$id."/";
        $this->billing_address = "/v1/address/".$id."/";
        return parent::$Izberg->update('Cart', $this->id, $params);
    }

    public function createOrder($params = null, $accept_type = 'Content-Type: application/json')
    {
        $object = new Order();
        $response = parent::$Izberg->Call("cart/" . $this->id . "/createOrder/", 'POST', $params, $accept_type);
        $object->hydrate($response);
        return $object;
    }

    public function addOffer($product_offer_id,$quantity = 1)
    {
        $params = array(
            'offer_id'=> $product_offer_id,
            'quantity'=> $quantity
        );
        return parent::$Izberg->Call($this->getName()."/items/", "POST", $params);
    }

    
	/**
 	  * Remove an item from cart
 	  * @param $id
 	  * @param string $accept_type
 	  * @return CartItem
 	  */
 	  public function removeItem($id, $accept_type = 'Accept: application/json')
 	  {
      $object = new CartItem();
      $response = parent::$Izberg->Call($object->getName()."/".$id."/", "DELETE", array(), $accept_type);
      $object->hydrate($response);
      return $object;
 	  }

    /**
 	  * Apply a coupon code
 	  * @param $code
 	  * @param string $action
 	  * @return DiscountCode
 	  */
    public function discountCode($code, $action = "add")
    {
      $params = array('discount_code'=> $code);
      $id = $this->id ? $this->id : 'mine';
      return parent::$Izberg->Call("cart/" . $id . "/" . $action . "_discount_code/", "POST", $params, 'Content-Type: application/json');
    }

    /**
    * Remove all cart items
    * @return Boolean
    */
    public function clean() {
      $this->getItems();
      foreach ($this->items as $item) {
        $item->delete();
      }
      return true;
    }


    /**
    * Remove all shipping options
    * @return Array
    */
    public function shippingOptions() {
      $id = $this->id ? $this->id : 'mine';
      return parent::$Izberg->Call("cart/" . $id . "/shipping_options/" , "GET", null, 'Content-Type: application/json');
    }

    /**
    * Select a shipping option
    * @return ShippingOption
    */
    public function selectShippingOption($option_id) {
      $id = $this->id ? $this->id : 'mine';
      return parent::$Izberg->Call("cart/" . $id . "/shipping_options/" . $option_id . "/select/" , "POST", array(), 'Content-Type: application/json');
    }

    /**
    * Select multiple shipping options
    * @param Array $ids
    * @return Array
    */
    public function selectShippingOptions($params) {
      $id = $this->id ? $this->id : 'mine';
      return parent::$Izberg->Call("cart/" . $id . "/shipping_options/" , "POST", $params, 'Content-Type: application/json');
    }

    /**
    * Update all available shipping options for cart
    * @return Array
    */
    public function updateShippingOptions() {
      $id = $this->id ? $this->id : 'mine';
      return parent::$Izberg->Call("cart/" . $id . "/shipping_options/update/" , "POST", null, 'Content-Type: application/json');
    }
}
