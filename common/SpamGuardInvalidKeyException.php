<?php
namespace Craft;

class SpamGuardInvalidKeyException extends Exception
{
	public function __construct($message=null, $code=0, $previous=null)
	{
		if (null === $message)
		{
			$message = Craft::t('Your API Key is not valid or has expired.');
		}

		parent::__construct($message, $code, $previous);
	}
}
