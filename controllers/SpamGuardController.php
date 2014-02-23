<?php
namespace Craft;

class SpamGuardController extends BaseController
{
	protected $allowAnonymous = array('actionDeleteLog');

	public function actionDeleteLog()
	{
		$this->requireAdmin();
		$this->requirePostRequest();

		$deleted = craft()->spamGuard->deleteLog(@$_POST['id']);

		if ($deleted)
		{
			craft()->userSession->setNotice($this->msg('Log deleted successfully'));
		}
		else
		{
			craft()->userSession->setError($this->msg('Unable to delete that log'));
		}

		$this->redirectToPostedUrl();
	}

	public function actionDeleteLogs()
	{
		$this->requireAdmin();
		$this->requirePostRequest();

		if (isset($_POST['confirmation']) && $_POST['confirmation'] == true)
		{
			$deleted = craft()->spamGuard->deleteLogs();

			if ($deleted)
			{
				craft()->userSession->setNotice($this->msg('Everything was trashed'));
			}
			else
			{
				craft()->userSession->setError($this->msg('Unable to trash everything'));
			}
		}
		else
		{
			craft()->userSession->setError($this->msg('You must confirm this action'));
		}

		$this->redirectToPostedUrl();
	}

	public function actionSubmitSpam()
	{
		//...
	}

	public function actionSubmitHam()
	{
		//...
	}

	public function msg($message='', $prefix='Spam Guard: ')
	{
		return $prefix.Craft::t($message);
	}
}
