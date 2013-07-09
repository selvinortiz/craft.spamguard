<?php
namespace Craft;

define('__BRIDGE', true);

/**
 * @Bridge
 *
 * Bridge is a tiny helper library for plugin development in Craft
 *
 * @author  Selvin Ortiz <http://twitter.com/selvinortiz>
 * @package Bridge
 * @version 1.0
 */
class Bridge
{
	/**
	 * getPluginName()
	 *
	 * Grabs the plugin name or nickname if one has been set in via the CP
	 *
	 * @param  object $pluginClass The plugin class instance
	 * @param  string $defaultName The fallback name to use in case no pluginNickname is found
	 * @return string The plugin name
	 */
	public static function getPluginName($pluginClass, $defaultName='')
	{
		$className = self::getClassName($pluginClass);

		if ( is_string($className) && ! empty($className) ) {
			$cmd = 	craft()->db->createCommand()
					->select('settings')
					->from('plugins')
					->where('class=:className', array(':className' => $className))
					->queryScalar();

			if ($cmd) {
				$plugin = getJson( $cmd );

				return empty($plugin->pluginNickname) ? Craft::t($defaultName) : Craft::t($plugin->pluginNickname);
			}
		}

		return Craft::t($defaultName);
	}

	//--------------------------------------------------------------------------------

	/**
	 * getClassName()
	 *
	 * Translates the class instance into a class name w/o the namespace or plugin postfix
	 *
	 * @param  object $pluginClass The plugin class instance
	 * @return string The plugin class name
	 */

	public static function getClassName($pluginClass)
	{
		$className = get_class($pluginClass);
		$className = str_replace(__NAMESPACE__.'\\', '', $className);
		$className = str_replace('Plugin', '', $className);

		if ( is_string($className) && ! empty( $className ) ) {
			return $className;
		}

		throw new \Exception('Plugin instance yielded an invalid class name @ '.__METHOD__);
	}

	//--------------------------------------------------------------------------------

	/*
	 * safeOutput()
	 *
	 * Marks html content as safe for output within templates
	 *
	 * @param	string 	$content 	The content to mark as safe
	 * @param	string 	$charset 	The (optional) charset to use
	 */
	public static function safeOutput($content, $charset=null)
	{
		if ( is_null($charset) )
		{
			$charset = craft()->templates->getTwig()->getCharset();
		}

		return new \Twig_Markup($content, (string) $charset);
	}
}
