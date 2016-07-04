<?php
class ShippingProviderAssignmentTest extends BaseTester
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

  public function testGetShippingProviderAssignments()
	{
    \VCR\VCR::insertCassette('testGetShippingProviderAssignments');

		$a = $this->getShippingIzberg();
		$provider_assignments = $a->get_list("shippingProviderAssignment");
    $this->assertEquals(count($provider_assignments), 2);
	}

  public function testCreateShippingProviderAssignments()
	{
    \VCR\VCR::insertCassette('testCreateShippingProviderAssignments');

		$a = $this->getShippingIzberg();
		$provider_assignment = $a->create("shippingProviderAssignment",array(
			"merchant" => "/v1/merchant/1168/",
			"provider" => "/v1/shipping_provider/27/",
			"options" => array()
		));
    $this->assertNotNull($provider_assignment->id);
	}

}
