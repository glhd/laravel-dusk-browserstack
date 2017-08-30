<?php
/**
 * Copyright (c) 2017 International Association of Certified Home Inspectors.
 */

namespace Galahad\BrowserStack;

use BrowserStack\Local;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Contracts\Container\Container;
use RuntimeException;

/**
 * Run BrowserStack from your tests.
 *
 * @property-read Container $app
 * @package Galahad\BrowserStack
 */
trait SupportsBrowserStack
{
	/**
	 * The BrowserStack process instance.
	 *
	 * @var Local
	 */
	protected static $browserStackProcess;
	
	/**
	 * Start the BrowserStack process.
	 *
	 * @param string|null $key
	 */
	public static function startBrowserStack(string $key = null)
	{
		static::$browserStackProcess = new Local();
		
		static::startBrowserStackProcess(static::$browserStackProcess, $key);
		
		static::afterClass(function() {
			static::stopBrowserStack();
		});
	}
	
	/**
	 * Stop the BrowserStack process.
	 *
	 * @return void
	 */
	public static function stopBrowserStack()
	{
		if (static::$browserStackProcess && static::$browserStackProcess->isRunning()) {
			static::$browserStackProcess->stop();
		}
	}
	
	/**
	 * Create the BrowserStack WebDriver instance.
	 *
	 * @param array $caps
	 * @param string|null $url
	 * @return RemoteWebDriver
	 */
	public static function createBrowserStackDriver(array $caps = [], string $url = null) : RemoteWebDriver
	{
		return RemoteWebDriver::create(
			$url ?? static::browserStackSeleniumUrl(),
			static::browserStackSeleniumCaps($caps)
		);
	}
	
	/**
	 * Build the BrowserStack Selenium URL.
	 *
	 * @return string
	 */
	protected static function browserStackSeleniumUrl() : string
	{
		$username = env('BROWSERSTACK_USERNAME'); // FIXME
		$key = env('BROWSERSTACK_ACCESS_KEY');
		
		return "https://{$username}:{$key}@hub-cloud.browserstack.com/wd/hub";
	}
	
	/**
	 * Build the BrowserStack Selenium capabilities.
	 *
	 * @param array|null $caps
	 * @return array
	 */
	protected static function browserStackSeleniumCaps(array $caps = null) : array
	{
		$defaults = [
			'browserstack.local' => 'true',
		];
		
		return array_merge(
			$defaults,
			DesiredCapabilities::chrome(),
			static::$browserStackCapabilities ?? [],
			$caps ?? []
		);
	}
	
	/**
	 * Build the process to run the BrowserStack.
	 *
	 * @param Local $process
	 * @param string|null $key
	 */
	protected static function startBrowserStackProcess(Local $process, string $key = null)
	{
		$key = $key ?? env('BROWSERSTACK_ACCESS_KEY');
		
		if (empty($key)) {
			throw new RuntimeException('BROWSERSTACK_ACCESS_KEY must be configured.');
		}
		
		$process->start([
			'key' => $key,
		]);
	}
}
