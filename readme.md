## Spam Guard *for* Craft

### About Spam Guard 0.3
*Spam Guard* allows you to take advantage of the powerful [Akismet](http://akismet.com) service API to fight spam on your website.
You can check/flag entry comments, form submissions and any content that may be potential **Spam**.

### About Rocket
You may have noticed a `rocket` folder within the plugin folder structure...
I use this folder to place `bootstrap` classes, `helper functions` and vendor `packages` when necessary.

### Changelog 0.3
- Adds the `spamGuardPostedContent()` action
- Implements callback execution based on `spamGuardPostedContent()` results
- Delegates most of the heavy logic to the `SpamGuardService`
- Cleans up the controller to only process a submit action `actionIsSpam()`


### Changelog 0.2
- Renames the `service` class
- Extends a new Akismet Class
- Adds the spam controller
- Adds the spam model
- Removes composer autoloader
- Loads Akismet via `Rocket::loadClass()`
- Cleans up whitespace and tabs


### Changelog 0.1
Initial preview release


### Installation
- Clone or download the repo
- Throw the `spamguard` folder inside your `craft/plugins`
- You may have to `cd` into `craft/plugins/spamguard/rocket` and `composer install/update`
- Install the `spamguard` plugin via the control panel
- Visit the settings page to add your `API Key` and `Origin URL`

### Usage/Example
**I.** If you want to check for spam from your templates (not likely) you may do something like...

	craft.SpamGuard.isSpam(
		{
			content: "Comment or data to check",
			author: "John Smith",
			email: "john@smith.com"
		}
	);

**II.** You may also want to submit content behind the scenes to the `/spamGuard/isSpam` action with `Ajax` in some cases...
	
	cract()->spamGuard->isSpam($data);

**III.** Finally and most often you would want to call the `spamGuardPostedContent()` action and pass along data and a couple of optional callbacks...

	$args = array(
		'data' => array(
			'content'	=> 'This is potential spam, watch out!',
			'author'	=> 'Joe Blogs',
			'email'		=> 'tinyjoe@blogs.com'
		),
		'onSuccess'		=> function() { // Something awesome... },
		'onFailure'		=> function() { // Something not so cool... }
	);

	craft()->plugins->call('spamGuardPostedContent', $args);


### @Todo
There are a few other functions available from the API but I have not had the chance to implement the wrappers for them yet.
These functions help mainly with false/positive reporting `submitHam()` and sneaky content  `submitSpam()` that gets through the filter.

There is also some cleaning up to do and it would probably help to better document the source code so, I'll work on that soon.

The other thing would be to implement a workflow where entry comments can be automatically checked and flagged as spam similar to WordPress.

### Feedback & Support
If you have any feedback or questions please reach out to me on twitter [@selvinortiz](http://twitter.com/selvinortiz)

#### MIT License
This and all other of my plugins are and will be released under the **MIT** license so that you can use the code however you want
without worrying about me screwing you over by trying to stop you from using or attempting to charge you for it.