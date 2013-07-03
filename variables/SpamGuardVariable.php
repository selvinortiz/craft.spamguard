<?php
namespace Craft;

class SpamGuardVariable
{
	public function isSpam( $data=array() )
	{
		try
		{
			$model = SpamGuardModel::populateModel($data);

			if ( $model->validate() )
			{
				return (bool) craft()->spamGuard->isSpam($model);
			}

			return false;
		}
		catch (\Exception $e)
		{
			return false;
		}
	}
}
