<?php
namespace Craft;

class SpamGuardVariable
{
	protected $plugin;

	public function __construct()
	{
		$this->plugin = craft()->plugins->getPlugin('spamGuard');
	}

	public function getName($real=false)
	{
		return $this->plugin->getName($real);
	}

	public function getVersion()
	{
		return $this->plugin->getVersion();
	}

	public function getUrl()
	{
		return sprintf('/%s/%s', craft()->config->get('cpTrigger'), 'spamguard');
	}

	public function getLogs(array $attributes=array())
	{
		return craft()->spamGuard->getLogs($attributes);
	}
}
