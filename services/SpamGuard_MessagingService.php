<?php
namespace Craft;

class SpamGuard_MessagingService extends BaseApplicationComponent
{
	public function sendMessage(SpamGuard_MessageModel $message, Model $settings)
	{
		if ( $message->validate() )
		{
			$email = new EmailModel();

			$email->fromEmail = $message->email;
			$email->fromName  = $message->author;
			$email->toEmail   = $message->sendToEmail;
			$email->subject   = $message->subject;
			$email->body      = $email->htmlBody = $this->getMessage($message, $settings);

			try
			{
				return craft()->email->sendEmail($email);
			}
			catch (\phpmailerException $e)
			{
				return false;
			}
		}

		return false;
	}

	//--------------------------------------------------------------------------------

	protected function getMessage(SpamGuard_MessageModel $message, Model $settings)
	{
		$vars = array(
			'subject'	=> $message->subject,
			'content'	=> $message->content,
			'author'	=> $message->author,
			'email'		=> $message->email
		);

		if ( ! empty($settings->emailTemplate) && stripos($settings->emailTemplate, '{{') !== false )
		{
			return craft()->templates->renderString($settings->emailTemplate, $vars);
		}
		else
		{
			return craft()->templates->render('spamguard/__message.twig', $vars);
		}
	}
}