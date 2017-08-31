# Run Laravel Dusk tests in BrowserStack

## Installation

First, install the composer package:

```
composer require galahad/dusk-browserstack
```

## Usage

Update your DuskTestCase:

```php
<?php

namespace Tests;

use Galahad\BrowserStack\SupportsBrowserStack;

abstract class DuskTestCase extends BaseTestCase
{
	// Add this:
	use SupportsBrowserStack;
	
	public static function prepare()
	{
		// This is no longer needed:
		// static::startChromeDriver();
	}
	
	protected function driver()
	{
		// Set up the browser stack driver as needed
		return $this->createBrowserStackDriver(/* $config */);
	}
}
```

## Options

Set these in the `driver()` call or in the `services.browserstack` config. The `key` option
can be omitted if you have a `BROWSERSTACK_ACCESS_KEY` environmental variable set.

- `username`: **[required]** your BrowserStack username
- `key`: **[required]** your BrowserStack API key
- `local_config`: array of options to pass to [BrowserStackLocal](https://github.com/browserstack/browserstack-local-php)
- `capabilities`: array of default capabilities to request (defaults to Chrome on any available platform)

## Advanced

It's possible to call `setBrowserStackLocalConfig` or `setBrowserStackCapabilities` within your tests,
and each new call to `driver()` will use the updated settings. You can use this to run different tests
on different browsers/platforms/etc.

## Changelog

### 1.0.2

- Better default BrowserStack logfile

### 1.0.1

- Loads configuration from `services.browserstack` by default for less boilerplate.

## 1.0.0

- First release
