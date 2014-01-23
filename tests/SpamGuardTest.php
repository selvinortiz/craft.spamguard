<?php
namespace Craft;

use \Mockery as m;

class SpamGuardTest extends BaseTest
{
	protected $config;
	protected $settings;
	protected $spamGuardService;

	public function setUp()
	{
		$this->config = m::mock('Craft\ConfigService');
		$this->config->shouldReceive('getIsInitialized')->andReturn(true);
		$this->config->shouldReceive('usePathInfo')->andReturn(true)->byDefault();

		$this->config->shouldReceive('get')->with('usePathInfo')->andReturn(true)->byDefault();
		$this->config->shouldReceive('get')->with('cpTrigger')->andReturn('admin')->byDefault();
		$this->config->shouldReceive('get')->with('pageTrigger')->andReturn('p')->byDefault();
		$this->config->shouldReceive('get')->with('actionTrigger')->andReturn('action')->byDefault();
		$this->config->shouldReceive('get')->with('translationDebugOutput')->andReturn(false)->byDefault();

		$this->config->shouldReceive('getLocalized')->with('loginPath')->andReturn('login')->byDefault();
		$this->config->shouldReceive('getLocalized')->with('logoutPath')->andReturn('logout')->byDefault();
		$this->config->shouldReceive('getLocalized')->with('setPasswordPath')->andReturn('setpassword')->byDefault();

		$this->config->shouldReceive('getCpLoginPath')->andReturn('login')->byDefault();
		$this->config->shouldReceive('getCpLogoutPath')->andReturn('logout')->byDefault();
		$this->config->shouldReceive('getCpSetPasswordPath')->andReturn('setpassword')->byDefault();
		$this->config->shouldReceive('getResourceTrigger')->andReturn('resource')->byDefault();

		$this->setComponent(craft(), 'config', $this->config);
		$this->setEnvironment();
		$this->loadServices();
	}

	public function testServiceFetch()
	{
		$data = array('white'=>'fff', 'black'=>'000');

		$this->assertEquals('fff', $this->spamGuardService->fetch('white', $data));
		$this->assertEquals(null, $this->spamGuardService->fetch('blue', $data, null));
		$this->assertEquals(false, $this->spamGuardService->fetch('blue', $data));
	}

	protected function setEnvironment()
	{
		$this->settings = m::mock('Craft\Model');
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
	}

	protected function loadServices()
	{
		require_once dirname(__FILE__).'/../services/SpamGuardService.php';

		$this->spamGuardService = new SpamGuardService();
	}

	protected function inspect($data)
	{
		fwrite(STDERR, print_r($data));
	}
}
