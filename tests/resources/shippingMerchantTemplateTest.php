<?php
class ShippingMerchantTemplateTest extends BaseTester
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

  public function testGetShippingMerchantTemplate()
	{
		// ini_set("memory_limit","4024M");
    // \VCR\VCR::insertCassette('testGetShippingMerchantTemplate');
		//
		// $a = $this->getShippingIzberg();
		// $merchant_templates = $a->get_list("shippingMerchantTemplate");
    // $this->assertEquals(count($merchant_templates), 0);
	}

}
