<?php
namespace Craft;

class SpamGuardVariable
{
    public function isSpam($content, $author='', $email='')
    {
        return (bool) craft()->spamGuard_spam->isSpam($content, $author, $email);
    }
}
