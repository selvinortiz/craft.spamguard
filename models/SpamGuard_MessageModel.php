<?php
namespace Craft;

class SpamGuard_MessageModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(
			'subject'		=> array(AttributeType::String, 'maxLength'=>255),
			'content' 		=> array(AttributeType::String,	'required'=>true, 'column'=>'text'),
			'author'		=> array(AttributeType::String, 'required'=>true, 'maxLength'=>100),
			'email'			=> array(AttributeType::Email,	'required'=>true, 'maxLength'=>100),
			// --
			'sendToName'	=> array(AttributeType::String, 'required'=>true),
			'sendToEmail'	=> array(AttributeType::Email, 'required'=>true)
		);
	}
}
