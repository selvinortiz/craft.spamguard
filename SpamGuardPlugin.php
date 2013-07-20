<?php
namespace Craft;

/**
 * @=SpamGuard
 *
 * Spam Guard allows you to harness the powerful of Akismet to fight spam
 *
 * @author		Selvin Ortiz - http://twitter.com/selvinortiz
 * @package		SpamGuard
 * @category	Craft CMS
 * @copyright	2013 Selvin Ortiz
 * @license		https://github.com/selvinortiz/spamguard/blob/master/license.txt
 * @link		https://github.com/selvinortiz/spamguard
 */

class SpamGuardPlugin extends BasePlugin
{
	public $props; // @var Plugin properties defined in plugin.json

	//--------------------------------------------------------------------------------

	public function __construct()
	{
		$this->loadBridge();
		$this->loadProps();
	}

	//--------------------------------------------------------------------------------

	public function getName() { return Bridge::getPluginName($this, $this->props->name); }

	//--------------------------------------------------------------------------------

	public function getVersion() { return $this->props->version; }

	//--------------------------------------------------------------------------------

	public function getDeveloper() { return $this->props->developer; }

	//--------------------------------------------------------------------------------

	public function getDeveloperUrl() { return $this->props->developerUrl; }

	//--------------------------------------------------------------------------------

	public function getPluginCpUrl()
	{
		return sprintf('/%s/%s', craft()->config->get('cpTrigger'), strtolower($this->props->handle) );
	}

	//--------------------------------------------------------------------------------

	public function hasCpSection() { return true; }

	//--------------------------------------------------------------------------------

	public function defineSettings()
	{
		return array(
			'pluginName'		=> array( AttributeType::String, 'maxLength'=>50 ),
			'pluginNickname'	=> array( AttributeType::String, 'maxLength'=>50 ),
			'akismetApiKey'		=> array( AttributeType::String, 'required'=>true, 'maxLength'=>25 ),
			'akismetOriginUrl'	=> array( AttributeType::String, 'required'=>true, 'maxLength'=>255 ),
			// --
			'sendToName'		=> array( AttributeType::String, 'required'=>true, 'maxLength'=>50 ),
			'sendToEmail'		=> array( AttributeType::Email,	'required'=>true, 'maxLength'=>100 ),
			'subjectPrefix'		=> array( AttributeType::String, 'default'=>'Form Submission', 'maxLength'=>50 ),
			// --
			'emailTemplate'		=> array( AttributeType::String, 'required'=>true, 'default'=>'' )
		);
	}

	//--------------------------------------------------------------------------------

	public function getSettingsHtml()
	{
		$tmpl = $this->props->settingsTemplate;
		$data = array('settings'=>$this->getSettings());

		return craft()->templates->render($tmpl, $data);
	}

	//--------------------------------------------------------------------------------

	public function prepSettings( $settings=array() )
	{
		if ( array_key_exists('pluginName', $settings) && ! empty($settings['pluginName']) )
		{
			return $settings;
		}

		return array_merge( $settings, array('pluginName'=>Bridge::getPluginName($this, $this->props->name) ) );
	}

	//--------------------------------------------------------------------------------

	public function onAfterInstall()
	{
		$dbCommand		= craft()->db->createCommand();
		$pluginClass	= Bridge::getClassName($this);
		$pluginSettings	= array(
			'pluginName'		=> $this->getName(),
			'pluginNickname'	=> $this->getName(),
			'emailTemplate'		=> IOHelper::getFile(__DIR__.'/templates/__message.twig')->getContents()
		);

		$dbCommand->update(
			'plugins', array('settings'=>toJson($pluginSettings)),
			'class=:className', array(':className'=>$pluginClass)
		);

		craft()->request->redirect( $this->getPluginCpUrl() );
	}

	//--------------------------------------------------------------------------------
	// @LOADERS
	//--------------------------------------------------------------------------------

	protected function loadProps()
	{
		$this->props = getJson(IOHelper::getFile(__DIR__.'/plugin.json')->getContents());
	}

	//--------------------------------------------------------------------------------

	protected function loadBridge()
	{
		$path = __DIR__.'/bridge/Loader.php';

		if ( file_exists($path) )
		{
			require_once($path);
		}
		else
		{
			throw new \Exception('The plugin package was not found @'.__METHOD__);
		}
	}

	//--------------------------------------------------------------------------------
	// @HOOKS
	//--------------------------------------------------------------------------------

	/**
	 * spamGuardDetectSpam()
	 *
	 * This function name was chosen in favor of spamGuardSubmittedContent/spamGuardPostedContent
	 * The signature was made more verbose and easier to understand
	 *
	 * @since	0.4.2
	 * @return	boolean		Whether spam was detected
	 */
	public function spamGuardDetectSpam($content, $author, $email, $onSuccess=false, $onFailure=false)
	{
		$modelData = array(
			'content'	=> $content,
			'author'	=> $author,
			'email'		=> $email
		);

		$detected	= craft()->spamGuard->detectSpam($modelData);
		$spamModel	= craft()->spamGuard->getModel();

		if ( $detected && $onFailure && is_callable($onFailure) )
		{
			$onFailure($spamModel);
		}

		if ( $detected == false && $onSuccess && is_callable($onSuccess) )
		{
			$onSuccess($spamModel);
		}

		return (bool) $detected;
	}
}
