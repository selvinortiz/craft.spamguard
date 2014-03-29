<?php
namespace Craft;

class SpamGuardVariable
{
	protected $plugin;

	public function __construct()
	{
		$this->plugin = craft()->plugins->getPlugin('spamGuard');
	}

	public function getName($real=true)
	{
		return $this->plugin->getName($real);
	}

	public function getVersion()
	{
		return $this->plugin->getVersion();
	}
	
	public function getDeveloper()
	{
		return $this->plugin->getDeveloper();
	}
	
	public function getDeveloperUrl()
	{
		return $this->plugin->getDeveloperUrl();
	}
	public function getUrl()
	{
		return sprintf('/%s/spamguard', craft()->config->get('cpTrigger'));
	}

	public function getLogs(array $attributes=array())
	{
		return craft()->spamGuard->getLogs($attributes);
	}
}
