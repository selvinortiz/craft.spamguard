<?php
namespace Craft;

class SpamGuardVariable
{
	protected $plugin;

	//--------------------------------------------------------------------------------

	public function __construct()
	{
		$this->plugin = craft()->plugins->getPlugin('spamGuard');
	}

	//--------------------------------------------------------------------------------
	
	public function getName()
	{
		return $this->plugin->getName();
	}

	//--------------------------------------------------------------------------------

	public function getVersion()
	{
		return $this->plugin->getVersion();
	}

	//--------------------------------------------------------------------------------

	public function getProp($name=null)
	{
		if ( is_null($name) )
		{
			return $this->plugin->props;
		}
		elseif ( property_exists($this->plugin->props, $name) )
		{
			return $this->plugin->props->$name;
		}
		else
		{
			return false;
		}
	}
}
