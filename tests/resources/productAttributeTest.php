<?php
class productAttributeTest extends BaseTester
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
  public function testGetProductAttributeReturnAttributes()
  {
    \VCR\VCR::insertCassette('testGetProductAttributeReturnAttributes');

    $a = $this->getIzberg(array(
        "appNamespace" => "homecare",
        "username" => "sebastien_fieloux",
        "accessToken" => "8a574f849e1d59a04aa696804cc728771c5686fc",
        "apiSecret" => "6d8f3910-10f4-4e99-8a21-062462141e2b",
        "sandbox" => true
    ));

    $productAttributes = $a->get_list("productAttribute");
    $this->assertEquals(count($productAttributes), 22);
  }
}
