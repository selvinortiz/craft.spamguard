<?php
namespace Craft;

class SpamGuardService extends BaseApplicationComponent
{
	protected $plugin;
	protected $provider;
	protected $settings;

	//--------------------------------------------------------------------------------

	public function __construct()
	{
		$this->plugin 	= craft()->plugins->getPlugin(SpamGuardPlugin::PLUGIN_HANDLE);

		$this->settings = $this->plugin->getSettings();

		if ( class_exists('Akismet') )
		{
			if ( isset($this->settings['akismetApiKey']) )
			{
				throw new \Exception('Please head to the plugin settings and add your API Key @ '.__METHOD__);
			}

			if ( isset($this->settings['akismetOriginUrl']) )
			{
				$this->settings['akismetOriginUrl'] = craft()->requrest->getHostInfo();
			}

			$this->provider	= new \Akismet($this->settings['akismetOriginUrl'], $this->settings['akismetApiKey']);
		}
		else
		{
			throw new \Exception('The Akismet class is not available, check the autoloader and/or namespace @ '.__METHOD__);
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
					if ( $this->provider->isKeyValid() )
					{
						return true;
					}
					else
					{
						throw new \Exception('Your API Key may be invalid or may has expired @'.__METHOD__);
					}
				}
			}
		}

		// Not spam or no settings yet!
		return false;
	}
}
