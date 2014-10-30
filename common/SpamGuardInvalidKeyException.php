<?php
namespace Craft;

/**
 * Class SpamGuardInvalidKeyException
 *
 * @author		Selvin Ortiz <selvin@selv.in>
 * @package		Craft
 * @copyright	2014 Selvin Ortiz
 * @license		[MIT]
 */
class SpamGuardInvalidKeyException extends Exception
{
	/**
	 * @inheritdoc
	 */
	public function __construct($message=null, $code=0, $previous=null)
	{
		if (null === $message)
		{
			$message = Craft::t('Your API Key is not valid or has expired, please fix that.');
		}

		parent::__construct($message, $code, $previous);
	}
}
