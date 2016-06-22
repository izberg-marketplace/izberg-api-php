<?php

/**
 * Izberg API class
 * API Documentation: http://developers.modizy.com/documentation/
 * Class Documentation: https://github.com/Modizy/Izberg-API-PHP
 *
 * @author Sebastien FIELOUX
 * @since 30.10.2011
 * @copyright Modizy.com 2014
 * @version 2.0
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 */
namespace Izberg;

require_once __DIR__."/Html2Text/Html2Text.php";

class Izberg
{

	/**
	* Use logger
	*/
	const LOGS = true;

	/**
	* The API production URL
	*/
	const PRODUCTION_API_URL = 'https://api.iceberg.technology/v1/';

	/**
	* The API sandbox URL
	*/
	const SANDBOX_API_URL = 'https://api.sandbox.iceberg.technology/v1/';

	/**
	* The Single Sign On URL
	*/
	const SINGLE_SIGN_ON_URL = 'user/sso/';

	/**
	* The default currency
	*/
	const DEFAULT_CURRENCY = 'EUR';

	/**
	* The Default shipping country
	*/
	const DEFAULT_SHIPPING_COUNTRY = 'FR';

	/**
	* The Default shipping country
	*/
	const DEFAULT_LOCALE = 'fr';


	/**
	* The singleton of Izberg instance
	*
	* @var Izberg
	*/
	protected static $_singleton;

	/**
	* The singleton of Izberg\Helper instance
	*
	* @var Izberg\Helper
	*/
	protected static $_helper;

	/**
	* The API base URL
	* @var String
	*/
	protected static $_api_url;

	/**
	* The izberg application namespace
	*
	* @var string
	*/
	private $_appnamespace;

	/**
	* The izberg api secret
	*
	* @var string
	*/
	private $_apisecret;


	/**
	* The izberg application access_token
	*
	* @var string
	*/
	private $_access_token;


	/**
	* The user email
	*
	* @var string
	*/
	private $_email;

	/**
	* The username
	*
	* @var string
	*/
	private $_username;

	/**
	* The request timestamp
	*
	* @var string
	*/
	private $_timestamp;


	/**
	* The request nonce
	*
	* @var string
	*/
	private $_nonce;


	/**
   * Anonymous
   *
   * @var boolean
   */
  private $_anonymous;

	/**
	* The user first name
	*
	* @var string
	*/
	private $_first_name;

	/**
	* The user last name
	*
	* @var string
	*/
	private $_last_name;

	/**
	* The user shipping country
	*
	* @var string
	*/
	private $_shipping_country;

	/**
	* The application locale
	*
	* @var string
	*/
	private $_locale;

	/**
	* The user currency
	*
	* @var string
	*/
	private $_currency;

	/**
	* The single sign on response
	*
	* @var array
	*/
	private $_single_sign_on_response;

	/**
	* Debug mode
	*
	* @var boolean
	*/
	private $_debug;

	public function getDebug()
	{
		return $this->_debug;
	}


	/**
	* API-secret Getter
	*
	* @return String
	*/
	public function getApiSecret() {
		return $this->_apisecret;
	}

	/**
	* Access token Getter
	*
	* @return String
	*/
	public function getAccessToken() {
		return $this->_access_token;
	}

	/**
	* NAMESPACE Getter
	*
	* @return String
	*/
	public function getAppNamespace() {
		return $this->_appnamespace;
	}

	/**
	* Email Getter
	*
	* @return String
	*/
	public function getEmail() {
		return $this->_email;
	}

	/**
	* First name Getter
	*
	* @return String
	*/
	public function getFirstName() {
		return $this->_first_name;
	}

	/**
	* Last name Getter
	*
	* @return String
	*/
	public function getLastName() {
		return $this->_last_name;
	}

	/**
	* Username Getter
	*
	* @return String
	*/
	public function getUsername() {
		return $this->_username;
	}

	/**
	* Currency Getter
	*
	* @return String
	*/
	public function getCurrency() {
		return $this->_currency;
	}

	/**
	* Shipping Country Getter
	*
	* @return String
	*/
	public function getShippingCountry() {
		return $this->_shipping_country;
	}

	public static function getApiUrl()
	{
		return self::$_api_url;
	}


	/**
	* Timestamp Getter
	*
	* @return String
	*/
	public function getTimestamp() {
		return $this->_timestamp;
	}

	/**
	* Nonce Getter
	*
	* @return String
	*/
	public function getNonce() {
		return $this->_nonce;
	}

	/**
	* Locale Getter
	*
	* @return String
	*/
	public function getLocale() {
		return $this->_locale;
	}

	/**
	* Message Auth Getter
	*
	* @return String
	*/
	public function getMessageAuth($email, $first_name, $last_name)
	{
		$this->setTimestamp(time());
		$to_compose = array($email, $first_name, $last_name, $this->getTimestamp());
		if (is_null($this->getApiSecret())) {
			throw new Exception\GenericException("To use SSO you have to set the api_secret");
		}
		$message_auth = hash_hmac('sha1', implode(";", $to_compose), $this->getApiSecret());
		return $message_auth;
	}


	/**
	* Request signature
	*
	* @return String
	*/
	public function getRequestSignature($body)
	{
		$this->setNonce(time());
		$nonce = $this->getNonce();
		$to_compose = array($body, $nonce);
		if (is_null($this->getApiSecret())) {
			throw new Exception\GenericException("To sign your requests, you need a api secret");
		}
		return hash_hmac('sha1', implode(":", $to_compose), $this->getApiSecret());
	}

	/**
	* Username Getter
	*
	* @return String
	*/
	public function setUsername($username)
	{
		$this->_username = $username;
	}

	/**
	* Access token Setter
	*
	* @return String
	*/
	public function setAccessToken($access_token)
	{
		$this->_access_token = $access_token;
	}

	/**
	* API-secret Setter
	*
	* @param string $apiSecret
	* @return void
	*/
	public function setApiSecret($apiSecret)
	{
		$this->_apisecret = $apiSecret;
	}

	/**
	* NAMESPACE Setter
	*
	* @param string $namespace
	* @return void
	*/
	public function setAppNamespace($namespace)
	{
		$this->_appnamespace = $namespace;
	}

	/**
	* Email Setter
	*
	* @param string $email
	* @return void
	*/
	public function setEmail($email)
	{
		$this->_email = $email;
	}

	/**
	* get current authenticated user
	*
	* @return StdObject
	*/
	public function getUser()
	{
		return $this->get("user");
	}

	/**
	* Use this user for current connection
	*
	* @return null
	*/
	public function setUser($params)
	{
		$this->_single_sign_on_response = $this->_getSingleSignOnResponse($params);
		$this->current_user = $this->getUser();
		$this->setAccessToken($this->_single_sign_on_response->access_token);
		$this->setUsername($this->_single_sign_on_response->username);
		if ($this->_single_sign_on_response->username != "Anonymous") {
			$this->_anonymous = false;
		} else {
			$this->_anonymous = true;
		}

		return $this->_single_sign_on_response;
	}

	/**
	* First name Setter
	*
	* @param string $firstname
	* @return void
	*/
	public function setFirstName($firstname)
	{
		$this->_first_name = $firstname;
	}

	/**
	* Last name Setter
	*
	* @param string $lastname
	* @return void
	*/
	public function setLastName($lastname)
	{
		$this->_last_name = $lastname;
	}

	/**
	* Currency Setter
	*
	* @param string $currency
	* @return void
	*/
	public function setCurrency($currency)
	{
		$this->_currency = $currency;
	}

	/**
	* Shipping country Setter
	*
	* @param string $shippingCountry
	* @return void
	*/
	public function setShippingCountry($shippingCountry)
	{
		$this->_shipping_country = $shippingCountry;
	}


	/**
	* Timestamp Setter
	*
	* @param string $timestamp
	* @return String
	*/
	public function setTimestamp($timestamp)
	{
		$this->_timestamp = $timestamp;
	}

	/**
	* Nonce Setter
	*
	* @param string $timestamp
	* @return String
	*/
	public function setNonce($nonce)
	{
		$this->_nonce = $nonce;
	}

	/**
	* Debug Setter
	*
	* @param string $debug
	* @return String
	*/
	public function setDebug($debug)
	{
		$this->_debug = $debug;
	}


	/**
	* Default constructor
	*
	* @param array|string $config          Izberg configuration data
	* @return void
	*/
	public function __construct($config)
	{
		$this->_debug = false;
		if (true === is_array($config)) {
			$this->helper = new Helper();

			self::$_api_url = (isset($config['sandbox']) && $config['sandbox'] === true) ? self::SANDBOX_API_URL : self::PRODUCTION_API_URL;
			self::$_api_url = (isset($config['apiUrl']))? $config['apiUrl']: self::$_api_url;

			$this->_locale = isset($config["locale"]) ? $config["locale"] : self::DEFAULT_LOCALE;

			if (isset($config['accessToken'])) {
				$this->setAccessToken($config['accessToken']);
				$this->setUsername($config['username']);
			}

			$this->_anonymous = (isset($config["anonymous"]) && $config["anonymous"] == true) ? true : false;

			if (isset($config['apiSecret']))
				$this->setApiSecret($config['apiSecret']);
			if (isset($config['appNamespace']))
				$this->setAppNamespace($config['appNamespace']);
			(isset($config['currency'])) ? $this->setCurrency($config['currency']) : $this->setCurrency(self::DEFAULT_CURRENCY);
			(isset($config['shippingCountry'])) ? $this->setShippingCountry($config['shippingCountry']) : $this->setShippingCountry(self::DEFAULT_SHIPPING_COUNTRY);

			// We save this instance as singleton
			self::setInstance($this);
			Resource::setIzberg($this);

		} else {
			throw new Exception\GenericException("Error: __construct() - Configuration data is missing.");
		}
	}

	public function sso($config)
	{
		// if you want to access user data
    if (isset($config['apiSecret']))
      $this->setApiSecret($config['apiSecret']);
    if (isset($config['appNamespace'])) $this->setAppNamespace($config['appNamespace']);
    $this->setEmail( isset($config['email']) ? $config['email'] : "");
    $this->setFirstName( isset($config['firstName']) ? $config['firstName'] : "");
    $this->setLastName( isset($config['lastName']) ? $config['lastName'] : "");

		// We get the izberg api key using the Single Sign On API
    return $this->setUser(array(
      "email" => isset($config['email']) ? $config['email'] : "",
      "first_name" => isset($config['firstName']) ? $config['firstName'] : "",
      "last_name" => isset($config['lastName']) ? $config['lastName'] : "",
      "from_session_id" => isset($config['from_session_id']) ? $config['from_session_id'] : null,
    ));
	}

	/**
	* Static function to get the last validated Instance
	*
	* @return Izberg
	*/
	public static function getInstance()
	{
		if (self::$_singleton) {
			return self::$_singleton;
		} else {
			throw new Exception\GenericException("You should create a first validated Izberg instance");
		}
	}

	/**
	* Set the default instance to a specified instance.
	*
	* @param Izberg $izberg An object instance of type Izberg,
	*   or a subclass.
	* @return void
	*/
	public static function setInstance(Izberg $izberg)
	{
		self::$_singleton = $izberg;
	}

	/**
	* Get helper instance
	* @return Izberg\Helper
	*/
	public function getHelper()
	{
		return $this->helper;
	}

	/**
	* The Log Function
	*
	* @param string $Message               Your log message
	* @param string [optional]             Log type (default is "ERROR")
	* @param string [optional]             Directory path for logs, CWD by default
	**/
	public function log($message, $level="error", $path = null)
	{
		// date_default_timezone_set("Europe/Paris");
		if (false === self::LOGS)
			return ;

		if (false === is_dir($path)) {
			$path = __DIR__."/log/";
			if (!is_dir($path)) mkdir($path);
		} else if (substr($path, -1) != '/')
			$path .= '/';

		file_put_contents($path."log-".$level."-".date("m-d").".txt", date("H:i:s | ").$message."\n", FILE_APPEND);
	}

	/**
	 * Get the inline user token
	 * @return String
	 */
	public function getInlineUserToken()
	{
		if ($this->_anonymous) {
			$h = 'IcebergAccessToken anonymous:'. $this->getAppNamespace() . ":" . $this->getAccessToken();
		} else {
			$h = 'IcebergAccessToken '. $this->getUsername() . ":" . $this->getAccessToken();
		}
		return "Authorization: " . $h;
	}

	/**
	 * Get the current application
	 * @return Resource/Application
	 */
	public function getCurrentApplication()
	{
		return $this->get("application", null, null, "Accept: application/json", "application/mine");
	}

	/**
	* The call operator
	*
	* @param string $function              API resource path
	* @param array [optional] $params      Additional request parameters
	* @param boolean [optional] $auth      Whether the function requires an access token
	* @param string [optional] $method     Request type GET|POST
	* @return mixed
	*/
	public function Call($path, $method = 'GET', $params = null, $accept_type = 'Accept: application/json', $content_type = 'Content-Type: application/json; charset=UTF-8', $output_file = null)
	{
		if (!is_null($params) && is_array($params) && $accept_type == "Content-Type: application/json")
		{
			$paramString = json_encode($params);
		}
		else if (!is_null($params) && is_array($params) && !empty($params)) {
			$paramString = '?' . http_build_query($params);
		} else {
			$paramString = null;
		}

		$apiCall = self::$_api_url . $path . (('GET' === $method) ? $paramString : null);

    $headers = array(
      $content_type,
      $accept_type,
			"Content-Language: " . $this->getLocale(),
      $this->getInlineUserToken()
    );

		$ch = curl_init();

		if ($this->getDebug()) {
			curl_setopt($ch, CURLOPT_VERBOSE, true);
		}

		$body = ltrim(ltrim($paramString, '&'), '?');
		if ('POST' === $method)
		{
			curl_setopt($ch, CURLOPT_POST, count($params));
			if (ltrim(ltrim($paramString, '&'), '?') != "") {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			}
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		} else if ('DELETE' === $method) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		} else if ('PUT' === $method) {
			curl_setopt($ch, CURLOPT_POST, count($params));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		}

		if ($output_file) {
			$fp = fopen ($output_file, 'w+');
			curl_setopt($ch, CURLOPT_FILE, $fp);
		} else {
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}

		// We sign the request
		$signature = $this->getRequestSignature($body);
		$nonce = $this->getNonce();
		array_push($headers, "Application-Signature: $signature");
		array_push($headers, "Application-Nonce: $nonce");

		curl_setopt($ch, CURLOPT_URL, $apiCall);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1");
		// curl_setopt($ch, CURLOPT_PROXYPORT, 8888);

		$data = $this->curlExec($ch);
		// We log request
		$this->log($method." | ".$apiCall, "call");
		if (!is_null($paramString)) {
			$this->log("PARAMS | ".ltrim(ltrim($paramString, '&'), '?'), "call", __DIR__."/log");
		}
		$this->log("DATE | ".$data, "call");

		if (false === $data) {
			throw new Exception\GenericException("Error: Call() - cURL error: " . curl_error($ch));
		}
		$http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code >= 400) {
      // We raise only on http code > 400
      throw new Exception\GenericException ("We got an response with code " . $http_code . " and response " . $data . " from url: " .$apiCall . " and headers : " . json_encode($headers) . " and params: " . $paramString );
    }
		curl_close($ch);
		return ($accept_type == 'Accept: application/json' || $accept_type == 'Content-Type: application/json') ? json_decode($data) : (($accept_type == 'Accept: application/xml') ?  simplexml_load_string($data) : $data);
	}

	/**
	* Api key Getter
	*
	* @return String
	*/
	protected function _getSingleSignOnResponse($params = null)
	{

		if(is_null($params)) {
			$params = array(
				"email" => $this->getEmail(),
				"first_name" => $this->getFirstName(),
				"last_name" => $this->getLastName()
			);
		}

		$params["message_auth"] = $this->getMessageAuth($params["email"], $params["first_name"], $params["last_name"]);
		$params["application"] = $this->getAppNamespace();
		$params["timestamp"] = $this->getTimeStamp();

		$apiCall = self::$_api_url . self::SINGLE_SIGN_ON_URL . "?" . http_build_query($params);

		$headers = array(
			'Accept: application/json',
			'Authorization: '. $this->getMessageAuth($params["email"],$params["first_name"],$params["last_name"])
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiCall);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		// curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1");
		// curl_setopt($ch, CURLOPT_PROXYPORT, 8888);

		$jsonData = $this->curlExec($ch);
		// list($headers, $jsonData) = explode("\r\n\r\n", $jsonData, 2);

		$httpcode = $this->curlGetInfo($ch, CURLINFO_HTTP_CODE);

		if (false === $jsonData) {
			throw new Exception\GenericException("Error: _getSingleSignOnResponse() - cURL error: " . curl_error($ch));
		}
		$http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		$jsonResponse = json_decode($jsonData);
		// We display the error only if the HTTP code is different of 200..300
		if (preg_match("/2\d{2}/", $httpcode)  == 0) {
			$message = "Error: from Iceberg API - error: " . print_r($jsonResponse,true) . " with params: " . json_encode($params);
			switch ($httpcode){
				case '400':
					throw new Exception\BadRequestException($message);
					break;
				case '401':
					throw new Exception\UnauthorizedException($message);
					break;
				case '403':
					throw new Exception\ForbiddenException($message);
					break;
				case '404':
					throw new Exception\NotFoundException($message);
					break;
				case '405':
					throw new Exception\MethodNotAllowedException($message);
					break;
				case '500':
					throw new Exception\InternalErrorException($message);
					break;
				default:
					throw new Exception\GenericException($message);
					break;
			}
		}
		return $jsonResponse;
	}

	// curl functions
	protected function curlExec($ch)
	{
		return curl_exec($ch);
	}

	protected function curlGetInfo($ch, $name)
	{
		return curl_getinfo($ch, $name);
	}


	/**
	* Test if AcessToken is valid
	*
	* @returns object
	*
	**/
	public function testIzbergToken()
	{
		try
		{
			$result = $this->Call('user/me/');
		}
		catch (Exception\GenericException $e)
		{
			$result = false;
		}
		if (isset($result->id) && $result->id == 0)
			$result = false;
		return ($result);
	}

	/**
	* Converts html string to simple string
	*
	* @returns string
	*
	**/
	public static function convertHtml($html)
	{
		$converter = new \Html2Text\Html2Text($html);
		return $converter->getText();
	}

	/**
	* Factory method, use it to build resources
	*
	* @returns object
	*
	**/
	public function get($resource, $id = null, $params = null, $accept_type = "Accept: application/json", $endpoint = null)
	{
		if (strtolower($resource) == "cart" && !$id)
			$id = "mine";
		if (strtolower($resource) == "user" && !$id)
			$id = "me";
		if (strtolower($resource) == "country" && !$params)
			$params = array("code" => "FR");
		if (strtolower($resource) == "return" && !$params)
			$resource = "ReturnRequest";

		$resource = "Izberg\Resource\\".ucfirst($resource);

		$object = new $resource();
		if (!$endpoint)
			$endpoint =  $object->getName();
		if ($id)
			$response = $this->Call($object->getPrefix() . $endpoint."/".$id."/", 'GET', $params, $accept_type);
		else
			$response = $this->Call($object->getPrefix() . $endpoint."/", 'GET', $params, $accept_type);
		if ($accept_type != "Accept: application/json") return $response;
		$object->hydrate($response);
		return $object;
	}


	/**
	* Factory method, use it to get response to build resources
	*
	* @returns object
	*
	**/
	public function get_list_response($resource, $params = null, $accept_type = "Accept: application/json", $url = null)
	{
		$resource = "Izberg\Resource\\" . ucfirst($resource);
		$object = new $resource();
		// If we override the get_list method
		if (method_exists($resource, "get_list") && !$url) {
			return $object->get_list($params, $accept_type);
		} else {
			// If we specified url
			if (is_null($url)) {
				$url = $object->getPrefix() . $object->getName()."/";
			}
			return $this->Call($url, 'GET', $params, $accept_type);
		}
	}

	/**
	* Factory method, use it to build resources
	*
	* @returns object
	*
	**/
	public function get_list($resource, $params = null, $accept_type = "Accept: application/json", $url = null)
	{
		$list = $this->get_list_response($resource, $params, $accept_type, $url);
		$object_list = array();
		$resource = "Izberg\Resource\\".ucfirst($resource);
		foreach ($list->objects as $object)
		{
			$obj = new $resource();
			$obj->hydrate($object);
			$object_list[] = $obj;
		}
		return $object_list;
	}

	/**
	* Factory method, use it to build resources
	*
	* @returns object
	*
	**/
	public function get_list_meta($resource, $params = null, $accept_type = "Accept: application/json")
	{
		$result = $this->get_list_response($resource, $params, $accept_type);
		return $result->meta;
	}

	/**
	* Factory method, use it to build resources
	*
	* @returns object
	*
	**/
	public function create($resource, $params = null, $accept_type = "Content-Type: application/json")
	{
		$resource = "Izberg\Resource\\".ucfirst($resource);
		if ($this->getDebug())
			$params['debug'] = 'true';
		$object = new $resource();
		$response = $this->Call($object->getPrefix() . $object->getName()."/", 'POST', $params, $accept_type);
		$object->hydrate($response);
		return $object;
	}

	/**
	* Updates Object
	*
	* @return Object
	*
	**/
	public function update($resource = null, $id = null, $params = null, $accept_type = "Content-Type: application/json")
	{
		if (!$id || !$resource)
			throw new Exception\GenericException(__METHOD__." needs a valid ID and a valid Resource Name");
		$resource = "Izberg\Resource\\".$resource;
		$object = new $resource();
		$name = $object->getName();
		$response = $this->Call($object->getPrefix() . $name . "/" . $id . "/", 'PUT', $params, $accept_type);
		$object->hydrate($response);
		return $object;
	}


	/**
	* Get Schema
	*
	* @return Object
	*
	**/
	public function get_schema($resource, $params = null, $accept_type = 'Accept: application/json')
	{
		$resource = "Izberg\Resource\\".$resource;
		$object = new $resource();
		return $this->Call($object->getPrefix() . $object->getName() ."/schema/", 'GET', $params, $accept_type);
	}
}
