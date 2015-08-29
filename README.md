# ChargifyV2

[![Build Status](https://travis-ci.org/yurevichcv/ChargifyV2.svg?branch=master)](https://travis-ci.org/yurevichcv/ChargifyV2)
[![License](http://img.shields.io/packagist/l/yurevichcv/chargify-v2.svg)](https://github.com/yurevichcv/ChagifyV2/blob/master/LICENSE)
[![Latest Stable Version](http://img.shields.io/github/release/yurevichcv/chargify-v2.svg)](https://packagist.org/packages/yurevichcv/chargify-v2)
[![Total Downloads](http://img.shields.io/packagist/dt/yurevichcv/chargify-v2.svg)](https://packagist.org/packages/yurevichcv/chargify-v2)

PHP wrapper for Chargify API v2 which also includes helpers to work with Cargify Direct.

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install ChargifyV2.

```bash
$ composer require yurevichcv/chargify-v2
```

This will install ChargifyV2 and all required dependencies. ChargifyV2 requires PHP 5.5.0 or newer.

## Usage

### Instantiation
```php
$direct = new \ChargifyV2\DirectHelper(
    '{{your api_id}}',
    '{{your api_secret}}',
    '{{your redirect_url}}'
);
$direct->setData([
  'secureField1' => 'value1',
  'secureField2' => 'value2'
]);
```

### Sign up (card update) form
```phtml
<html>
<head>
    <title>Sign up form</title>
</head>
<body>
<form method="post" action="<?php echo $direct->getSignUpAction() ?>">
    <?php foreach ($direct->getSecureFields() as $name => $value): ?>
        <input type="hidden" name="secure[<?php echo $name ?>]" value="<?php echo $value ?>"/>
    <?php endforeach; ?>
    <!-- Other fields -->
    <input type="submit" value="Sign Up" />
</form>
```

### Success page
```php
$direct = new \ChargifyV2\DirectHelper(
    '{{your api_id}}',
    '{{your api_secret}}'
);

$client = new \ChargifyV2\Client(
    '{{your api_id}}',
    '{{your api_password}}'
);

$isValidResponse = $direct->isValidResponseSignature(
    $_GET['signature'],
    $_GET['api_id'],
    $_GET['timestamp'],
    $_GET['nonce'],
    $_GET['status_code'],
    $_GET['result_code'],
    $_GET['call_id']
);

if ($isValidResponse) {
  $result = $client->getCall($_GET['call_id']);
}
```

More examples can be found [here](examples).

## Learn more at these links
- [Chargify Website](https://www.chargify.com)
- [Chargify Direct Introduction](https://docs.chargify.com/chargify-direct-introduction)

## License

The ChargifyV2 is licensed under the MIT license. See [License File](LICENSE) for more information.
