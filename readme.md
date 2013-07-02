## Spam Guard *for* Craft

*Spam Guard* allows you to take advantage of the powerful [Akismet](http://akismet.com) service API to fight spam
on your blog, form submissions or any submitted data that could be potential **spam**.

### Installation
- Throw the `spamguard` folder inside your `craft/plugins`
- You may have to `cd` into `craft/plugins/spamguard/rocket` and `composer install`
- Install the `spamguard` plugin via the control panel
- Visit the settings page to add your `API Key` and `Origin URL`

### Usage/Example
If you had a blog and were concerned about `comment` spam, from your templates you could...

		craft.SpamGuard.isSpam(content, author[optional], email[optional])

From your controller you could call the service like so...

		cract()->spamGuard_spam->isSpam($content, $author[optional], $email[optional])

...when a form is posted/submitted!

### @=TODO
There are a few functions available from the API but I have not had the change to implement the wrappers for them yet.
These functions help mainly with false/positive reporting `submitHam()` and sneaky content  `submitSpam()` that gets through the filter.