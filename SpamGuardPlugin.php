<?php
namespace Craft;

/**
 * Spam Guard 0.5.0
 *
 * Spam Guard allows you to harness the power of Akismet to fight spam
 *
 * @author		Selvin Ortiz - http://twitter.com/selvinortiz
 * @package		SpamGuard
 * @category	Craft CMS
 * @copyright	2014 Selvin Ortiz
 * @license		https://github.com/selvinortiz/craft.spamguard/blob/master/license.txt
 * @link		https://github.com/selvinortiz/craft.spamguard
 */

class SpamGuardPlugin extends BasePlugin
{
	/**
	 * Listens for form events
	 */
	public function init()
	{
		if ($this->getSettings()->enableFormSupport)
		{
			craft()->on('contactForm.beforeSend', function(ContactFormEvent $event)
			{
				$spam = craft()->spamGuard->detectContactFormSpam($event->params['message']);

				if ($spam)
				{
					$event->fakeIt = true;
				}
			});
		}
	}

	/**
	 * Gets the plugin name or alias given by end user
	 *
	 * @param	bool	$real	Whether the real name should be returned
	 * @return	string
	 */
	public function getName($real=false)
	{
		if ($real)
		{
			return 'Spam Guard';
		}

		$alias = $this->getSettings()->pluginAlias;

		return empty($alias) ? 'Spam Guard' : Craft::t($alias);
	}

	public function getVersion()
	{
		return '0.5.0';
	}

	public function getDeveloper()
	{
		return 'Selvin Ortiz';
	}

	public function getDeveloperUrl()
	{
		return 'http://twitter.com/selvinortiz';
	}

	public function hasCpSection()
	{
		return $this->getSettings()->enableCpTab;
	}

	public function defineSettings()
	{
		return array(
			'akismetApiKey'		=> array(AttributeType::String, 'required'=>true, 'maxLength'=>25),
			'akismetOriginUrl'	=> array(AttributeType::String, 'required'=>true, 'maxLength'=>255),
			'enableFormSupport'	=> AttributeType::Bool,
			'enableCpTab'		=> AttributeType::Bool,
			'pluginAlias'		=> AttributeType::String
		);
	}

	public function getSettingsHtml()
	{
		craft()->templates->includeCssResource('spamguard/css/spamguard.css');

		return craft()->templates->render(
			'spamguard/_settings.html',
			array(
				'settings' => $this->getSettings()
			)
		);
	}

	/**
	 * spamGuardDetectSpam()
	 *
	 * Allows your own plugin to verify spammy content by using craft()->plugins->call()
	 *
	 * @since    0.4.2
	 * @param	array	$data email, author, content
	 * @param	bool	$onSuccess
	 * @param	bool	$onFailure
	 * @return  bool	Whether spam was detected
	 */
	public function spamGuardDetectSpam(array $data, $onSuccess=false, $onFailure=false)
	{
		$isSpam	= craft()->spamGuard->isSpam($data);

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
}
