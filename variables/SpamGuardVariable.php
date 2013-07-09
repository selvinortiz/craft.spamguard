<?php
namespace Craft;

class SpamGuardVariable
{
	public function getName()
	{
		return craft()->plugins->getPlugin(SpamGuardPlugin::PLUGIN_HANDLE)->getName();
	}

	//--------------------------------------------------------------------------------

	public function getVersion()
	{
		return craft()->plugins->getPlugin(SpamGuardPlugin::PLUGIN_HANDLE)->getVersion();
	}

	//--------------------------------------------------------------------------------
	
	/**
	 * detectSpam()
	 *
	 * Provided for testing within a template only
	 */
	public function detectSpam($content, $author, $email, $onSuccess=false, $onFailure=false)
	{
		// Prepare the model data
		$modelData = array(
			'content'	=> $content,
			'author'	=> $author,
			'email'		=> $email
		);

		$detected	= craft()->spamGuard->detectSpam($modelData);
		$spamModel	= craft()->spamGuard->getModel();

		if ( $detected )
		{
			return '<h2>Spam was detected, sorry for the bad news!</h2>';
		}
		else
		{
			// We may have gotten false but that happens when the model fails validation too (safety)
			if ( $spamModel->validate() )
			{
				return '<h2>Looks like you are free of spam, that is awesome!</h2>';
			}
			return '<h2>Could not figure out if you got spammed or nott!!!</h2>';
		}
	}
}
