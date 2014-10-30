<?php
namespace Craft;

/**
 * Class SpamGuardModel
 *
 * @author		Selvin Ortiz <selvin@selv.in>
 * @package		Craft
 * @copyright	2014 Selvin Ortiz
 * @license		[MIT]
 */
class SpamGuardModel extends BaseModel
{
	public function defineAttributes()
	{
		return array(
			'id'			=> array(AttributeType::Number),
			'email'			=> array(AttributeType::Email),
			'author'		=> array(AttributeType::String,	'maxLength'	=> 50),
			'content'		=> array(AttributeType::String,	'column'	=> ColumnType::Text),
			'isKeyValid'	=> AttributeType::Bool,
			'flaggedAsSpam'	=> AttributeType::Bool,
			'isSpam'		=> AttributeType::Bool,
			'isHam'			=> AttributeType::Bool,
			'data'			=> AttributeType::Mixed,
			'dateCreated'	=> AttributeType::DateTime,
			'dateUpdated'	=> AttributeType::DateTime
		);
	}
}
