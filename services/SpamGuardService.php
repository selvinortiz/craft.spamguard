<?php
namespace Craft;

class SpamGuardService extends BaseApplicationComponent
{
	protected $model;		// SpamGuardModel instance
	protected $provider;	// Akismet instance

	public function __construct(BaseModel $settings)
	{
		$plugin			= craft()->plugins->getPlugin('spamGuard');
		$settings		= $plugin->getSettings();
		$apiKey			= $settings->akismetApiKey;
		$originUrl		= $settings->akismetOriginUrl ?: craft()->request->getHostInfo();
		$params			= array('akismetApiKey' => $apiKey, 'akismetOriginUrl' => $originUrl);
		$akismet		= SpamGuard_AkismetModel::populateModel($params);

		if ($akismet->validate() && class_exists('Akismet'))
		{
			$this->provider = new \Akismet($akismet->akismetOriginUrl, $akismet->akismetApiKey);
		}
		else
		{
			$this->provider = false;

			if (!count($akismet->getErrors()))
			{
				$this->handleMissingAkismetClass($plugin);
			}
			elseif ( $akismet->getError('akismetApiKey') )
			{
				$this->handleMissingApiKey($plugin);
			}
			elseif ( $akismet->getError('akismetOriginUrl') )
			{
				$this->handleMissingOriginUrl($plugin);
			}
			else
			{
				$this->handleSkyFalling($plugin);
			}
		}
	}

	public function detectSpam($data=false)
	{
		if (is_object($data) && ($data instanceof SpamGuardModel))
		{
			$model = $data;
		}
		elseif (is_array($data) && count($data))
		{
			$model = SpamGuardModel::populateModel($data);
		}
		else
		{
			$model = new SpamGuardModel();

			$model->content	= craft()->request->getPost('content');
			$model->author	= craft()->request->getPost('author');
			$model->email	= craft()->request->getPost('email');
		}

		// Bind the model
		$this->setModel($model);

		if ($model->validate())
		{
			if (is_object($this->provider))
			{
				$this->provider->setCommentContent($model->content);
				$this->provider->setCommentAuthor($model->author);
				$this->provider->setCommentAuthorEmail($model->email);
				$this->provider->setCommentUserAgent($_SERVER['HTTP_USER_AGENT']);
				$this->provider->setUserIp($_SERVER['REMOTE_ADDR']);

				if ($this->provider->isCommentSpam())
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

	/**
	 * Contact Form beforeSend()
	 *
	 * Allows you to use spamguard alongside the contactform plugin by P&T
	 *
	 * @since	0.4.7
	 * @return	boolean
	 */
	public function detectContactFormSpam(BaseModel $email)
	{
		$data = array(
			'content'	=> $email->message,
			'author'	=> $email->fromName,
			'email'		=> $email->fromEmail
		);

		return (bool) $this->detectSpam($data);
	}

	//--------------------------------------------------------------------------------
	// @=HELPERS
	//--------------------------------------------------------------------------------

	protected function handleInvalidKey($plugin)
	{
		throw new \Exception('Your API Key may be invalid or may have expired');
	}

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

	protected function handleMissingAkismetClass()
	{
		throw new \Exception('The Akismet was not loaded properly.');
	}

	protected function handleSkyFalling()
	{
		throw new \Exception('We were unable to process your request.');
	}

	protected function setModel(SpamGuardModel $model)
	{
		$this->model =& $model;
	}

	public function getModel()
	{
		return $this->model instanceof SpamGuardModel ? $this->model : false;
	}
}
