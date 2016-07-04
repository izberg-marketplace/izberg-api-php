<?php
class cartTest extends BaseTester
{
  /**
   * @before
   */
  public function startRecording()
  {
    \VCR\VCR::turnOn();
    \VCR\VCR::configure()->setStorage('json');
  }

  /**
   * @after
   */
  public function stopRecording()
  {
    // To stop recording requests, eject the cassette
    \VCR\VCR::eject();
    // Turn off VCR to stop intercepting requests
    \VCR\VCR::turnOff();
  }

  /**
   * Tests
   */
  public function testgetCartShouldReturnACart()
  {
    \VCR\VCR::insertCassette('testgetCartShouldReturnACart');

    $a = $this->getIzberg();
    $cart = $a->get("cart");
    $this->assertArrayHasKey("id", (array)$cart);
  }

  public function testgetCartItemsShouldReturnCartItems()
  {
    \VCR\VCR::insertCassette('testgetCartItemsShouldReturnCartItems');

    $a = $this->getIzberg();
    $cart = $a->get("cart");
    $items = $cart->getItems();
    $this->assertTrue(is_array($cart->items));
  }

  public function testinDebugModeCartShouldReturnDebugEqualToTrue()
  {
    \VCR\VCR::insertCassette('testinDebugModeCartShouldReturnDebugEqualToTrue');

    $a = $this->getIzberg();
    $a->setDebug(true);
    $cart = $a->create('cart');
    $this->assertTrue($cart->debug);
  }

  public function testAddCartItemShouldAddItem()
  {
    \VCR\VCR::insertCassette('testAddCartItemShouldAddItem');

    $a = $this->getIzberg();

    $my_cart = $a->get('Cart');
    $number_items = count($my_cart->getItems());
    $this->assertTrue($number_items==0);
    $my_cart->addItem(array(
      "offer_id" => 38895,
      "variation_id" => null,
      "quantity" => 1
    ));
    \VCR\VCR::eject();

    \VCR\VCR::insertCassette('testAddCartItemShouldAddItem2');
    $cart = $a->get('Cart');
    $items = $cart->getItems();
    $this->assertEquals(count($items), $number_items + 1);
    // We remove the item
    // TODO Uncomment this once the bug is fixed in staging
    $firstItem = $items[0];
    $firstItem->delete();
    \VCR\VCR::eject();

    \VCR\VCR::insertCassette('testAddCartItemShouldAddItem3');
    $items = $cart->getItems();
    $this->assertEquals(count($items), $number_items);
  }

  public function testCleanCartShouldRemoveAllItems()
  {
    \VCR\VCR::insertCassette('testCleanCartShouldRemoveAllItems');

    $a = $this->getIzberg();

    $my_cart = $a->create('cart');
    $number_items = count($my_cart->getItems());
    $this->assertTrue($number_items==0);
    $item = $my_cart->addItem(array(
      "offer_id" => 38895,
      "variation_id" => null,
      "quantity" => 1
    ));

    \VCR\VCR::eject();
    \VCR\VCR::insertCassette('testCleanCartShouldRemoveAllItems2');
    $items = $my_cart->getItems();
    $this->assertEquals(count($items), 1);
    $my_cart->clean();
    \VCR\VCR::eject();
    \VCR\VCR::insertCassette('testCleanCartShouldRemoveAllItems3');
    $items = $my_cart->getItems();
    $this->assertEquals(count($items), 0);
    \VCR\VCR::eject();
  }

  public function testNewCartItemShouldCreateANewCart()
  {
    \VCR\VCR::insertCassette('testNewCartItemShouldCreateANewCart1');
    $a = $this->getIzberg();
    $cart = $a->create('cart');
    \VCR\VCR::eject();

    \VCR\VCR::insertCassette('testNewCartItemShouldCreateANewCart2');
    $cart1 = $a->create('cart');
    $this->assertNotSame($cart1->id, $cart->id);
    \VCR\VCR::eject();
  }

  // TODO When sandbox will be updated
  public function testShippingWorkflow()
  {

    \VCR\VCR::insertCassette('testShippingWorkflow1');
    $a = $this->getShippingIzberg();
    $cart = $a->create('cart');

    // We add a product IPhone 5C Jaune 32 Go
    $item = $cart->addItem(array(
      "offer_id" => 1059252,
      "variation_id" => null,
      "quantity" => 1
    ));

    $country = $a->get('country');
    $address = $a->create('address', array(
			"address" => "Address line 1",
			"address2" => "Address line 2",
			"city" => "St remy de provence",
			"company" => "Sebfie",
			"country" => "/v1/country/" . $country->id . "/",
			// "default_billing" => true,
			// "default_shipping" => true,
			"digicode" => null,
			"first_name" => "sebastien",
			"floor" => null,
			"last_name" => "fieloux",
			"name" => "House",
			"phone" => "0698674532",
			"state" => null,
			"status" => 10,
			"zipcode" => "13210"
			));
		$cart->setBillingAddress($address->id);
		$cart->setShippingAddress($address->id);

    // We get list of shipping_providers
    $shipping_options = $cart->shippingOptions();
    // We recompute total
    $cart->updateShippingOptions();

    \VCR\VCR::eject();
    \VCR\VCR::insertCassette('testShippingWorkflow2');

    $shipping_options = $cart->shippingOptions();

    $choices = array("choices" => array());
    foreach($shipping_options->objects as $options) {
      // We select first shipping provider
      if(!isset($options->shipping_choices[0])) {
        continue;
      }
      array_push($choices["choices"], "/v1/cart_shipping_choice/".$options->shipping_choices[0]->id."/");
    }
    //
    $cart->selectShippingOptions($choices);

    \VCR\VCR::eject();
    \VCR\VCR::insertCassette('testShippingWorkflow3');
    $shipping_options = $cart->shippingOptions();


    $this->assertEquals($shipping_options->objects[0]->shipping_choices[0]->status, "selected");
    \VCR\VCR::eject();
  }
}
