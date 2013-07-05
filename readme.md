## Spam Guard *for* Craft
*by* [Selvin Ortiz](http://twitter.com/selvinortiz)

### Version 0.4.1
*Requirements*
- PHP 5.3
- Craft 1.0

### Description
*Spam Guard* allows you to harness the power of [Akismet](http://akismet.com) to help you fight **spam**.

### Changelog
*__Spam Guard__ may not be stable enough for production until version __0.5__ is released but you are more than welcome to begin `testing/integrating`.*

----

#### 0.4.1
- Fixes inaccurate fetching of setting values
- Corrects a `plugins call` example in the readme
- Adds the `spamGuardSubmittedContent() action
- Removes code examples in source code
- Handles missing `API Key` more gracefully
- Adds placeholder Controller/Actions to be implemented
- Adds the Akismet model

#### 0.4
- Adds the `migrations` folder due to some errors while self updating if not present
- Removes `composer.json` and the composer `vendor` package
- Adds `arrayGet($key, $arr=array(), $def=false)` as a helper function
- Cleans up the `SpamGuardService` constructor
- Removes `rocket/packages` from the `.gitignore` since composer is not being used
- Redirects you to the `settings` page for S**pam Guard** if no `API Key` has been set
- Updates the readme examples

#### 0.3
- Adds the `spamGuardPostedContent()` action
- Implements callback execution based on `spamGuardPostedContent()` results
- Delegates most of the complex logic to the `SpamGuardService`
- Cleans up the controller and only exposes a submit action `actionIsSpam()`

#### 0.2
- Renames the `service` class
- Extends a new Akismet Class
- Adds the spam controller
- Adds the spam model
- Removes composer autoloader
- Loads Akismet via `Rocket::loadClass()`
- Cleans up whitespace and tabs


#### 0.1
Initial preview release

----

### Installation
- Clone `git@github.com:selvinortiz/spamguard.git` or [download](https://github.com/selvinortiz/spamguard/archive/master.zip) the *Spam Guard* repo 
- Throw the contents inside your `craft/plugins/spamguard`
- Make sure *Craft* has `read/write` permissions on `craft/plugins/spamguard`
- Install the `spamguard` plugin via the control panel
- Add your `API Key` and `Origin URL`

*If you attempt to use **Spam Guard** without setting an `API Key` it will redirect you to the `settings` page so that you may add it.*

### Usage/Example

**Spam Guard** exposes the `spamGuardPostedContent()` action which we can use to check submitted content for spam.

First, we need a form (contact, registration, etc.) to grab the potential **spammy** content.

	<form method="post" action="" class="forms">
		<input type="hidden" name="action" id="action" value="youController/yourAction"/>
		<fieldset>
			<ul>
				<li>
					<label for="email">Email</label>
					<input type="email" name="email" id="email" size="40" value="" />
				</li>
				<li>
					<label for="author">Name</label>
					<input type="text" name="author" id="author" value="" size="40" />
				</li>
				<li>
					<fieldset>
						<label for="content">Textarea</label>
						<textarea id="content" name="content" class="width-100"></textarea>
					</fieldset>
				</li>
				<li>
					<input type="submit" name="submit" value="Submit" />
				</li>
			</ul>
		</fieldset>
	</form>

The things to note about this form:

- It submits to your own `Controller/Action` or whatever you want to post to, not a `Controller/Action` provided by *Spam Guard*.
- It defines the `content`, `author`, and `email` fields which **Spam Gaurd** will check in the POST array and run the *spam check* against it.
- It only defines the fields we need for *Spam Guard* but in theory this could be a bigger form with many other fields.

Next, we need to do some *pre-processing* before handing things off to *Spam Guard* and tipically that looks something like this...

	// Pseudo Logic
	1. Validate the POSTed data
	2. Prepare the content, author, and email parameters for Spam Guard
	3. Define the functions to pass along to Spam Guard which should be called based on the spam check

		$onSuccess = function()
		{
			// No spam found, do the happy dance!
		}

		$onFailure = function()
		{
			// You got spammed, hit your head against the fridge!
		}

	4. If the form contains the content, author, and email fields, check for spam

		// The data array must contain the content, author, and email keys
		// If your form has different names for this fields, you can cast/import them
		$params = array(
			'data'		=> craft()->request->getPost(),
			'onSuccess'	=> $onSuccess,	// optional
			'onFailure'	=> $onFailure	// optional
		);

		// spamGuardPostedContent() runs isSpam() behind the scenes
		// The (boolean) response will match that of isSpam()

		$response = craft()->plugins->call('spamGuardPostedContent', $params);

		if ( $response['SpamGuard'] )
		{
			// spam found by spamGuardPostedContent()
			// onFailure() was called and true was returned
		}
		else
		{
			// spam not found by spamGuardPostedContent()
			// onSuccess() was called and false was returned
		}
	
	5. You can now save comment, send email, or whatever your form does if the data is clean

### TODO
- Work on production ready release candidate (0.5)
- Screencast showing different use case implementations might be helpful
- Sample workflows for contact form submissions

*For now though, I would appriciate any feedback you may have so that __Spam Guard__ can be production ready quicker.*

### Feedback & Support
If you have any feedback or questions please reach out to me on twitter [@selvinortiz](http://twitter.com/selvinortiz)

### MIT License
*Spam Guard for Craft* is released under the [MIT license](http://opensource.org/licenses/MIT) which pretty much means you can do with it as you please and I won't get mad because I'm that nice; )