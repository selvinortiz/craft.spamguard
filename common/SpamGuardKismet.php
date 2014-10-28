<?php
namespace Craft;

use Guzzle\Http\Client;

/**
 * Class		SpamGuardKismet
 * @author		Selvin Ortiz <selvin@selvin.co>
 * @package		Spam Guard
 * @version		1.0
 * @license		http://opensource.org/licenses/MIT Copyright 2014 Selvin Ortiz
 * @copyright	Selvin Ortiz
 */

class SpamGuardKismet
{
	/**
	 * @var string
	 */
	protected $apiKey;

	/**
	 * @var string
	 */
	protected $originUrl;

	/**
	 * @var Client
	 */
	protected $httpClient;

	/**
	 * The URL endpoint to the akismet API
	 */
	const ENDPOINT = 'rest.akismet.com/1.1';

	/**
	 * @param BaseModel		$pluginSettings
	 * @param null|Client	$httpClient
	 */
	public function __construct(BaseModel $pluginSettings, $httpClient=null)
	{
		$this->apiKey		= $pluginSettings->getAttribute('akismetApiKey');
		$this->originUrl	= $pluginSettings->getAttribute('akismetOriginUrl');
		$this->httpClient	= $httpClient ? $httpClient : new Client;
	}

	/**
	 * Checks whether the API key is valid
	 *
	 * @return bool
	 */
	public function isKeyValid()
	{
		$params = array(
			'key'	=> $this->apiKey,
			'blog'	=> $this->originUrl,
		);

		$request	= $this->httpClient->post($this->getKeyEndpoint(), null, $params);
		$response	= (string) $request->send()->getBody();

		return (bool) ('valid' === $response);
	}

	/**
	 * Validates potential spam against the Akismet API
	 *
	 * @param array $data
	 *
	 * @throws SpamGuardInvalidKeyException
	 * @return bool
	 */
	public function isSpam(array $data=array())
	{
		$params = $this->mergeWithDefaultParams(
			array(
				'comment_author'		=> isset($data['author']) ? $data['author'] : null,
				'comment_content'		=> isset($data['content']) ? $data['content'] : null,
				'comment_author_email'	=> isset($data['email']) ? $data['email'] : null,
			)
		);

		if ($this->isKeyValid())
		{
			$request	= $this->httpClient->post($this->getContentEndpoint(), null, $params);
			$response	= (string) $request->send()->getBody();

			return (bool) ('true' == $response);
		}

		throw new SpamGuardInvalidKeyException;
	}

	/**
	 * @param array $data
	 *
	 * @throws SpamGuardInvalidKeyException
	 * @return bool
	 */
	public function submitSpam(array $data=array())
	{
		$params = $this->mergeWithDefaultParams(
			array(
				'comment_author'		=> isset($data['author']) ? $data['author'] : null,
				'comment_content'		=> isset($data['content']) ? $data['content'] : null,
				'comment_author_email'	=> isset($data['email']) ? $data['email'] : null,
			)
		);

		if ($this->isKeyValid())
		{
			$request	= $this->httpClient->post($this->getSpamEndpoint(), null, $params);
			$response	= (string) $request->send()->getBody();

			return (bool) ('Thanks for making the web a better place.' == $response);
		}

		throw new SpamGuardInvalidKeyException;
	}

	/**
	 * @param array $data
	 *
	 * @throws SpamGuardInvalidKeyException
	 * @return bool
	 */
	public function submitHam(array $data=array())
	{
		$params = $this->mergeWithDefaultParams(
			array(
				'comment_author'		=> isset($data['author']) ? $data['author'] : null,
				'comment_content'		=> isset($data['content']) ? $data['content'] : null,
				'comment_author_email'	=> isset($data['email']) ? $data['email'] : null,
			)
		);

		if ($this->isKeyValid())
		{
			$request	= $this->httpClient->post($this->getHamEndpoint(), null, $params);
			$response	= (string) $request->send()->getBody();

			return (bool) ('Thanks for making the web a better place.' == $response);
		}

		throw new SpamGuardInvalidKeyException;
	}

	/**
	 * @param array $extraParams
	 *
	 * @return array
	 */
	protected function mergeWithDefaultParams(array $extraParams=array())
	{
		return array_merge(
			array(
				'blog'			=> craft()->getSiteUrl(),
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
		return 'Craft 2.2 | Spam Guard 0.6.0';
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
