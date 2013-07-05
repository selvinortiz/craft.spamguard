<?php
namespace Craft;

class SpamGuardService extends BaseApplicationComponent
{
	const PROVIDER_CLASS_NAME = 'Akismet';	// The service provider class name (FQ)

	protected $model;						// SpamGuardModel instance
	protected $provider;					// Akismet instance

	//--------------------------------------------------------------------------------

	public function __construct()
	{
		$spamGuard	= craft()->plugins->getPlugin(SpamGuardPlugin::PLUGIN_HANDLE);
		$settings	= $spamGuard->getSettings();
		$originUrl	= $settings['akismetOriginUrl'];
		$apiKey		= $settings['akismetApiKey'];

		if ( class_exists( self::PROVIDER_CLASS_NAME ) )
		{
			if ( ! $originUrl )
			{
				$originUrl = craft()->request->getHostInfo();
			}

			if ( ! $apiKey )
			{
				// Redirect to CP if the user is logged in
				if ( craft()->userSession->isLoggedIn() )
				{
					craft()->request->redirect( $spamGuard->getPluginCpUrl() );
				}
				else
				{
					Craft::log('Spam Guard: You attempted to use Spam Guard without an API Key', LogLevel::Warning);
				}
			}

			$this->provider = ($originUrl && $apiKey) ? new \Akismet($originUrl, $apiKey) : false;
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

		// Bind the model
		$this->setModel($model);

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

	//--------------------------------------------------------------------------------

	protected function setModel(SpamGuardModel $model)
	{
		$this->model =& $model;
	}
	//--------------------------------------------------------------------------------
	
	public function getModel()
	{
		return $this->model instanceof SpamGuardModel ? $this->model : false;
	}
}
