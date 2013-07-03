<?php
namespace Craft;

/**
 * toJson()
 * Utility to encode arrays into JSON using the Yii JSON parser
 *
 * @param	array	$data	The associative array to encode
 * @return	string			The JSON encoded string
 */

if ( ! function_exists('toJson') )
{
	function toJson( $data=array() )
	{
		return \CJSON::encode($data);
	}
}

//--------------------------------------------------------------------------------

/**
 * getJson()
 * Utility to decode JSON using the Yii JSON parser
 *
 * @param	string	$str	The JSON string to decode
 * @return	string			The array/object decoded from the JSON string
 */

if ( ! function_exists('getJson') )
{
	function getJson( $str, $asarray=false )
	{
		return \CJSON::decode($str, $asarray);
	}
}

//--------------------------------------------------------------------------------

/**
 * dd()
 * Debug & Die
 */

if ( ! function_exists('dd') )
{
	function dd($var, $die=true)
	{
		Craft::dump($var);

		if ($die) {
			exit;
		}
	}
}
