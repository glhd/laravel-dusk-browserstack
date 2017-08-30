<?php
/**
 * Copyright (c) 2017 International Association of Certified Home Inspectors.
 */

namespace Galahad\BrowserStack;

use BrowserStack\Local;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use RuntimeException;

/**
 * Run BrowserStack from your tests.
 *
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
	 * @throws RuntimeException if the driver file path doesn't exist.
	 * @return void
	 */
	public static function startBrowserStack()
	{
		static::$browserStackProcess = new Local();
		
		static::startBrowserStackProcess(static::$browserStackProcess);
		
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
	 * @return RemoteWebDriver
	 */
	public static function createBrowserStackDriver() : RemoteWebDriver
	{
		return RemoteWebDriver::create(
			static::browserStackSeleniumUrl(),
			static::browserStackSeleniumCaps()
		);
	}
	
	/**
	 * Build the BrowserStack Selenium URL.
	 *
	 * @return string
	 */
	protected static function browserStackSeleniumUrl() : string
	{
		$username = config('services.browserstack.username');
		$key = config('services.browserstack.key');
		
		return "https://{$username}:{$key}@hub-cloud.browserstack.com/wd/hub";
	}
	
	/**
	 * Build the BrowserStack Selenium capabilities.
	 *
	 * @return array
	 */
	protected static function browserStackSeleniumCaps() : array
	{
		$defaults = [
			'browserstack.local' => 'true',
		];
		
		return array_merge($defaults, DesiredCapabilities::chrome(), static::$browserStackCapabilities ?? []);
	}
	
	/**
	 * Build the process to run the BrowserStack.
	 *
	 * @param Local $process
	 */
	protected static function startBrowserStackProcess(Local $process)
	{
		$key = config('services.browserstack.key', env('BROWSERSTACK_ACCESS_KEY'));
		
		if (!$key) {
			throw new RuntimeException('services.browserstack.key or BROWSERSTACK_ACCESS_KEY must be configured.');
		}
		
		$process->start([
			'key' => $key,
		]);
	}
}
