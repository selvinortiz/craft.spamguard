<?php
namespace Craft;

/**
 * Class SpamGuardService
 *
 * @author	Selvin Ortiz
 * @package	Craft
 */

class SpamGuardService extends BaseApplicationComponent
{
	/**
	 * The akismet client written from scratch for Spam Guard by Selvin Ortiz
	 * @var SpamGuardKismet
	 */
	protected $kismet;

	/**
	 * @var BaseModel
	 */
	protected $pluginSettings;

	/**
	 * Initializes the component and the akismet client
	 */
	public function init()
	{
		$this->pluginSettings	= craft()->plugins->getPlugin('spamguard')->getSettings();
		$this->kismet			= new SpamGuardKismet($this->pluginSettings);
	}

	/**
	 * Checks whether the content is consider spam as far as akismet is concerned
	 *
	 * @param array $data The array containing the key/value pairs to validate
	 *
	 * @example
	 * $data		= array(
	 * 	'email'		=> 'john@smith.com',
	 * 	'author'	=> 'John Smith',
	 * 	'content'	=> 'We are Smith & Co, one of the best companies in the world.'
	 * )
	 *
	 * @note $data[content] is required
	 *
	 * @return bool
	 */
	public function isSpam(array $data=array())
	{
		$isKeyValid		= true;
		$flaggedAsSpam	= false;

		try
		{
			$flaggedAsSpam = $this->kismet->isSpam($data);
		}
		catch(SpamGuardInvalidKeyException $e)
		{
			if (craft()->userSession->isAdmin())
			{
				craft()->userSession->setError($e->getMessage());
				craft()->request->redirect(sprintf('/%s/settings/plugins/spamguard/', craft()->config->get('cpTrigger')));
			}
			else
			{
				$isKeyValid	= false;

				Craft::log($e->getMessage(), LogLevel::Warning);
			}
		}

		$params = array_merge($data, array(
			'isKeyValid'	=> $isKeyValid,
			'flaggedAsSpam'	=> $flaggedAsSpam
		));

		$this->addLog($params);

		return (bool) $flaggedAsSpam;
	}

	/**
	 * Contact Form beforeSend()
	 *
	 * Allows you to use spamguard alongside the Contact Form plugin by P&T
	 *
	 * @since	0.4.7
	 * @param	BaseModel $form
	 * @return	boolean
	 */
	public function detectContactFormSpam(BaseModel $form)
	{
		$data = array(
			'content'	=> $form->getAttribute('message'),
			'author'	=> $form->getAttribute('fromName'),
			'email'		=> $form->getAttribute('fromEmail'),
		);

		return $this->isSpam($data);
	}

	/**
	 * Guest Entries beforeSave()
	 *
	 * Allows you to use spamguard alongside the Guest Entries plugin by P&T
	 *
	 * @since	0.5.3
	 * @param	EntryModel $entry
	 * @return	boolean
	 */
	public function detectGuestEntrySpam(EntryModel $entry)
	{
		$data			= array();
		$emailField		= craft()->request->getPost('spamguard.emailField');
		$authorField	= craft()->request->getPost('spamguard.authorField');
		$contentField	= craft()->request->getPost('spamguard.contentField');

		if (empty($contentField))
		{
			SpamGuardPlugin::log('Guest Entries support is enabled in Spam Guard but no fields are configured for validation.', LogLevel::Warning);

			return false;
		}

		try
		{
			$data['email']		= craft()->templates->renderObjectTemplate($emailField, $entry);
			$data['author']		= craft()->templates->renderObjectTemplate($authorField, $entry);
			$data['content']	= craft()->templates->renderObjectTemplate($contentField, $entry);
		}
		catch(Exception $e)
		{
			SpamGuardPlugin::log('Unable to fetch the field values from the entry.', LogLevel::Error);

			return false;
		}

		return $this->isSpam($data);
	}

	/**
	 * Deletes a log by id
	 *
	 * @param $id
	 *
	 * @return bool|void
	 * @throws \CDbException
	 */
	public function deleteLog($id)
	{
		$log = SpamGuardRecord::model()->findById($id);

		if ($log)
		{
			$log->delete();

			return true;
		}

		return false;
	}

	/**
	 * @return mixed
	 */
	public function deleteLogs()
	{
		return SpamGuardRecord::model()->deleteAll();
	}

	/**
	 * @param $data
	 *
	 * @return bool
	 */
	protected function addLog($data)
	{
		if ($this->pluginSettings->getAttribute('logSubmissions'))
		{
			$record					= SpamGuardRecord::model()->populateRecord($data);

			if ($record->validate())
			{
				$record->save();
			}
		}

		return false;
	}

	/**
	 * Returns an array of logs if any are found
	 *
	 * @param array $attributes
	 *
	 * @return array
	 */
	public function getLogs(array $attributes=array())
	{
		$models		= array();
		$records	= SpamGuardRecord::model()->findAllByAttributes($attributes);

		if ($records)
		{
			foreach ($records as $record)
			{
				$models[] = SpamGuardModel::populateModel($record->getAttributes());
			}
		}

		return $models;
	}
}
