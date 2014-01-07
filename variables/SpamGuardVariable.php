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
}
