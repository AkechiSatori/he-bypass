# HEBypass
Bypass he.net javascript verify

## Install

```composer require akechisatori/he-bypass```

## Usage

```php

<?php
include 'vendor/autoload.php';
try {
	$self_ip = "YOUR_SELF_IP";
	$res = (new \HENET\Core)->Request("https://bgp.he.net/AS134606", $self_ip);
	var_dump($res->data());
} catch (\Exception $e) {
	var_dump($e);
}
```