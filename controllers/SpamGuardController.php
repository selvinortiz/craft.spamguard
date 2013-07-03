<?php
namespace Craft;

class SpamGuardController extends BaseController
{
	protected $allowAnonymous = true;
	
	//--------------------------------------------------------------------------------
	
	public function actionIsSpam()
	{
		// Will use the POST data so content, author, and email must be available
		return craft()->spamGuard->isSpam();
	}
}
