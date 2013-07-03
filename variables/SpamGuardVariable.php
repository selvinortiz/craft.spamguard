<?php
namespace Craft;

class SpamGuardVariable
{
	public function isSpam( $data=array() )
	{
		try
		{
			$spamModel = SpamGuard_SpamModel::populateModel($data);

			if ( $spamModel->validate() )
			{
				return (bool) craft()->spamGuard_spam->isSpam($spamModel);
			}

			return false;
		}
		catch (\Exception $e)
		{
			return false;
		}
	}
}
