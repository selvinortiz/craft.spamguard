<?php
namespace Craft;

class SpamGuardVariable
{
    public function isSpam($content, $author='', $email='')
    {
    	try
    	{
        	return (bool) craft()->spamGuard_spam->isSpam($content, $author, $email);
        }
        catch (\Exception $e)
        {
        	// Maybe flash a message here?
        	return false;
        }
    }
}
