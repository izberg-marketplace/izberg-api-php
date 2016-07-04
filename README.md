# IZBERG-API-PHP

[![Build Status](https://travis-ci.org/izberg-marketplace/izberg-api-php.svg?branch=master)](https://travis-ci.org/izberg-marketplace/izberg-api-php)

## About

PHP Wrapper around the IZBERG API

## Get started

[Create an account](https://account.izberg-marketplace.com) on IZBERG-Marketplace.
[Create an application](https://operator.izberg-marketplace.com) to be able to use this API.

## API DOCUMENTATION

You can found our [API documentation](http://izberg-marketplace.github.io/izberg-api-php-doc/) to help you.

### Initialize the class

You can use your access token or our Single Sign On (SSO) system to identify:

*IZBERG uses [psr-0](http://www.php-fig.org/psr/psr-0/) convention for autoload*

#### With Access token

```php
<?php
    $izberg = new Izberg\Izberg(array(
      'appNamespace' => 'YOUR_APP_NAMESPACE',
      'accessToken'  => 'YOUR_ACCESSTOKEN',
      'username'   	 => 'YOUR_USERNAME',
      'apiSecret'    => 'YOUR_APP_SECRET'
    ));

?>
```

#### With SSO

```php
<?php
    $izberg = new Izberg\Izberg(array(
      'appNamespace' => 'YOUR_APP_NAMESPACE',
      'apiSecret'    => 'YOUR_APP_SECRET'
    ));

    $izberg->sso(array(
      "email"     => "YOUR_EMAIL",
      "firstName" => "YOUR_ACCOUNT_FIRST_NAME",
      "lastName"  => "YOUR_ACCOUNT_LAST_NAME"
    ));

?>
```

#### Sandbox

To use our sandbox environment, just pass the param `sandbox` in options when you create your Izberg object:

```php
$izberg = new Izberg\Izberg(array(
  'appNamespace' => 'YOUR_APP_NAMESPACE',
  'sandbox'      => true,
));
```

**Note**: It will request a none https URL.

#### Authentication

By default, we authenticate the admin user with informations specified on initialization (`email`, `firstname`, `lastname`). If you want to authenticate a new user, you can do it using the `setUser()` function:

```php
$izberg->setUser(array(
  "email"      => "myemail@yahoo.fr",
  "first_name" => "seb",
  "last_name"  => "fie"
));
```

This is useful when you want to link your API calls to a user, you will need it during an order process.

## Resources

Basically, all resources are handled the same way, using the 5 same generic methods

 * You have access to the following resources directly through the main **Izberg** object:
     * Address
     * Brand
     * Cart
     * ApplicationCategory (Categories of your application)
     * Category (Categories created by IZBERG)
     * Country
     * Feed
     * Webhook
     * Merchant
     * Order
     * OrderItem
     * Webhook
     * LocaleConfig
     * MerchantOrder
     * Payment
     * User
     * Review
     * Message

### Instanciating resources


#### Get List

The `get_list()` method will return an array containing all the instanciated objects from the called resource.

```php
public function get_list($resource, $params = null, $accept_type = "Accept: application/json")
```

The first parameter is the resource's name, the second one are optional parameters, the last one is the accept type, for most of the action, you will only need the `$resource` parameter.

For example, the following will return the list of all the merchants on your marketplace.

```php
$merchant_list = $Izberg->get_list("merchant");
```

####Get

The `get()` method works like `get_list()`, but it returns only one object, you have to specify the object's id

```php
public function get($resource, $id, $params = null, $accept_type = "Accept: application/json")
```

For example, the following will return the cart object with ID '963':

```php
$my_cart = $Izberg->get("cart", 963);
```

####Create

The `create()` method will create a new element of the specified resource

```php
public function create($resource, $params = null, $accept_type = "Accept: application/json")
```

`$name` is the resource's name and `$params` are the object you want to create (`$params` can be either an object or an array)

The following example will create a new address:

```php
$my_adress = $Izberg->create(
  "address", array(
    "address" => "ADDRESS LINE 1",
    "address2" => "ADDRESS LINE 2",
    "city" => "CITY NAME",
    "company" => "COMPANY", // Optional
    "country" => "COUNTRY_ID",
    "default_billing" => true,
    "default_shipping" => true,
    "digicode" => null,
    "first_name" => "FIRST NAME",
    "floor" => null,
    "last_name" => "LAST NAME",
    "name" => "ADDRESS NAME",
    "phone" => "PHONE NUMBER",
    "state" => "STATE NAME", // Optional
    "status" => 10,
    "zipcode" => "ZIPCODE"
	)
);
```

####Update

The `update()` method will update one element from a specified resource

```php
public function update($resource, $id, $params = null, $accept_type = "Accept: application/json")
```

`$name` is the resource's name, `$id` is the object's id and `$params` are the fields you want to update.

The following example will update an existing merchant

```php
$my_merchant = $Izberg->update("merchant", 15, array("description" => "An updated merchant"));
```

###Resources specific methods

Each object returned by the handling methods can use both the save and delete functions

####Save

Save the current object

```php
$merchant = $Izberg->get("merchant", 15);
$merchant->description = "An Updated Merchant";
$merchant->save();
```

####Delete

Deletes an element from a specific resource

```php
$address = $izberg->get("address", 963)
$address->delete();
```

## Order Process

### Simple Order

Creating an order on IZBERG is really easy, the only thing you need is the Item ID, and your customer's informations.

```php
<?php
	$valid_array = array(
			'appNamespace' => 'YOUR_APP_NAMESPACE',
			'accessToken'  => 'YOUR_ACCESSTOKEN',
			'username'     => 'YOUR_USERNAME',
			'apiSecret'    => 'YOUR_APP_SECRET'
			)

	$IzbergInstance = new Izberg\Izberg($valid_array);

	$IzbergInstance->setUser(array(
				"email" => "EMAIL_ADDRESS",
				"first_name" => "FIRST_NAME",
				"last_name" => "LAST_NAME"
				));
```

Now that we have set the User informations, we can add the offer to the cart.

```php
$id_offer = "MY OFFER ID";
$quantity = "MY OFFER QUANTITY";

$my_cart = IzbergInstance->get('cart');
$my_cart->addItem(array(
  'offer_id' => $id_offer,
  'quantity' => (int)$quantity,
));
```

You have to use `Cart::addItem()` for each different offer you want to add to your cart.

We need the `country_id` in in order to set the customer's address (Default value is "FR").

```php
$country = $IzbergInstance->get('country');
```

Now we can set the Shipping and Billing addresses.

```php
$address = $IzbergInstance->create('address', array(
  "address" => "ADDRESS LINE 1",
  "address2" => "ADDRESS LINE 2",
  "city" => "CITY NAME"
  "company" => "OPTIONNAL COMPANY NAME",
  "country" => "/v1/country/" . $country->id . "/",
  "default_billing" => true,
  "default_shipping" => true,
  "digicode" => null,
  "first_name" => "FIRST NAME",
  "floor" => null,
  "last_name" => "LAST NAME",
  "name" => "ADDRESS NAME",
  "phone" => "PHONE NUMBER",
  "state" => "OPTIONNAL STATE NAME",
  //STATUS | 0 : INACTIVE | 10 : ACTIVE | 90 : HIDDEN
  "status" => 10,
  "zipcode" => "ZIPCODE"
));

$my_cart->setBillingAddress($address->id);
$my_cart->setShippingAddress($address->id);
```

Now that both addresses are set, we can place the order.

```php
$order = $my_cart->createOrder();
$order->updateStatus('authorizeOrder');
```

## Webhook

### Create

```php
$params = array(
  'url' => "http://create.com",
  'event' => 'merchant_order_confirmed',
);
$hook = $izberg->create("webhook", $params);
```

### Get and Update

```php
$webhook_id = 1046;
$hook = $izberg->get("webhook", $webhook_id);
$hook->url = "http://update.com";
$hook = $hook->save();
```

## Locale

### GET

```php
$a = $this->getIzberg();
$locale = $a->get("localeConfig");
```

### Update and reset using delete

```php
$locale->update(array("languages" => ["fr","it"]));
$this->assertEquals($locale->languages, ["fr","it"]);
$locale->delete();
```

## Coupons

####APPLY

```php

  $a = $this->getIzberg();
	$cart = $a->create("cart");
  $cart->discountCode("code1234","add"); // "add" (default) or "remove" action

```

### Documentation

To generate doc, we use [apigen](http://www.apigen.org/) , with this command:

`apigen generate --source lib --destination doc`

## Run tests
- Install php unit : http://phpunit.de/getting-started.html
- cd /to/the/izberg/php/library/folder
- run 'USERNAME1=sebfie TOKEN1=156d219e38f84953c159a857738119bc0c35de96 API_SECRET_KEY=6cb0c550-9686-41af-9b5e-5cf2dc2aa3d0 phpunit --debug tests'
