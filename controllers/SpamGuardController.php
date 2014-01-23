<?php
namespace Craft;

class SpamGuardController extends BaseController
{
	protected $allowAnonymous = array('actionDeleteLog');

	public function actionDeleteLog()
	{
		$this->requirePostRequest();

		$deleted = craft()->spamGuard->deleteLog(@$_POST['id']);

		if ($deleted)
		{
			craft()->userSession->setNotice(Craft::t('Spam Guard: Log deleted successfully'));
		}
		else
		{
			craft()->userSession->setError(Craft::t('Spam Guard: Unable to delete that log'));
		}

		$this->redirectToPostedUrl();
	}

	public function actionDeleteLogs()
	{
		$this->requirePostRequest();

		if (isset($_POST['confirmation']) && $_POST['confirmation'] == true)
		{
			$deleted = craft()->spamGuard->deleteLogs();

			if ($deleted)
			{
				craft()->userSession->setNotice(Craft::t('Spam Guard: Everything was trashed!'));
			}
			else
			{
				craft()->userSession->setError(Craft::t('Spam Guard: Unable to trash everything!'));
			}
		}
		else
		{
			craft()->userSession->setError(Craft::t('Spam Guard: You must confirm this action!'));
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
}
