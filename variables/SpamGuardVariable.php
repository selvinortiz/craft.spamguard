<?php
namespace Craft;

/**
 * Spam Guard @v0.5.3
 *
 * Spam Guard harnesses the power of Akismet to fight spam
 *
 * @author		Selvin Ortiz - http://twitter.com/selvinortiz
 * @package		Spam Guard
 * @copyright	2014 Selvin Ortiz
 * @license		[MIT]
 */
class SpamGuardVariable
{
	protected $plugin;
	protected $service;

	public function __construct()
	{
		$this->plugin	= craft()->plugins->getPlugin('spamGuard');
		$this->service	= craft()->spamGuard;
	}

	/**
	 * Allows the plugin and service methods to be called via craft.spamguard
	 *
	 * @param	string	$method		The method name that was called
	 * @param	array	$params		The array of parameters that were passed
	 *
	 * @throws	\BadMethodCallException
	 * 
	 * @return	mixed	The returned value from the callable
	 */
	public function __call($method, array $params=array())
	{
		if (method_exists($this->plugin, $method))
		{
			return call_user_func_array(array($this->plugin, $method), $params);
		}

		if (method_exists($this->service, $method))
		{
			return call_user_func_array(array($this->service, $method), $params);
		}

		throw new \BadMethodCallException(Craft::t('{m} is not a valid/callable method.', array('m' => $method)));
	}
}
