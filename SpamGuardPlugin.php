<?php
namespace Craft;

/**
 * Spam Guard @v0.6.0
 *
 * Spam Guard harnesses the power of Akismet to fight Spam
 *
 * @author		Selvin Ortiz <selvin@selv.in>
 * @version		0.6.0
 * @package		Craft
 * @copyright	2014 Selvin Ortiz
 * @license		http://opensource.org/licenses/MIT
 * @copyright	2014 Selvin Ortiz
 */
class SpamGuardPlugin extends BasePlugin
{
	/**
	 * Enables support for third party plugins
	 *
	 * @throws Exception
	 * @return void
	 */
	public function init()
	{
		#
		# Support for Contact Forms
		if ($this->getSettings()->getAttribute('enableContactFormSupport'))
		{
			craft()->on('contactForm.beforeSend', function(Event $event)
			{
				$spam = spamGuard()->detectContactFormSpam($event->params['message']);

				if ($spam)
				{
					$event->fakeIt = true;
				}
			});
		}

		#
		# Support for Guest Entries
		if ($this->getSettings()->getAttribute('enableGuestEntriesSupport'))
		{
			craft()->on('guestEntries.beforeSave', function(Event $event)
			{
				$spam = spamGuard()->detectDynamicFormSpam($event->params['entry']);

				if ($spam)
				{
					$event->fakeIt = true;
				}
			});
		}

		#
		# Support for Sprout Forms
		if ($this->getSettings()->getAttribute('enableSproutFormsSupport'))
		{
			craft()->on('sproutForms.beforeSaveEntry', function(Event $event)
			{
				$spam = spamGuard()->detectDynamicFormSpam($event->params['entry']);

				if ($spam)
				{
					$event->fakeIt	= true;
					$event->isValid	= false;
				}
			});
		}
	}

	/**
	 * Returns the name of the plugin or the alias given by the end user
	 *
	 * @param bool $real
	 *
	 * @return string
	 */
	public function getName($real=false)
	{
		$alias	= $this->getSettings()->getAttribute('pluginAlias');

		return ($real || empty($alias)) ? 'Spam Guard' : $alias;
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return '0.6.0';
	}

	/**
	 * @return string
	 */
	public function getDeveloper()
	{
		return 'Selvin Ortiz';
	}

	/**
	 * @return string
	 */
	public function getDeveloperUrl()
	{
		return 'http://twitter.com/selvinortiz';
	}

	/**
	 * @return bool
	 */
	public function hasCpSection()
	{
		return $this->getSettings()->getAttribute('enableCpTab');
	}

	/**
	 * @return array
	 */
	public function registerCpRoutes()
	{
		return array(
			'spamguard'	=> array('action' => 'spamGuard/index')
		);
	}

	/**
	 * @return array
	 */
	public function defineSettings()
	{
		return array(
			'akismetApiKey'				=> array(AttributeType::String,	'required'	=> true,	'maxLength' => 25),
			'akismetOriginUrl'			=> array(AttributeType::String,	'required'	=> true),
			'enableContactFormSupport'	=> array(AttributeType::Bool,	'default'	=> true),
			'enableGuestEntriesSupport'	=> array(AttributeType::Bool,	'default'	=> true),
			'enableSproutFormsSupport'	=> array(AttributeType::Bool,	'default'	=> true),
			'logSubmissions'			=> array(AttributeType::Bool,	'default'	=> false),
			'enableCpTab'				=> array(AttributeType::Bool,	'default'	=> true),
			'pluginAlias'				=> AttributeType::String,
		);
	}

	/**
	 * @return string
	 */
	public function getSettingsHtml()
	{
		craft()->templates->includeCssResource('spamguard/css/spamguard.css');

		return craft()->templates->render('spamguard/_settings', spamGuard()->getTemplateVariables());
	}
}


/**
 * Enables service layer encapsulation and proper hinting
 *
 * @return SpamGuardService
 */
function spamGuard()
{
	return craft()->spamGuard;
}
