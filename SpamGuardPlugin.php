<?php
namespace Craft;

/**
 * @=SpamGuard
 *
 * SpamGuard allows you to use the powerful Akismet API to fight spam
 *
 * @author  Selvin Ortiz - http://twitter.com/selvinortiz
 * @package SpamGuard
 * @version 0.4
 *
 *
 * @example (Template) Retuns true/false
 *
 * $content: 	The blog post comment or submitted content to check
 * $author:		The submitted content author name
 * $email: 		The submitted content author email
 *
 * <code>
 *  craft.SpamGuard.isSpam(
 *  	{
 *  		content: "Potential spammy comment or submitted content",
 *  		author: "John Smith",
 *  		email: "john@smith.com"
 *  	}
 *  )
 * </code>
 *
 * @example (Service) Returns true/false
 *
 * <code>
 *  craft()->spamGuard->isSpam(SpamGuardModel $model)
 * </code>
 */

class SpamGuardPlugin extends BasePlugin
{
	const PLUGIN_NAME			= 'Spam Guard';
	const PLUGIN_HANDLE			= 'spamGuard';
	const PLUGIN_VERSION		= '0.4';
	const PLUGIN_DEVELOPER		= 'Selvin Ortiz';
	const PLUGIN_DEVELOPER_URL	= 'http://twitter.com/selvinortiz';
	const PLUGIN_SETTINGS_TMPL	= 'spamguard/__settings.twig';

	//--------------------------------------------------------------------------------
	
	public function __construct()
	{
		require_once __DIR__.'/rocket/Rocket.php';
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

	public function getCpUrl()
	{
		// Is this already available via a Craft service provider?
		return sprintf('/%s/%s', craft()->config->get('cpTrigger'), strtolower(self::PLUGIN_HANDLE) );
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
			'pluginName'		=> array(AttributeType::String, 'maxLength'=>50),
			'pluginNickname'	=> array(AttributeType::String, 'maxLength'=>50),
			'akismetApiKey'		=> array(AttributeType::String, 'required'=>true, 'maxLength'=>50),
			'akismetOriginUrl'	=> array(AttributeType::String, 'required'=>true, 'maxLength'=>255)
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
		$dbCommand		= craft()->db->createCommand();
		$pluginClass	= Rocket::getClassName($this);
		$pluginSettings	= array( 'pluginName'=>$this->getName(), 'pluginNickname'=>$this->getName() );

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

	//--------------------------------------------------------------------------------
	// @HOOKS
	//--------------------------------------------------------------------------------
	
	/**
	 * spamGuardPostedContent()
	 *
	 * @param  array $params The associative array of data and callbacks
	 *
	 * <code>
	 * 	$params = array( data=>array(content=>'', author=>'', email=>''), onSuccess=>function(){}, onFailure=>function(){})
	 * </code>
	 */
	public function spamGuardPostedContent($params=false)
	{
		// I was planning on using onSpam/onClean but went with the more generic onSuccess/onFailure
		$data 		= array();
		$onSuccess	= null;
		$onFailure	= null;

		if ( $params && is_array($params) )
		{
			@extract($params);
		}

		// Should I be calling a controller method here instead of the service?
		$isContentSpam = craft()->spamGuard->isSpam($data);

		if ( $isContentSpam && $onFailure )
		{
			if (is_callable($onFailure))
			{
				$onFailure();
			}
		}
		elseif ( ! $isContentSpam && $onSuccess )
		{
			if (is_callable($onSuccess))
			{
				$onSuccess();
			}
		}

		return $isContentSpam;
	}
}
