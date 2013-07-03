<?php
namespace Craft;

class SpamGuardModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'content' 	=> array(AttributeType::String, 'required'=>true, 'column'=>'text'),
			'author'	=> array(AttributeType::String, 'required'=>true, 'maxLength'=>50),
			'email'		=> array(AttributeType::Email, 'required'=>true),
			'message'	=> AttributeType::String
		);
	}
}