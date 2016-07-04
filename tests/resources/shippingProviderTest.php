<?php
class ShippingProviderTest extends BaseTester
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

  public function testGetShippingProviders()
	{
    \VCR\VCR::insertCassette('testGetShippingProviders');

		$a = $this->getShippingIzberg();
		$providers = $a->get_list("shippingProvider");
    $this->assertEquals(count($providers), 4);
	}

	// TODO 401 response for the moment
  // public function testCreateShippingProviders()
	// {
  //   \VCR\VCR::insertCassette('testCreateShippingProviders');
	//
	// 	$a = $this->getShippingIzberg();
	// 	$provider = $a->create("shippingProvider", array(
	// 		"integrator" => false,
	// 		"method" => "sebfie",
	// 		"application" => "/v1/application/36/",
	// 		"zone" => "/v1/zone/5/"
	// 	));
  //   var_dump($provider);
	// }

  public function testGetShippingProvider()
	{
    \VCR\VCR::insertCassette('testGetShippingProvider');

		$a = $this->getShippingIzberg();
		$provider = $a->get("shippingProvider", 27);
    $this->assertEquals($provider->id, 27);
	}
}
