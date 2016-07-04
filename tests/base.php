<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

abstract class BaseTester extends PHPUnit_Framework_TestCase
{

  public function getShippingIzberg()
  {
    return $this->getIzberg(array(
        "appNamespace" => "sebastien2",
        "username" => "sebastien_fieloux",
        "accessToken" => "9f89c1230c03f89c8e07ec2d702e841bdd3a30a7",
        "apiSecret" => "3fec10fc-26db-44c2-b366-aeac8e8e526d ",
        "sandbox" => true
    ));
  }
  public function getIzberg($options = array(), $extra_mocks_methods = array())
	{
		if (empty($options)) {
			$options = array(
					"appNamespace" => "lolote",
					"username" => getenv("USERNAME1"),
					"accessToken" => getenv("TOKEN1"),
          "apiSecret" => getenv("API_SECRET_KEY"),
					"sandbox" => true
			);
		}
		$mock = $this->getMock('Izberg\Izberg', array_merge(array('setTimestamp', 'getTimestamp', 'setNonce','getNonce','log'),$extra_mocks_methods), array($options));

		$mock->expects($this->any())
	    ->method('setTimestamp')
	    ->will($this->returnValue(true));

		$mock->expects($this->any())
	    ->method('getTimestamp')
	    ->will($this->returnValue(1439912480));

		$mock->expects($this->any())
	    ->method('setNonce')
	    ->will($this->returnValue(true));

		$mock->expects($this->any())
	    ->method('getNonce')
	    ->will($this->returnValue(1439912482));

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

}
