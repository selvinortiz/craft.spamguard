<?php
namespace Craft;

class SpamGuardService extends BaseApplicationComponent
{
	/**
	 * The fully qualified class name of the provider to instantiate
	 */
	const PROVIDER_CLASS_NAME = 'Akismet';

	/**
	 * $provider The instance of the service provider (Akismet) used by Spam Guard
	 * 
	 * @var object Instance of Akismet
	 */
	protected $provider;

	//--------------------------------------------------------------------------------

	public function __construct()
	{
		$spamGuard	= craft()->plugins->getPlugin(SpamGuardPlugin::PLUGIN_HANDLE);
		$settings	= $spamGuard->getSettings();
		$originUrl	= arrayGet('originUrl', $settings);
		$apiKey		= arrayGet('akismetApiKey', $settings);

		if ( class_exists( self::PROVIDER_CLASS_NAME ) )
		{
			if ( ! $originUrl )
			{
				$originUrl = craft()->request->getHostInfo();
			}

			if ( ! $apiKey )
			{
				// Would like to flash a message/warning when users arrive at the settings page
				craft()->request->redirect( $spamGuard->getCpUrl() );
			}

			$this->provider	= new \Akismet($originUrl, $apiKey);
		}
		else
		{
			throw new \Exception( self::PROVIDER_CLASS_NAME.' is not available @ '.__METHOD__);
		}
	}

	//--------------------------------------------------------------------------------

	public function isSpam($data=false)
	{
		// Got a model
		if ( is_object($data) && ($data instanceof SpamGuardModel) )
		{
			$model = $data;
		}
		// Array to model
		elseif ( is_array($data) && count($data) )
		{
			$model = SpamGuardModel::populateModel($data);
		}
		// Post request to model
		else
		{
			$model = new SpamGuardModel();

			$model->content	= craft()->request->getPost('content');
			$model->author	= craft()->request->getPost('author');
			$model->email	= craft()->request->getPost('email');
		}

		if ( $model->validate() )
		{
			if ( is_object($this->provider) )
			{
				$this->provider->setCommentContent($model->content);
				$this->provider->setCommentAuthor($model->author);
				$this->provider->setCommentAuthorEmail($model->email);
				$this->provider->setCommentUserAgent($_SERVER['HTTP_USER_AGENT']);
				$this->provider->setUserIp($_SERVER['REMOTE_ADDR']);

				if ( $this->provider->isCommentSpam() )
				{
					// May return false if the key is invalid so check that too
					if ( $this->provider->isKeyValid() )
					{
						return true;
					}
					else
					{
						throw new \Exception('Your API Key may be invalid or may have expired @'.__METHOD__);
					}
				}
			}
		}

		return false;
	}
}
