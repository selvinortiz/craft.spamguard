<?php
namespace Craft;

class SpamGuardController extends BaseController
{
	protected $allowAnonymous = true;
	
	//--------------------------------------------------------------------------------
	// @TODO: Implement the following functions
	//--------------------------------------------------------------------------------
	
	public function actionSpamGuardTest()
	{
		// Force a POST request
		$this->requirePostRequest();

		// Get the plugin and the settings
		$spamGuard	= craft()->plugins->getPlugin(SpamGuardPlugin::PLUGIN_HANDLE);
		$settings	= $spamGuard->getSettings();

		// Define accessory function
		$feedback 	= function($type='error')
		{
			$success = array(
				'class'		=> 'success',
				'message'	=> 'Looks like this test passes with flying colors, no spam here;)'
			);
			$error = array(
				'class'		=> 'error',
				'message'	=> 'Looks like someone is trying to spam the crap out of our website, not nice;('
			);
			$invalid = array(
				'class'		=> 'error',
				'message'	=> 'If you really wanna test, why don\'t you give me some data huh?'
			);

			return @$$type; // < Pretty weird I know;)
		};

		// Compose the arguments for the Spam Guard method call
		$args = array(
			'content'	=> craft()->request->getPost('content'),
			'author'	=> craft()->request->getPost('author'),
			'email'		=> craft()->request->getPost('email'),
			'onSuccess'	=> function($spamModel) use ($feedback)
			{
				if ($spamModel->validate())
				{
					$feedback = $feedback('success');
					craft()->urlManager->setRouteVariables(array('feedback'=>$feedback, 'spam'=>$spamModel));
				}
				else
				{
					$feedback = $feedback('invalid');
					craft()->urlManager->setRouteVariables(array('feedback'=>$feedback, 'spam'=>$spamModel));
				}
			},
			'onFailure'	=> function($spamModel) use ($feedback)
			{
				$feedback = $feedback('error');
				craft()->urlManager->setRouteVariables(array('feedback'=>$feedback, 'spam'=>$spamModel));
			}
		);

		craft()->plugins->call('spamGuardDetectSpam', $args );
	}

	//--------------------------------------------------------------------------------
	
	public function actionSubmitHam()
	{
		// ...
	}

	//--------------------------------------------------------------------------------
	
	public function actionSubmitSpam()
	{
		// ...
	}
}
