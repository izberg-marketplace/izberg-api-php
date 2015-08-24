<?php namespace Ice;

require_once("resource.class.php");
require_once("order.class.php");

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
        if (!isset($this->items))
            $this->items = array();
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
        /*
        **  Add an offer to the Cart
        */
        $params = array(
            'offer_id'=> $product_offer_id,
            'quantity'=> $quantity
        );
        return parent::$Izberg->Call($this->getName()."/items/", "POST", $params);
    }

}

class CartItem extends Resource
{
}
