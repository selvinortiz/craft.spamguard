<?php
namespace Craft;

use tijsverkoyen\akismet\Akismet;

class SpamGuard_SpamService extends BaseApplicationComponent
{
    protected $plugin;
    protected $provider;
    protected $settings = array();

    public function __construct()
    {
        $this->plugin 	= craft()->plugins->getPlugin(SpamGuardPlugin::PLUGIN_HANDLE);
        $this->settings = $this->plugin->getSettings();

        if ( class_exists('tijsverkoyen\\akismet\\Akismet') )
        {
            if ( empty($this->settings['akismetApiKey']) )
            {
                throw new \Exception('Please head to the plugin settings and add your API Key @ '.__METHOD__);
            }

            if ( empty($this->settings['akismetOriginUrl']) )
            {
                $this->settings['akismetOriginUrl'] = craft()->requrest->getHostInfo();
            }

            $this->provider	= new Akismet(
                $this->settings['akismetApiKey'],
                $this->settings['akismetOriginUrl']
            );
        }
        else
        {
            throw new \Exception('The Akismet class is not available, check the autoloader and/or namespace @ '.__METHOD__);
        }
    }

    public function isSpam($content, $author='', $email='')
    {
        if ( is_object($this->provider) )
        {
            $this->provider->setUserAgent($_SERVER['HTTP_USER_AGENT']);

            if ( $this->provider->isSpam( $content, $author, $email ) )
            {
                if ( $this->provider->verifyKey() )
                {
                    return true;
                }
                else
                {
                    throw new \Exception('Your API Key may be invalid or may has expired @'.__METHOD__);
                }
            }
        }

        // Not spam or no settings yet!
        return false;
    }
}
