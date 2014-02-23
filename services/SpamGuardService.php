<?php
namespace Craft;

use \selvinortiz\spamguard\Kismet;

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

			require_once craft()->path->getPluginsPath().'spamguard/library/Kismet.php';

			$this->spamguard = new Kismet($params);
		}

		return $this->spamguard;
	}

	/**
	 * isSpam()
	 * $data may contain email, author, and content keys.
	 * 
	 * @param  array   $data
	 * @return boolean
	 */
	public function isSpam(array $data=array())
	{
		$spamguard		= $this->loadSpamGuard();
		$isKeyValid		= $spamguard->isKeyValid();
		$flaggedAsSpam	= true;

		if ($isKeyValid)
		{
			$flaggedAsSpam = $spamguard->isSpam($data);
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

	public function fetch($key, array $arr=array(), $def=false)
	{
		return array_key_exists($key, $arr) ? $arr[$key] : $def;
	}
}

