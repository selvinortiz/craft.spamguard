<?php
namespace Craft;

//--------------------------------------------------------------------------------

$classMap = array( 'Akismet' => 'packages/akismet/Akismet.php');

//--------------------------------------------------------------------------------

if ( ! defined('__FUNCTIONS') )
{
	require_once __DIR__.'/Functions.php';
}

//--------------------------------------------------------------------------------

if ( ! defined('__BRIDGE') )
{
	require_once __DIR__.'/Bridge.php';
}

//--------------------------------------------------------------------------------

foreach ($classMap as $className => $filePath)
{
	$file = __DIR__.'/'.ltrim($filePath, '/');

	if (! class_exists($className) && file_exists($file))
	{
		require_once($file);
	}
}
