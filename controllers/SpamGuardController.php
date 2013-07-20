<?php
namespace Craft;

class SpamGuardController extends BaseController
{
	protected $plugin;
	protected $allowAnonymous = true;

	//--------------------------------------------------------------------------------

	public function __construct()
	{
		$this->plugin	= craft()->plugins->getPlugin('spamGuard');
	}

	//--------------------------------------------------------------------------------

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

		if ( $message->validate() )
		{
			// Since we are not sending back to the form we can load the full subject into the $message
			$message->subject = $this->getSubject($settings);

			if ( $spam->validate() )
			{
				if ( craft()->spamGuard->detectSpam($spam) )
				{
					$this->logSpam($spam);
					$this->redirect('http://spam.abuse.net/');
				}

				if ( craft()->spamGuard_messaging->sendMessage($message, $settings) )
				{
					$feedback = $this->getNotice('Yay, your message was sent successfully.', true);
					craft()->urlManager->setRouteVariables( array('feedback'=>$feedback) );
				}
				else
				{
					$feedback = $this->getNotice('The server is acting up, we could not send the message: (', false);
					craft()->urlManager->setRouteVariables( array('feedback'=>$feedback) );
				}
			}
		}
		else
		{
			$feedback = $this->getNotice('Spill the beans please!', false);
			craft()->urlManager->setRouteVariables( array('feedback'=>$feedback, 'message'=>$message) );
		}
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

	//--------------------------------------------------------------------------------

	protected function getNotice($msg, $success=true)
	{
		if ( $success )
		{
			return array('message'=>trim($msg), 'type'=>'success');
		}
		else
		{
			return array('message'=>trim($msg), 'type'=>'warning');
		}
	}

	//--------------------------------------------------------------------------------

	protected function logSpam(SpamGuardModel $spam)
	{
		$msg = sprintf("\n@SPAMGUARD (##) AUTHOR %s : EMAIL %s : SPAM \n%s \n(##)\n", $spam->author, $spam->email, $spam->content);

		Craft::log($msg, LogLevel::Error);
	}

	//--------------------------------------------------------------------------------

	public function actionSubmitHam()
	{
		// @expect 1.0.0
	}

	//--------------------------------------------------------------------------------

	public function actionSubmitSpam()
	{
		// @expect 1.0.0
	}
}
