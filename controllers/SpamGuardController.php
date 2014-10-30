<?php
namespace Craft;

/**
 * Class SpamGuardController
 *
 * @author		Selvin Ortiz <selvin@selv.in>
 * @package		Craft
 * @copyright	2014 Selvin Ortiz
 * @license		[MIT]
 */
class SpamGuardController extends BaseController
{
	/**
	 * Renders the index template to improve performance
	 *
	 * @return string
	 */
	public function actionIndex()
	{
		craft()->templates->includeCssResource('spamguard/css/spamguard.css');

		$this->renderTemplate('spamguard/_index', spamGuard()->getTemplateVariables(true));
	}

	/**
	 * Removes a log from the db if it exists and the user has the proper permissions
	 *
	 * @throws HttpException
	 */
	public function actionDeleteLog()
	{
		$this->requireAdmin();
		$this->requirePostRequest();

		$id			= craft()->request->getPost('id');
		$deleted	= false;

		if ($id)
		{
			$deleted = spamGuard()->deleteLog($id);
		}

		if ($deleted)
		{
			craft()->userSession->setNotice(Craft::t('Log deleted successfully.'));
		}
		else
		{
			craft()->userSession->setError(Craft::t('Unable to delete the log.'));
		}

		$this->redirectToPostedUrl();
	}

	/**
	 * Removes all logs from the db if any exist and the user has the proper permissions
	 *
	 * @throws HttpException
	 */
	public function actionDeleteLogs()
	{
		$this->requireAdmin();
		$this->requirePostRequest();

		$confirmation	= craft()->request->getPost('confirmation', false);

		if ($confirmation)
		{
			$deleted = spamGuard()->deleteLogs();

			if ($deleted)
			{
				craft()->userSession->setNotice(Craft::t('All logs were deleted successfully.'));
			}
			else
			{
				craft()->userSession->setError(Craft::t('Unable to delete all logs.'));
			}
		}
		else
		{
			craft()->userSession->setError(Craft::t('Please check the box to confirm this action.'));
		}

		$this->redirectToPostedUrl();
	}
}
