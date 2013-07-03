<?php
namespace Craft;

class SpamGuard_SpamController extends BaseController
{
	protected $messages         = array();
	protected $allowAnonymous   = true;
	
	//--------------------------------------------------------------------------------
	
	public function actionIsSpam()
	{
		$this->requirePostRequest();

		$spam = new SpamGuard_SpamModel();

		$spam->content  = Input::get('content');
		$spam->author   = Input::get('author');
		$spam->email    = Input::get('email');

		if ( $spam->validate() )
		{
			if ( craft()->spamGuard_spam->isSpam($spam) )
			{
				$spam->message = $this->getMessage('totalSpam');
			}
			else
			{
				$spam->message = $this->getMessage('noSpam', false);
			}
		}

		craft()->urlManager->setRouteVariables( array('spam'=>$spam) );
	}

	//--------------------------------------------------------------------------------

	protected function getMessage($key, $error=true)
	{
		$this->loadMessages();

		if ( array_key_exists($key, $this->messages) )
		{
			if ( $error )
			{
				return sprintf('<em class="error">%s</em>', $this->messages[$key]);
			}
			else
			{
				return sprintf('<em>%s</em>', $this->messages[$key]);
			}
		}

		return $default;
	}

	//--------------------------------------------------------------------------------

	protected function loadMessages()
	{
		if ( ! count($this->messages) )
		{
			$this->messages = array(
				'noSpam'    => Craft::t('The data submitted checks out as clean.'),
				'totalSpam' => Craft::t('The data submitted is total Spam!')
			);
		}
	}
}
