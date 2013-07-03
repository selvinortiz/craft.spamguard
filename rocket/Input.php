<?php
namespace Craft;

class Input
{
	public static function get($key, $default=false)
	{
		if ( is_string($key) && array_key_exists($key, $_POST) )
		{
			return $_POST[$key];
		}

		return $default;
	}
}