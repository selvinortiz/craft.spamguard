<?php
namespace Craft;

class SpamGuard_AkismetModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'akismetOriginUrl'	=> array(AttributeType::String, 'required'=>true, 'maxLength'=>255),
			'akismetApiKey'		=> array(AttributeType::String, 'required'=>true, 'minLength'=>12)
		);
	}
}
