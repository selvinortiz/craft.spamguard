## Spam Guard *for* Craft
*by* [Selvin Ortiz](http://twitter.com/selvinortiz)

### Version 0.4.2
*Requirements*
- PHP 5.3
- Craft 1.0

### Description
*Spam Guard* allows you to harness the power of [Akismet](http://akismet.com) to help you fight **spam**

### Changelog
*__Spam Guard__ may not be stable enough for production until version __0.5__ is released but you are more than welcome to begin `testing/integrating`.*

----

#### 0.4.3
- Renames `rocket` to `bridge`
- Makes the `bridge` helper library play nice with other plugins that may use it
- Adds `safeOutput()` to mark template output as safe without having to use a raw filter from the template
- Implements small fixes and improvements throughout

#### 0.4.2
- Adds `spamGuardDetectSpam()` to `SpamGuardPlugin` *LTS*
- Adds `detectSpam` to the `SpamGuardService` *LTS*
- Removes `spamGuardPostedContent()` from `SpamGuardPlugin`
- Removes `spamGuardSubmittedContent()` from `SpamGuardPlugin`
- Removes `isSpam` from `SpamGuardService`
- Adds the migrations folder explicitly with `.gitkeep`

*The breaking changes in this version ensure a better foundation on which to build on for __Spam Guard__
and it also ensures that the functions have easy to understand names that hint at their behavior and return values.*

#### 0.4.1
- Fixes inaccurate fetching of setting values
- Corrects a `plugins call` example in the readme
- Adds the `spamGuardSubmittedContent() action
- Removes code examples in source code
- Handles missing `API Key` more gracefully
- Adds placeholder Controller/Actions to be implemented
- Adds the Akismet model
- Removes empty `hookRegisterCpRoutes()`

#### 0.4
- Adds the `migrations` folder due to some errors while self updating if not present
- Removes `composer.json` and the composer `vendor` package
- Adds `arrayGet($key, $arr=array(), $def=false)` as a helper function
- Cleans up the `SpamGuardService` constructor
- Removes `rocket/packages` from the `.gitignore` since composer is not being used
- Redirects you to the `settings` page for __Spam Guard__ if no `API Key` has been set
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

*If you attempt to use __Spam Guard__ without setting an `API Key` it will redirect you to the `settings` page so that you may add it.*

----

### Spam Guard API

#### `spamGuardDetectSpam(content, author, email, [onSuccess], [onFailure])`
`@spamguard/SpamGuardPlugin.php`

----

##### `$content` _required_

The content to check for spam... this could be a post comment, message from a contact form or similar content.

##### `$author` _required_
The name of the author of the content you are checking for spam

##### `$email` _required_
The email address of the author of the content you are checking for spam

##### `$onSuccess`
The `callback` function that gets called when spam is **not** detected...
this function will have access to the `SpamGuardModel` instance from its scope.


##### `$onFailure`
The `callback` function that gets called when spam **is** detected...
this function will have access to the `SpamGuardModel` instance from its scope.

_Callbak functions may end execution or perform redirects but if they don't..
they will be executed and the value of the spam detection method will be returned._

#### Example
This function can be called from your own Controller but pay attention to its signature and the way _Craft_ calls methods on plugins.

	$args = array(
		'content'	=> 'I get so angry when the liquor cabinet is empty.',
		'author'	=> 'Angry Brad',
		'email'		=> 'angry@brad.com',
		'onSuccess'	=> function() {},
		'onFailure'	=> function() {}
	);

	$response = craft()->plugins->call('spamGuardDetectSpam', $args)

	// The $response will be an array of plugin reponses matching the method name you called
	// In our case, it might return something like this...

	array(
		'SpamGuard'	=> false
	);

	// It returns false probably because Brad might be an angry drunken fool but his content is solid;)


----

### Workflow Examples
_Non yet but I plan on adding some as soon as I get a chance... in the meantime, if you need help with implementation just drop me a line!_

### TODO
- Work on production ready release candidate (0.5)
- Screencast showing different use case implementations might be helpful
- Sample workflows for contact form submissions

*For now though, I would appriciate any feedback you may have so that __Spam Guard__ can be production ready quicker.*

### Feedback & Support
If you have any feedback or questions please reach out to me on twitter [@selvinortiz](http://twitter.com/selvinortiz)

### MIT License
*Spam Guard for Craft* is released under the [MIT license](http://opensource.org/licenses/MIT) which pretty much means you can do with it as you please and I won't get mad because I'm that nice; )