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
		return $this->createBrowserStackDriver([
			'username' => $this->app['config']->get('services.browserstack.username'),
			'key' => $this->app['config']->get('services.browserstack.key'),
		]);
	}
}
```

## Options

- `username`: **[required]** your BrowserStack username
- `key`: **[required]** your BrowserStack API key
- `local_config`: array of options to pass to [BrowserStackLocal](https://github.com/browserstack/browserstack-local-php)
- `capabilities`: array of default capabilities to request (defaults to Chrome on any available platform)

## Advanced

It's possible to call `setBrowserStackLocalConfig` or `setBrowserStackCapabilities` within your tests,
and each new call to `driver()` will use the updated settings. You can use this to run different tests
on different browsers/platforms/etc.
