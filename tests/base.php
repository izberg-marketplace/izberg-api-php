<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

abstract class BaseTester extends PHPUnit_Framework_TestCase
{

  public function getShippingIzberg()
  {
    return $this->getIzberg(array(
        "appNamespace" => "XX",
        "username" => "XX",
        "accessToken" => "XX",
        "apiSecret" => "XXX",
        "sandbox" => true
    ));
  }
  public function getIzberg($options = array(), $extra_mocks_methods = array())
	{
		if (empty($options)) {
			$options = array(
					"appNamespace" => "XX",
					"username" => getenv("USERNAME1"),
					"accessToken" => getenv("TOKEN1"),
          "apiSecret" => getenv("API_SECRET_KEY"),
					"sandbox" => true
			);
		}
		$mock = $this->getMockBuilder('Izberg\Izberg')
                 ->setMethods(array_merge(array('setTimestamp', 'getTimestamp', 'setNonce','getNonce','log'),$extra_mocks_methods))
                 ->setConstructorArgs(array($options))
                 ->getMock();

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
	    ->will($this->returnValue(XX));

		return $mock;
	}

	public function sso(&$a)
	{
		$a->sso(array(
      "email"     => "myemail@yahoo.fr",
			"apiKey"    => "XXX",
			"apiSecret" => "XXX",
      "firstName" => "my_firstname",
      "lastName"  => "my_lastname"
    ));
	}

}
