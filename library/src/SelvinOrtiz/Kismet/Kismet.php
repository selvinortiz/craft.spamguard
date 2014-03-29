<?php
namespace SelvinOrtiz\Kismet;

use Guzzle\Http\Client;

/**
 * Kismet is a simple and modern client for the Akismet API
 *
 * @author		Selvin Ortiz <selvin@selvin.co>
 * @package		spamguard
 * @version		1.0
 * @license		http://opensource.org/licenses/MIT Copyright 2014 Selvin Ortiz
 * @copyright	Selvin Ortiz
 */
class Kismet
{
	protected $apiKey;
	protected $originUrl;
	protected $httpClient;

	const ENDPOINT = 'rest.akismet.com/1.1';

	public function __construct(array $data=array())
	{
		$this->apiKey		= $this->fetch('apiKey', $data, null);
		$this->originUrl	= $this->fetch('originUrl', $data, null);
		$this->httpClient	= $this->fetch('httpClient', $data, new Client());
	}

	public function isKeyValid(array $data=array())
	{
		$params = array(
			'key'	=> $this->fetch('apiKey', $data, $this->apiKey),
			'blog'	=> $this->fetch('originUrl', $data, $this->originUrl)
		);

		$request	= $this->httpClient->post($this->getKeyEndpoint(), null, $params);
		$response	= (string) $request->send()->getBody();

		return (bool) ($response == 'valid');
	}

	public function isSpam(array $data=array())
	{
		$params = $this->mergeWithDefaultParams(
			array(
				'comment_author'		=> $this->fetch('author', $data),
				'comment_content'		=> $this->fetch('content', $data),
				'comment_author_email'	=> $this->fetch('email', $data)
			)
		);

		if ($this->isKeyValid())
		{
			$request	= $this->httpClient->post($this->getContentEndpoint(), null, $params);
			$response	= (string) $request->send()->getBody();

			return (bool) ('true' == $response);
		}

		throw new InvalidKeyException;
	}

	public function submitSpam(array $data=array())
	{
		$params = $this->mergeWithDefaultParams(
			array(
				'comment_author'		=> $this->fetch('author', $data),
				'comment_content'		=> $this->fetch('content', $data),
				'comment_author_email'	=> $this->fetch('email', $data)
			)
		);

		if ($this->isKeyValid())
		{
			$request	= $this->httpClient->post($this->getSpamEndpoint(), null, $params);
			$response	= (string) $request->send()->getBody();

			return (bool) ('Thanks for making the web a better place.' == $response);
		}

		throw new Exception('The key provided is not valid or has expired.');
	}

	public function submitHam(array $data=array())
	{
		$params = $this->mergeWithDefaultParams(
			array(
				'comment_author'		=> $this->fetch('author', $data),
				'comment_content'		=> $this->fetch('content', $data),
				'comment_author_email'	=> $this->fetch('email', $data)
			)
		);

		if ($this->isKeyValid())
		{
			$request	= $this->httpClient->post($this->getHamEndpoint(), null, $params);
			$response	= (string) $request->send()->getBody();

			return (bool) ('Thanks for making the web a better place.' == $response);
		}

		throw new Exception('The key provided is not valid or has expired.');
	}

	protected function mergeWithDefaultParams(array $extraParams=array())
	{
		return array_merge(
			array(
				'blog'			=> \Craft\craft()->getSiteUrl(),
				'user_ip'		=> $this->getRequestingIp(),
				'user_agent'	=> $this->getUserAgent(),
				'comment_type'	=> 'Entry'
			),
			$extraParams
		);
	}

	/**
	 * Ensures that we get the right IP address even if behind CloudFlare
	 *
	 * @todo	Add support for IPV6 and Proxy servers (Overkill?)
	 * @return	string
	 */
	public function getRequestingIp()
	{
		return isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
	}

	protected function getUserAgent()
	{
		return 'Craft 1.3 | Spam Guard 0.5';
	}

	protected function fetch($key, array $arr=array(), $def=false)
	{
		return array_key_exists($key, $arr) ? $arr[$key] : $def;
	}
	
	protected function getKeyEndpoint()
	{
		return sprintf('http://%s/verify-key', self::ENDPOINT);
	}

	protected function getContentEndpoint()
	{
		return sprintf('http://%s.%s/comment-check', $this->apiKey, self::ENDPOINT);
	}

	protected function getSpamEndpoint()
	{
		return sprintf('http://%s.%s/submit-spam', $this->apiKey, self::ENDPOINT);
	}

	protected function getHamEndpoint()
	{
		return sprintf('http://%s.%s/submit-ham', $this->apiKey, self::ENDPOINT);
	}
}
