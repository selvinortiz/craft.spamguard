<?php
namespace Craft;

/**
 * Spam Guard 0.4.7
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
	public function __construct()
	{
		Craft::import('plugins.spamguard.helpers.Akismet');
	}

	public function init()
	{
		if ($this->getSettings()->enableContactFormSupport)
		{
			craft()->on('contactForm.beforeSend', function(ContactFormEvent $event)
			{
				if (craft()->spamGuard->detectContactFormSpam($event->params['email']))
				{
					$event->fakeIt = true;
				}
			});
		}
	}

	public function getName($real=false)
	{
		$alias = Craft::t($this->getSettings()->pluginAlias);

		if ($real)
		{
			return 'Spam Guard';
		}

		return empty($alias) ? 'Spam Guard' : $alias;
	}

	public function getVersion()
	{
		return '0.4.7';
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
		return sprintf('/%s/spamguard', craft()->config->get('cpTrigger'));
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

			'sendToName'		=> array(AttributeType::String, 'required'=>true, 'maxLength'=>50),
			'sendToEmail'		=> array(AttributeType::Email,	'required'=>true, 'maxLength'=>100),
			'subjectPrefix'		=> array(AttributeType::String, 'default'=>'Form Submission', 'maxLength'=>50),
			'emailTemplate'		=> array(AttributeType::String, 'required'=>true, 'default'=>''),

			'enableCpTab'		=> AttributeType::Bool,
			'pluginAlias'		=> AttributeType::String,

			// Contact Form by P&T
			'enableContactFormSupport'	=> AttributeType::Bool
		);
	}

	public function getSettingsHtml()
	{
		craft()->templates->includeCssResource('spamguard/css/spamguard.css');

		return craft()->templates->render(
			'spamguard/_settings.html',
			array(
				'settings'	=> $this->getSettings()
			)
		);
	}

	/**
	 * spamGuardDetectSpam()
	 *
	 * Allows your own plugin to verify spammy content by using craft()->plugins->call()
	 *
	 * @since	0.4.2
	 * @return	boolean		Whether spam was detected
	 */
	public function spamGuardDetectSpam($content, $author, $email, $onSuccess=false, $onFailure=false)
	{
		$data = array(
			'content'	=> $content,
			'author'	=> $author,
			'email'		=> $email
		);

		$detected	= craft()->spamGuard->detectSpam($data);
		$model		= craft()->spamGuard->getModel();

		if ($detected && $onFailure && is_callable($onFailure))
		{
			$onFailure($model);
		}

		if (!$detected && $onSuccess && is_callable($onSuccess))
		{
			$onSuccess($model);
		}

		return $detected;
	}
}
