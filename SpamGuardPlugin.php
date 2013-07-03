<?php
namespace Craft;

/**
 * @=SpamGuard
 *
 * SpamGuard allows you to use the powerful Akismet API to fight spam
 *
 * @author  Selvin Ortiz - http://twitter.com/selvinortiz
 * @package SpamGuard
 * @version 0.1
 *
 *
 * @example (Template) Retuns true/false
 *
 * $content: 	The blog post comment or submitted content to check
 * $author:		The submitted content author name
 * $email: 		The submitted content author email
 *
 * <code>
 * 		craft.SpamGuard.isSpam($content, $author[optional], $email[optional])
 * </code>
 *
 * @example (Service) Returns true/false
 *
 * <code>
 * 		craft()->spamGuard_spam->isSpam($content, $author[optional], $email[optional])
 * </code>
 *
 * NOTE
 * ----
 * Even though the $author and $email are both optional, it is recommended that you provide them.
 * Not doing so will impact the amount of false/positives you get as well as the request response time.
 * ----
 */

class SpamGuardPlugin extends BasePlugin
{
    const PLUGIN_NAME   		= 'Spam Guard';
    const PLUGIN_HANDLE 		= 'spamGuard';
    const PLUGIN_VERSION 		= '0.1';
    const PLUGIN_DEVELOPER 		= 'Selvin Ortiz';
    const PLUGIN_DEVELOPER_URL 	= 'http://twitter.com/selvinortiz';
    const PLUGIN_SETTINGS_TMPL  = 'spamguard/settings.twig';

    //--------------------------------------------------------------------------------
    
    public function __construct()
    {
        // Import Rocket
        require_once __DIR__.'/rocket/Rocket.php';

        // Launch it
        Rocket::launch();
    }

    //--------------------------------------------------------------------------------
    
    public function getName()
    {
        return Rocket::getPluginName($this, self::PLUGIN_NAME);
    }

    //--------------------------------------------------------------------------------
    
    public function getVersion()
    {
        return self::PLUGIN_VERSION;
    }

    //--------------------------------------------------------------------------------

    public function getDeveloper()
    {
        return self::PLUGIN_DEVELOPER;
    }

    //--------------------------------------------------------------------------------

    public function getDeveloperUrl()
    {
        return self::PLUGIN_DEVELOPER_URL;
    }

    //--------------------------------------------------------------------------------

    public function hasCpSection()
    {
        return true;
    }

    //--------------------------------------------------------------------------------

    public function defineSettings()
    {
        return array(
            'pluginName'        => array(AttributeType::String, 'maxLength'=>50),
            'pluginNickname'    => array(AttributeType::String, 'maxLength'=>50),
            'akismetApiKey'     => array(AttributeType::String, 'required'=>true, 'maxLength'=>50),
            'akismetOriginUrl'  => array(AttributeType::String, 'required'=>true, 'maxLength'=>255)
        );
    }

    //--------------------------------------------------------------------------------

    public function getSettingsHtml()
    {
        return craft()->templates->render(self::PLUGIN_SETTINGS_TMPL, array('settings'=>$this->getSettings()));
    }

    //--------------------------------------------------------------------------------
    
    public function prepSettings( $settings=array() )
    {
        if ( array_key_exists('pluginName', $settings) && ! empty($settings['pluginName']) )
        {
            return $settings;
        }

        return array_merge( $settings, array('pluginName'=>Rocket::getPluginName($this, self::PLUGIN_NAME) ) );
    }

    //--------------------------------------------------------------------------------
    
    public function onAfterInstall()
    {
        $dbCommand      = craft()->db->createCommand();
        $pluginClass    = Rocket::getClassName($this);
        $pluginSettings = array( 'pluginName'=>$this->getName(), 'pluginNickname'=>$this->getName() );

        $dbCommand->update(
            'plugins', array('settings'=>toJson($pluginSettings)),
            'class=:className', array(':className'=>$pluginClass)
        );

        $toSettings = sprintf('/%s/%s', craft()->config->get('cpTrigger'), strtolower(SpamGuardPlugin::PLUGIN_HANDLE));
        
        craft()->request->redirect( $toSettings );
    }

    //--------------------------------------------------------------------------------
    
    public function hookRegisterCpRoutes()
    {
        return array();
    }
}
