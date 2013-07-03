<?php
namespace Craft;

/**
 * @=Rocket
 *
 * Rocket is a tiny helper library for plugin development in Craft
 *
 * @author  Selvin Ortiz - http://twitter.com/selvinortiz
 * @package Rocket
 * @version 1.0
 */

class Rocket
{
    protected static $launched = false;
    protected static $classMap = array();

    public static function Launch()
    {
        if (self::$launched == false) {
            require_once __DIR__.'/Functions.php';
            require_once __DIR__.'/packages/autoload.php';

            self::loadClassMap();
            self::registerLoader();

            self::$launched = true;
        }
    }

    //--------------------------------------------------------------------------------

    public function loadClassMap()
    {
        self::$classMap = require_once __DIR__.'/ClassMap.php';
    }

    //--------------------------------------------------------------------------------
    
    public static function registerLoader()
    {
        spl_autoload_register( array('Craft\\Rocket', 'loadClass') );
    }

    //--------------------------------------------------------------------------------
    
    public static function loadClass($className)
    {
        $className = ltrim($className, '\\');

        if ( array_key_exists($className, self::$classMap) )
        {
            require_once self::getBasePath(self::$classMap[$className]);
        }
        else
        {
            $name = strtolower($className);
            $path = self::getBasePath("ext/{$name}/{$name}.php");

            if ( file_exists( $path ) )
            {
                require $path;
            }
        }

        return false;
    }

    //--------------------------------------------------------------------------------
    
    public static function getBasePath( $append='' )
    {
        if ( ! empty($append) )
        {
            return __DIR__.'/'.ltrim($append, '');
        }

        return __DIR__.'/';
    }


    //--------------------------------------------------------------------------------

    /**
     * getPluginName()
     *
     * Allows a plugin to be given a more memorable name on the CP
     *
     * @param  object $pluginClass The plugin class instance
     * @param  string $defaultName The fallback name to use in case no pluginNickname is found
     * @return string The plugin name
     */

    public static function getPluginName($pluginClass, $defaultName='')
    {
        $className = self::getClassName($pluginClass);

        if ( is_string($className) && ! empty($className) ) {
            $cmd = craft()->db->createCommand()
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
}
