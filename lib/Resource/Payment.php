<?php
namespace Izberg\Resource;
use Izberg\Resource;

class Payment extends Resource
{
  /**
  * Make pending payment Authorization
  * @return Payment
  */
  public function pending(){
		$response = parent::$Izberg->Call( $this->getName() . '/'. $this->id . "/pending_authorization/", 'POST', null, 'Content-Type: application/json');
		$this->hydrate($response);
		return $this;
	}
}
