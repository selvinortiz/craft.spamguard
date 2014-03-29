<?php
namespace SelvinOrtiz\Kismet\Exception;

use Craft\Craft;

class InvalidKeyException extends \Exception
{
	public function __construct($message=null, $code=0, $previous=null)
	{
		if (is_null($message))
		{
			$message = Craft::t('Your API Key is not valid or has expired.');
		}

		parent::__construct($message, $code, $previous);
	}
}
