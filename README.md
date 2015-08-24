#Izberg-API-PHP

[![Build Status](https://travis-ci.org/Izberg-Marketplace/izberg-api-php.svg?branch=master)](https://travis-ci.org/Izberg-Marketplace/izberg-api-php)
## About

PHP Wrapper around the Izberg API

## Get started


[Create an account](http://account.izberg-marketplace.com) on Izberg-Marketplace.
[Create an application](http://dashboard.izberg-marketplace.com) to be able to use this api.


### Initialize the class

You can use your access token or our Single Sign On system to identify:

#### With Access token :

```php
<?php
    require_once 'izberg.php';

    $izberg = new Izberg(array(
      'appNamespace' => 'YOUR_APP_NAMESPACE',
      'accessToken'  => 'YOUR_ACCESSTOKEN',
      'username'   	 => 'YOUR_USERNAME',
      'apiKey'       => 'YOUR_APP_KEY',
      'apiSecret'    => 'YOUR_APP_SECRET'
    ));

?>
```

#### With SSO :

```php
<?php
    require_once 'izberg.php';

    $izberg = new Izberg(array(
      'appNamespace' => 'YOUR_APP_NAMESPACE',
      'apiKey'       => 'YOUR_APP_KEY',
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

To use our sandbox environment, just pass the param `sandbox` in options when you create your izberg object :

```php
<?php
    require_once 'izberg.php';

    $izberg = new Izberg(array(
      'appNamespace' => 'YOUR_APP_NAMESPACE',
      'sandbox'      => true,
    ));

?>
```

**Note** : It will request a none https url.

#### Authentification

By default we authenticate the admin user with informations specified on initialization (email, firstname, lastname). If you want to authenticate a new user, you can do it using the setUser function :

```php
<?php

  $izberg->setUser(array(
    "email"      => "myemail@yahoo.fr",
    "first_name" => "seb",
    "last_name"  => "fie"
  ));
?>
```

This is useful when you want to link your api calls to a user, you will need it during an order process.


##Ressources

Basically, all ressources are handled the same way, using the 5 same generic methods

 * You have access to the following resources directly through the main **Izberg** object:
     * Address
     * Brand
     * Cart
     * Category
     * Country
     * Feed
     * Webhook
     * Merchant
     * Order
     * Order


     * MerchantOrder
     * Payment
     * User
     * Review
     * Message

###Instanciating resources


####Get List

The get_list() method will return an array containing all the instanciated objects from the called resource.

```php

public function get_list($resource, $params = null, $accept_type = "Accept: application/json")
```

The first parameter is the ressource's name, the second one are the eventual parameters, the last one is the accept type, for most of the action, you will only need the $resource parameter

For exemple, the following will return the list of all the merchants on your marketplace.

```php

$merchant_list = $Izberg->get_list("merchant");

```

####Get

The get() method works like get_list(), but it returns only one object, you have to specify the object's id

```php

public function get($resource, $id, $params = null, $accept_type = "Accept: application/json")
```

For exemple, the following will return the cart object of id '963'

```php

$my_cart = $Izberg->get("cart", 963);
```

####Create

The create() method will create a new element of the specified ressource

```php

public function create($resource, $params = null, $accept_type = "Accept: application/json")
```

$name is the ressource's name and $params are the object you want to create ($params can be either an object or an array)

The following example will create a new address

```php

$my_adress = $Izberg->create("address", array(
						"address" => "ADDRESS LINE 1",
						"address2" => "ADDRESS LINE 2",
						"city" => "CITY NAME",
						"company" => "OPTIONNAL COMPANY",
						"country" => "COUNTRY_ID",
						"default_billing" => true,
						"default_shipping" => true,
						"digicode" => null,
						"first_name" => "FIRST NAME",
						"floor" => null,
						"last_name" => "LAST NAME",
						"name" => "ADDRESS NAME",
						"phone" => "PHONE NUMBER",
						"state" => "OPTIONNAL STATE NAME",
						"status" => 10,
						"zipcode" => "ZIPCODE"
			)
		);
```

####Update

The update() method will update one element from a specified ressource

```php

public function update($resource, $id, $params = null, $accept_type = "Accept: application/json")
```

$name is the ressource's name, $id is the object's id and $params are the fields you want to update.

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

Deletes an element from a specific ressource

```php

	$address = $izberg->get("address", 963)
	$address->delete();

```

## Order Process

### Simple Order

Creating an order on Izberg is really easy, the only thing you need is the Item ID, and your customer's informations.

```php

<?php

	require_once "izberg.php";

	$valid_array = array(
			'appNamespace' => 'YOUR_APP_NAMESPACE',
			'accessToken'  => 'YOUR_ACCESSTOKEN',
			'username'     => 'YOUR_USERNAME',
			'apiKey'       => 'YOUR_APP_KEY',
			'apiSecret'    => 'YOUR_APP_SECRET'
			)

	$IzbergInstance = new Izberg($valid_array);

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

You have to use Cart::addItem() for each different offer you want to add to your cart.

We need the country_id in in order to set the customer's address (Default value is "FR").

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

?>

```

## Webhook

####Create

```php

    $params = array(
        'url' => "http://create.com",
        'event' => 'merchant_order_confirmed',
    );
    $hook = $izberg->create("webhook", $params);

```

####Get and Update

```php

	$webhook_id = 1046;
	$hook = $izberg->get("webhook", $webhook_id);

    $hook->url = "http://update.com";
    $hook = $hook->save();

```

### Run tests

- Install php unit : http://phpunit.de/getting-started.html
- cd /to/the/izberg/php/library/folder
- run 'USERNAME1=sebfie TOKEN1=156d219e38f84953c159a857738119bc0c35de96 phpunit --debug tests/IzbergTest.php'
