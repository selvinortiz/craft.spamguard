<?php
namespace Craft;

class SpamGuardVariable
{
	public function getVersion()
	{
		return craft()->plugins->getPlugin(SpamGuardPlugin::PLUGIN_HANDLE)->getVersion();
	}

	//--------------------------------------------------------------------------------
	
	/**
	 * isSpam()
	 *
	 * Provided for testing withing a template only
	 */
	public function isSpam( $data, $onSuccess=false, $onFailure=false)
	{
		$params = array(
			'data'	=> array(
				'content'	=> arrayGet('content', $data),
				'author'	=> arrayGet('author', $data),
				'email'		=> arrayGet('email', $data)
			),
			'onSuccess'		=> $onSuccess,
			'onFailure'		=> $onFailure
		);

		$model = SpamGuardModel::populateModel($params['data']);

		if ($model->validate())
		{
			try
			{
				if (craft()->spamGuard->isSpam($model))
				{
					return 'You got totally spammed!!!';
				}
				else
				{
					return 'The inbox will be pleased, no spam here!';
				}
			}
			catch (\Exception $e)
			{
				return 'Something we totally nuts:<br>'.$e->getMessage();
			}

		}

		return 'Data is not good enough dude!';
	}
}
