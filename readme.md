## Spam Guard *for* Craft

### About Spam Guard
*Spam Guard* allows you to take advantage of the powerful [Akismet](http://akismet.com) service API to fight spam
on your website.

You can check/flag entry comments, form submissions and any content that may be potential **Spam**.

### About Rocket
You may have noticed a `rocket` folder within the plugin folder structure...
I use this folder to place `bootstrap` classes, `helper functions` and vendor `packages` when necessary.

### Changlog 0.2


### Changelog 0.1
Initial preview release

### Installation
- Clone or download the repo
- Throw the `spamguard` folder inside your `craft/plugins`
- You may have to `cd` into `craft/plugins/spamguard/rocket` and `composer install/update`
- Install the `spamguard` plugin via the control panel
- Visit the settings page to add your `API Key` and `Origin URL`

### Usage/Example
If you had a blog and were concerned about `comment` spam, from your templates you could...

	craft.SpamGuard.isSpam({content: "Comment or data to check", author: "John Smith", email: "john@smith.com"})

From your controller you could call the service when a form is submitted like so...

	cract()->spamGuard_spam->isSpam(SpamGuard_SpamModel $model)

You can submit a form to the controller action `/spamGuard/spam/isSpam` to run the check.

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