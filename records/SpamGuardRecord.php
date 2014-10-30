<?php
namespace Craft;

/**
 * Class SpamGuardRecord
 *
 * @author		Selvin Ortiz <selvin@selv.in>
 * @package		Craft
 * @copyright	2014 Selvin Ortiz
 * @license		[MIT]
 */
class SpamGuardRecord extends BaseRecord
{
	public function getTableName()
	{
		return 'spamguard';
	}

	public function defineAttributes()
	{
		return array(
			'email'			=> array(AttributeType::Email,	'required'	=> true),
			'author'		=> array(AttributeType::String,	'maxLength'	=> 50),
			'content'		=> array(AttributeType::String,	'column'	=> ColumnType::Text),
			'isKeyValid'	=> AttributeType::Bool,
			'flaggedAsSpam'	=> AttributeType::Bool,
			'isSpam'		=> AttributeType::Bool,
			'isHam'			=> AttributeType::Bool,
			'data'			=> AttributeType::Mixed
		);
	}

	public function defineIndexes()
	{
		return array(
			array('columns' => array('email'))
		);
	}
}
