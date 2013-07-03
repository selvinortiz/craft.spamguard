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
 * arrayGet()
 * Helper utility to get an array value by key or a defualt the key is not found
 *
 * @param	string	$key	The array key to search for
 * @param	array	$arr	The array to search in
 * @param	mix 	$def	The default value to return if the key is not found
 *
 * @return	mix 	$def
 */

if ( ! function_exists('arrayGet') )
{
	function arrayGet( $key, $arr=array(), $def=false )
	{
		if ( is_string($key) && array_key_exists($key, $arr) )
		{
			return $arr[ $key ];
		}

		return $def;
	}
}

//--------------------------------------------------------------------------------

/**
 * dd()
 * Dump & Die
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
