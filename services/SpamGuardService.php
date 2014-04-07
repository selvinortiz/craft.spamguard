<?php
namespace Craft;

use \SelvinOrtiz\Kismet\Kismet;
use \SelvinOrtiz\Kismet\InvalidKeyException;

class SpamGuardService extends BaseApplicationComponent
{
	protected $spamguard;

	public function loadSpamGuard()
	{
		if (is_null($this->spamguard))
		{
			$plugin	= craft()->plugins->getPlugin('spamguard');
			$params	= array(
				'apiKey'	=> $plugin->getSettings()->akismetApiKey,
				'originUrl'	=> $plugin->getSettings()->akismetOriginUrl
			);

			$this->spamguard = new Kismet($params);
		}

		return $this->spamguard;
	}

	/**
	 * The core validation method, checks whether content is considered spammy
	 *
	 * @param  array   $data	The array containing the key/value pairs to validate
	 *
	 * @example
	 * $data		= array(
	 * 	'email'		=> 'john@smith.com',
	 * 	'author'	=> 'John Smith',
	 * 	'content'	=> 'We are Smith & Co, one of the best companies in the world.'
	 * )
	 *
	 * $data['content'] (required)
	 *
	 * @return boolean
	 */
	public function isSpam(array $data=array())
	{
		$spamguard		= $this->loadSpamGuard();
		$isKeyValid		= true;
		$flaggedAsSpam	= false;

		try
		{
			$flaggedAsSpam = $spamguard->isSpam($data);
		}
		catch(InvalidKeyException $e)
		{
			if (craft()->userSession->isAdmin())
			{
				$cp = craft()->config->get('cpTrigger');

				craft()->userSession->setError($e->getMessage());
				craft()->request->redirect("/{$cp}/settings/plugins/spamguard");
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
	 * Allows you to use spamguard alongside the contactform plugin by P&T
	 *
	 * @since	0.4.7
	 * @param	BaseModel $form
	 * @return	boolean
	 */
	public function detectContactFormSpam(BaseModel $form)
	{
		$data = array(
			'content'	=> $form->message,
			'author'	=> $form->fromName,
			'email'		=> $form->fromEmail
		);

		return $this->isSpam($data);
	}

	/**
	 * Guest Entries beforeSave()
	 *
	 * Allows you to use spamguard alongside the guestentries plugin by P&T
	 *
	 * @since	0.5.3
	 * @param	BaseModel $entry
	 * @return	boolean
	 */
	public function detectGuestEntrySpam(BaseModel $entry)
	{
		$data	= array();
		$fields	= craft()->request->getPost('spamguard.validationFields');

		if (empty($fields))
		{
			Craft::log(Craft::t('Guest Entries support is enabled in Spam Guard but no fields are configured for validation.'), LogLevel::Warning);

			return false;
		}

		$email		= $this->getEntryFieldValue(craft()->request->getPost('spamguard.emailField'), $entry);
		$author		= $this->getEntryFieldValue(craft()->request->getPost('spamguard.authorField'), $entry);
		$fields		= array_map('trim', explode(',', $fields));
		$content	= '';

		if ($email)
		{
			$data['email'] = $email;
		}

		if ($author)
		{
			$data['author'] = $author;
		}

		foreach ($fields as $field)
		{
			if (isset($entry->$field))
			{
				$content .= (string) $entry->$field.PHP_EOL;
			}
		}

		$content = trim($content);

		if (empty($content))
		{
			return false;
		}
		
		$data['content'] = $content;

		return $this->isSpam($data);
	}

	public function deleteLog($id)
	{
		$log = SpamGuardRecord::model()->findById($id);

		if ($log)
		{
			return $log->delete();
		}

		return false;
	}

	public function deleteLogs()
	{
		return SpamGuardRecord::model()->deleteAll();
	}

	protected function addLog($data)
	{
		if (craft()->plugins->getPlugin('spamguard')->getSettings()->logSubmissions)
		{
			$record					= new SpamGuardRecord();
			$record->email			= $this->fetch('email', $data);
			$record->author			= $this->fetch('author', $data);
			$record->content		= $this->fetch('content', $data);
			$record->isKeyValid		= $this->fetch('isKeyValid', $data, null);
			$record->flaggedAsSpam	= $this->fetch('flaggedAsSpam', $data, true);
			$record->isSpam			= $this->fetch('isSpam', $data, null);
			$record->isHam			= $this->fetch('isHam', $data, null);
			$record->data			= $this->fetch('data', $data, array());

			if ($record->validate())
			{
				$record->save();
			}
		}

		return false;
	}

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

	public function fetch($key, array $arr = array(), $def = false)
	{
		return array_key_exists($key, $arr) ? $arr[$key] : $def;
	}

	/**
	 * Parses a field string from a spamguard input to return the entry value for that field
	 *
	 * @param	string		$field		The entry field string to fetch > "firstName, lastName"
	 * @param	BaseModel	$entry		The entry model provided by GuestEntriesEvent
	 * @param	mixed		$default	The default value to return if no entry->field found
	 */
	public function getEntryFieldValue($field = '', BaseModel $entry, $default = null)
	{
		if (empty($field))
		{
			return $default;
		}

		if (stripos($field, ',') === false)
		{
			return ( ! empty($field) && isset($entry->$field) ) ? $entry->$field : $default;
		}

		$values	= array();
		$fields	= array_map('trim', explode(',', $field));

		unset($field);

		foreach ($fields as $field)
		{
			if ( ! empty($field) && isset($entry->$field) )
			{
				$values[] = (string) $entry->field;
			}
		}

		return count($values) ? implode(' ', $values) : $default;
	}
}
