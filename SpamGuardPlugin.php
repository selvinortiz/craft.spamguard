<?php
namespace Craft;

/**
 * Spam Guard @v0.5.5
 *
 * Spam Guard harnesses the power of Akismet to fight spam on your behalf
 *
 * @author		Selvin Ortiz - http://twitter.com/selvinortiz
 * @package		Spam Guard
 * @copyright	2014 Selvin Ortiz
 * @license		[MIT]
 */
class SpamGuardPlugin extends BasePlugin
{
	/**
	 * Enables support for third party plugins
	 *
	 * @return	void
	 */
	public function init()
	{
		// Support for contact.beforeSend()
		if ($this->getSettings()->enableContactFormSupport)
		{
			craft()->on('contactForm.beforeSend', function(Event $event)
			{
				$spam = craft()->spamGuard->detectContactFormSpam($event->params['message']);

				if ($spam)
				{
					$event->fakeIt = true;
				}
			});
		}

		// Support for guestEntries.beforeSave()
		if ($this->getSettings()->enableGuestEntriesSupport)
		{
			craft()->on('guestEntries.beforeSave', function(Event $event)
			{
				$spam = craft()->spamGuard->detectGuestEntrySpam($event->params['entry']);

				if ($spam)
				{
					$event->fakeIt = true;
				}
			});
		}

		// Load dependencies
		$bootstrap = craft()->path->getPluginsPath().'spamguard/library/vendor/autoload.php';

		if (!file_exists($bootstrap))
		{
			throw new Exception(Craft::t('Please download the latest release or read the install notes'));
		}

		require_once $bootstrap;
	}

	/**
	 * Gets the name of the plugin or its alias given by end user
	 *
	 * @param	boolean	$real	Whether the real plugin name should be returned
	 *
	 * @return	string
	 */
	public function getName($real=false)
	{
		$name	= 'Spam Guard';
		$alias	= $this->getSettings()->pluginAlias;

		if ($real)
		{
			return $name;
		}

		return empty($alias) ? $name : $alias;
	}

	public function getVersion()
	{
		return '0.5.5';
	}

	public function getDeveloper()
	{
		return 'Selvin Ortiz';
	}

	public function getDeveloperUrl()
	{
		return 'http://twitter.com/selvinortiz';
	}

	public function getPluginCpUrl()
	{
		$cp = craft()->config->get('cpTrigger');

		return "/{$cp}/settings/plugins/spamguard";
	}

	public function hasCpSection()
	{
		return $this->getSettings()->enableCpTab;
	}

	public function defineSettings()
	{
		$url = craft()->getSiteUrl();

		return array(
			'akismetApiKey'				=> array(AttributeType::String,	'required' => true, 'maxLength' => 25),
			'akismetOriginUrl'			=> array(AttributeType::String,	'required' => true, 'default' => $url),
			'enableContactFormSupport'	=> array(AttributeType::Bool,	'default' => true, 'required'),
			'enableGuestEntriesSupport'	=> array(AttributeType::Bool,	'default' => true),
			'logSubmissions'			=> array(AttributeType::Bool,	'default' => false),
			'enableCpTab'				=> array(AttributeType::Bool,	'default' => true),
			'pluginAlias'				=> AttributeType::String
		);
	}

	public function getSettingsHtml()
	{
		$settings = $this->getSettings();

		craft()->templates->includeCssResource('spamguard/css/spamguard.css');

		return craft()->templates->render('spamguard/_settings', compact('settings'));
	}

	/**
	 * spamGuardDetectSpam()
	 *
	 * Allows your own plugin to verify spammy content by using craft()->plugins->call()
	 *
	 * @since   0.4.2
	 * @param	array('email' => '', 'author' => '', 'content' => 'Content to check for spam.')
	 * @param	mixed	$onSuccess	Set to false or a callable function to execute on success
	 * @param	mixed	$onFailure	Set to false or a callable function to execute on failure
	 *
	 * @return  bool	Whether spam was detected
	 */
	public function spamGuardDetectSpam(array $data, $onSuccess=false, $onFailure=false)
	{
		$isSpam = craft()->spamGuard->isSpam($data);

		if ($isSpam && $onFailure && is_callable($onFailure))
		{
			$onFailure($data);
		}

		if (!$isSpam && $onSuccess && is_callable($onSuccess))
		{
			$onSuccess($data);
		}

		return $isSpam;
	}

	public function onAfterInstall()
	{
		$cp = craft()->config->get('cpTrigger');

		craft()->request->redirect("/{$cp}/settings/plugins/spamguard");
	}
}
