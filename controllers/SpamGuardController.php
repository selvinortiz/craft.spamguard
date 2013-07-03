<?php
namespace Craft;

class SpamGuardController extends BaseController
{
	protected $response 		= '';
	protected $messages			= array();
	protected $allowAnonymous	= true;
	
	//--------------------------------------------------------------------------------
	
	public function actionIsSpam()
	{
		$this->requirePostRequest();

		$spam = new SpamGuardModel();

		$spam->content	= Input::get('content');
		$spam->author	= Input::get('author');
		$spam->email	= Input::get('email');

		if ( $spam->validate() )
		{
			if ( craft()->spamGuard->isSpam($spam) )
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
	
	protected function isSpam($model=false)
	{
		if ( ! is_object($model) )
		{
			$model = new SpamGuardModel();

			$model->content	= Input::get('content');
			$model->author	= Input::get('author');
			$model->email	= Input::get('email');
		}

		if ( $model->validate() )
		{
			return craft()->spamGuard->isSpam($model);
		}

		$this->addResponse('The model did not validate @ '.__METHOD__);

		return false;
	}

	//--------------------------------------------------------------------------------

	protected function addResponse($msg='')
	{
		$this->response = $msg;
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
			$this->messages	= array(
				'noSpam'	=> Craft::t('The data submitted checks out as clean.'),
				'totalSpam'	=> Craft::t('The data submitted is total Spam!')
			);
		}
	}
}
