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
		// Get Settings
		$plugin		= craft()->plugins->getPlugin(SpamGuardPlugin::PLUGIN_HANDLE);
		$settings	= $plugin->getSettings();
		$originUrl	= $settings->akismetOriginUrl ?: craft()->request->getHostInfo();
		$apiKey		= $settings->akismetApiKey;

		// Load Model
		$akismetM	= SpamGuard_AkismetModel::populateModel( array('akismetApiKey'=>$apiKey, 'akismetOriginUrl'=>$originUrl) );

		// Ensure safe use of the Akismet service provider
		if ( $akismetM->validate() && class_exists( self::PROVIDER_CLASS_NAME ) )
		{
			$this->provider = new \Akismet($akismetM->akismetOriginUrl, $akismetM->akismetApiKey);
		}
		else
		{
			$this->provider = false;

			if ( ! count($akismetM->getErrors()) )
			{
				$this->handleMissingAkismetClass($plugin);
			}

			elseif ( $akismetM->getError('akismetApiKey') )
			{
				$this->handleMissingApiKey($plugin);
			}

			elseif ( $akismetM->getError('akismetOriginUrl') )
			{
				$this->handleMissingOriginUrl($plugin);
			}
			else
			{
				$this->handleSkyFalling($plugin);
			}
		}
	}

	//--------------------------------------------------------------------------------

	public function detectSpam($data=false)
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
						$this->handleInvalidKey($spamGuard);
					}
				}
			}
		}

		return false;
	}

	//--------------------------------------------------------------------------------
	// @=HELPERS
	//--------------------------------------------------------------------------------
	
	protected function handleInvalidKey($plugin)
	{
		throw new \Exception('Your API Key may be invalid or may have expired');
	}
	
	//--------------------------------------------------------------------------------

	protected function handleMissingApiKey($plugin)
	{
		if ( craft()->userSession->isLoggedIn() )
		{
			craft()->request->redirect( $plugin->getPluginCpUrl() );
		}
		else
		{
			Craft::log('Spam Guard: You attempted to use Spam Guard without an API Key', LogLevel::Warning);
		}

	}

	//--------------------------------------------------------------------------------
	
	protected function handleMissingOriginUrl()
	{
		if ( craft()->userSession->isLoggedIn() )
		{
			craft()->request->redirect( $plugin->getPluginCpUrl() );
		}
		else
		{
			Craft::log('Spam Guard: You attempted to use Spam Guard without setting up the origin URL', LogLevel::Warning);
		}
	}

	//--------------------------------------------------------------------------------
	
	protected function handleMissingAkismetClass()
	{
		throw new \Exception('The Akismet was not loaded properly.');
	}

	//--------------------------------------------------------------------------------
	
	protected function handleSkyFalling()
	{
		throw new \Exception('We were unable to process your request.');
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
