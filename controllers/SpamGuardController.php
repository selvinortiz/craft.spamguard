<?php
namespace Craft;

class SpamGuardController extends BaseController
{
	protected $plugin			= null;
	protected $allowAnonymous	= true;

	public function __construct()
	{
		$this->plugin = craft()->plugins->getPlugin('spamGuard');
	}

	public function actionSendMessage()
	{
		$this->requirePostRequest();

		$settings 			= $this->plugin->getSettings();
		$params 			= array(
			'subject'		=> craft()->request->getPost('subject'),
			'content'		=> craft()->request->getPost('content'),
			'author'		=> craft()->request->getPost('author'),
			'email'			=> craft()->request->getPost('email'),
			'sendToName'	=> $settings->sendToName,
			'sendToEmail'	=> $settings->sendToEmail
		);

		$spam 				= SpamGuardModel::populateModel($params);
		$message			= SpamGuard_MessageModel::populateModel($params);

		if ($message->validate())
		{
			$message->subject = $this->getSubject($settings);

			if ($spam->validate())
			{
				if (craft()->spamGuard->detectSpam($spam))
				{
					$this->handleSpammySubmission();
				}
				else
				{
					if (craft()->spamGuard_message->sendMessage($message, $settings))
					{
						$this->handleSuccessfulSubmission();
					}
					else
					{
						$feedback = $this->getNotice('The server is acting up, we could not send the message: (', false);
						craft()->urlManager->setRouteVariables( array('feedback'=>$feedback) );
					}
				}
			}
		}
		else
		{
			craft()->urlManager->setRouteVariables(array('message'=>$message));
		}
	}

	public function handleSuccessfulSubmission()
	{
		$successfulReturn = craft()->request->getPost('successfulReturn');

		if ($successfulReturn)
		{
			$this->redirectTo($successfulReturn);
		}

		craft()->request->redirectToPostedUrl();
	}

	public function handleSpammySubmission()
	{
		$successfulReturn	= craft()->request->getPost('successfulReturn');
		$unsuccessfulReturn	= craft()->request->getPost('unsuccessfulReturn');

		if ($successfulReturn || $unsuccessfulReturn)
		{
			$this->redirectTo($unsuccessfulReturn ?: $successfulReturn);
		}

		craft()->request->redirectToPostedUrl();
	}

	//--------------------------------------------------------------------------------
	// @HELPERS > actionSendMessage()
	//--------------------------------------------------------------------------------

	protected function getSubject(Model $settings)
	{
		$prefix		= trim($settings->subjectPrefix);
		$subject	= trim(craft()->request->getPost('subject'));

		if ( $prefix && $subject )
		{
			return sprintf('%s (%s)', $prefix, $subject);
		}

		if ( !empty($prefix) )
		{
			return $prefix;
		}

		if ( ! empty($subject) )
		{
			return $subject;
		}

		return false;
	}

	protected function getNotice($msg, $success=true)
	{
		if ($success)
		{
			return array('message'=>trim($msg), 'type'=>'success');
		}
		else
		{
			return array('message'=>trim($msg), 'type'=>'warning');
		}
	}

	public function actionSubmitHam()
	{
		// @expect 1.0.0
	}

	public function actionSubmitSpam()
	{
		// @expect 1.0.0
	}
}
