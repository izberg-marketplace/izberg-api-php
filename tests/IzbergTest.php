<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once "lib/iceberg.php";

class IzbergTest extends PHPUnit_Framework_TestCase
{
	// ======================================
	// TESTS FUNCTIONS
	// ======================================
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

	public function getIceberg($options = array())
	{
		if (empty($options)) {
			$options = array(
					"appNamespace" => "lolote",
					"username" => getenv("USERNAME1"),
					"accessToken" => getenv("TOKEN1"),
					"sandbox" => true
			);
		}
		$mock = $this->getMock('Iceberg', array('setTimestamp', 'getTimestamp'), array($options));

		$mock->expects($this->any())
	    ->method('setTimestamp')
	    ->will($this->returnValue(true));

		$mock->expects($this->any())
	    ->method('getTimestamp')
	    ->will($this->returnValue(1439912480));

		return $mock;
	}

	public function sso(&$a)
	{
		$a->sso(array(
      "email"     => "myemail@yahoo.fr",
			"apiKey"    => "d43fce48-836c-43d3-9ddb-7da2e70af9f1",
			"apiSecret" => "6cb0c550-9686-41af-9b5e-5cf2dc2aa3d0",
      "firstName" => "my_firstname",
      "lastName"  => "my_lastname"
    ));
	}


	// ======================================
	// TESTS
	// ======================================

	public function testConstructorUseParams()
	{
		\VCR\VCR::insertCassette('testConstructorUseParams');

		$a = $this->getIceberg();
		$this->sso($a);
		// Assertions
		$this->assertEquals("lolote", $a->getAppNamespace());
		$this->assertEquals("d43fce48-836c-43d3-9ddb-7da2e70af9f1", $a->getApiKey());
		$this->assertEquals("6cb0c550-9686-41af-9b5e-5cf2dc2aa3d0", $a->getApiSecret());
		$this->assertEquals("myemail@yahoo.fr", $a->getEmail());
		$this->assertEquals("my_firstname", $a->getFirstName());
		$this->assertEquals("my_lastname", $a->getLastName());
		$this->assertEquals(Iceberg::DEFAULT_CURRENCY, $a->getCurrency());
		$this->assertEquals(Iceberg::DEFAULT_SHIPPING_COUNTRY, $a->getShippingCountry());
	}

	public function testSandboxParamIsWellUsedForUrlToRequest()
	{
		$a = new Iceberg(array("sandbox" => true, "appNamespace" => "lolote"));
		$this->assertEquals(PHPUnit_Framework_Assert::readAttribute($a, '_api_url'), "https://api.sandbox.iceberg.technology/v1/");
		$a = new Iceberg(array("appNamespace" => "lolote"));
		$this->assertEquals(PHPUnit_Framework_Assert::readAttribute($a, '_api_url'), "https://api.iceberg.technology/v1/");
	}


	public function testShouldThrowErrorIfNotGoodResponseCode()
	{
		\VCR\VCR::insertCassette('testShouldThrowErrorIfNotGoodResponseCode');

		$this->setExpectedException('Exception', "Error: from Iceberg API - error: {\"errors\": [\"message_auth is invalid\"]}");
		$a = $this->getIceberg();
		$a->sso(array(
      "email"     => "myemail@yahoo.fr",
			"apiKey"    => "d43fce48-836c-43d3-9ddb-7da2e70af9f1",
			"apiSecret" => "6cb0c550-9686",
      "firstName" => "my_firstname",
      "lastName"  => "my_lastname"
    ));
	}

	public function testGetInstanceShoudlReturnTheCreatedInstance()
	{
		\VCR\VCR::insertCassette('testGetInstanceShoudlReturnTheCreatedInstance');

		$a = $this->getIceberg();
		$this->sso($a);

		$this->assertEquals($a, Iceberg::getInstance());
	}


	public function testGetProductShouldReturnProducts()
	{
		\VCR\VCR::insertCassette('testGetProductShouldReturnProducts');

		$a = $this->getIceberg();
		$products = $a->get_list("product");
		$this->assertTrue(is_array($products));
    $this->assertNotEmpty($products);
	}

	public function testGetFullProductImportShouldReturnAllProducts()
	{
		\VCR\VCR::insertCassette('testGetFullProductImportShouldReturnAllProducts');

		$a = $this->getIceberg();
		$merchant = $a->get("Merchant", 15);
		$result = $merchant->get_catalog();
    $this->assertInstanceOf('SimpleXMLElement', $result);
	}

	public function testGetProductSchemaShouldReturnProductSchema()
	{
    \VCR\VCR::insertCassette('testGetProductSchemaShouldReturnProductSchema');

		$a = $this->getIceberg();
		$product = $a->get_schema("product");
    $this->assertNotNull($product->allowed_detail_http_methods);
	}

	public function testGetCategoriesShouldReturnCategories()
	{
    \VCR\VCR::insertCassette('testGetCategoriesShouldReturnCategories');

		$a = $this->getIceberg();
		$categories = $a->get_list("category");
		$this->assertTrue(is_array($categories));
		$this->assertNotEmpty($categories);
	}

	public function testGetMerchantsShouldReturnMerchants()
	{
    \VCR\VCR::insertCassette('testGetMerchantsShouldReturnMerchants');

		$a = $this->getIceberg();
		$merchants = $a->get_list("merchant");
		$this->assertTrue(is_array($merchants));
    $this->assertNotEmpty($merchants);
	}

	public function testGetMerchantsSchemaShouldReturnMerchantsSchema()
	{
    \VCR\VCR::insertCassette('testGetMerchantsSchemaShouldReturnMerchantsSchema');

		$a = $this->getIceberg();
		$merchantSchema = $a->get_schema("merchant");
    $this->assertInstanceOf('stdClass', $merchantSchema);
    $this->assertNotNull($merchantSchema->allowed_detail_http_methods);
	}

	public function testgetCartShouldReturnACart()
	{
    \VCR\VCR::insertCassette('testgetCartShouldReturnACart');

		$a = $this->getIceberg();
		$cart = $a->get("cart");
		$this->assertArrayHasKey("id", (array)$cart);
	}

	public function testgetCartItemsShouldReturnCartItems()
	{
    \VCR\VCR::insertCassette('testgetCartItemsShouldReturnCartItems');

		$a = $this->getIceberg();
		$cart = $a->get("cart");
		$items = $cart->getItems();
		$this->assertTrue(is_array($cart->items));
	}

	public function testinDebugModeCartShouldReturnDebugEqualToTrue()
	{
    \VCR\VCR::insertCassette('testinDebugModeCartShouldReturnDebugEqualToTrue');

		$a = $this->getIceberg();
		$a->setDebug(true);
		$cart = $a->create('cart');
		$this->assertTrue($cart->debug);
	}

	public function testAddCartItemShouldAddItem()
	{
    \VCR\VCR::insertCassette('testAddCartItemShouldAddItem');

		$a = $this->getIceberg();

		$my_cart = $a->get('Cart');
		$number_items = count($my_cart->getItems());
    $this->assertTrue($number_items==0);
		$my_cart->addItem(array(
			"offer_id" => 27254,
			"variation_id" => 60873,
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

	public function testNewCartItemShouldCreateANewCart()
	{
    \VCR\VCR::insertCassette('testNewCartItemShouldCreateANewCart1');
		$a = $this->getIceberg();
		$cart = $a->create('cart');
    \VCR\VCR::eject();

    \VCR\VCR::insertCassette('testNewCartItemShouldCreateANewCart2');
		$cart1 = $a->create('cart');
		$this->assertNotSame($cart1->id, $cart->id);
    \VCR\VCR::eject();
	}

	public function testgetAdressesShouldReturnAdresses()
	{
    \VCR\VCR::insertCassette('testgetAdressesShouldReturnAdresses');

		$a = $this->getIceberg();
		$addresses = $a->get_list("address");
		$this->assertTrue(is_array($addresses));
	}

	public function testgetCountryShouldReturnTheCountry()
	{
		\VCR\VCR::insertCassette('testgetCountryShouldReturnTheCountry');

		$a = $this->getIceberg();
		$country = $a->get("country");
		$this->assertEquals($country->code, 'FR');
	}

	public function testcreateAddressesShouldReturnACreatedAddress()
	{
    \VCR\VCR::insertCassette('testcreateAddressesShouldReturnACreatedAddress');

		$a = $this->getIceberg();
		$country = $a->get("country");
		$addr = $a->create("address", array(
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
		$this->assertArrayHasKey("id", (array) $addr);
		// We check that this address is well linked to the user
		$addresses = $a->get_list("address");
		$this->assertTrue(is_array($addresses));
	}

	public function testGetAddressShouldReturnAddress()
	{
    \VCR\VCR::insertCassette('testGetAddressShouldReturnAddress');

		$a = $this->getIceberg();
		$country = $a->get("country");
		$addr = $a->create("address", array(
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
		// We check that this address is well linked to the user
		$new_address = $a->get("address", $addr->id);
		$this->assertEquals($new_address->id, $addr->id);
	}

	public function testSaveObject()
	{
    \VCR\VCR::insertCassette('testSaveObject');

		$a = $this->getIceberg();
		$name = "random description 12345";
		$addresses = $a->get_list('address');
		$address = $addresses[0];
		$address->name = $name;
		$address->save();
		$address_check = $a->get("address", $address->id);
		$this->assertEquals($address->name, $address_check->name);
	}

	// MAIN FUNCTION TO TEST THE FULL ORDER PROCESS

	public function testFullOrderProcess()
	{
		\VCR\VCR::insertCassette('testFullOrderProcess');

    ini_set("memory_limit","1024M");
		$a = $this->getIceberg();
		// We get the first merchant
		$merchants = $a->get_list('merchant');
		$merchant = $merchants[0];
		$products = $merchant->getCatalog();
		$product = $products->product;
		$best_offer_id = (string) $product->best_offer->id;
		$i = 0;
		while ((int)$product->best_offer->variations->variation[$i]->stock === 0)
		{
			$i++;
		}
		$best_variation = (string) $product->best_offer->variations->variation[$i]->id;
		$my_cart = $a->get('Cart');
		$my_cart->addItem(array(
			"offer_id" => $best_offer_id,
			"variation_id" => $best_variation,
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
		$my_cart->setBillingAddress($address->id);
		$my_cart->setShippingAddress($address->id);
		$order = $my_cart->createOrder();
	  // Place the order
		$order->updateStatus('authorizeOrder');
		$this->assertEquals("60", $order->status);
	}
}
