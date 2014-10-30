# Spam Guard

Lovingly crafted by [Selvin Ortiz][developer] for [Craft CMS][craftcms]

## Changelog

### 0.6.0
- Adds support for [Sprout Forms][sproutforms]
- Adds the `SpamGuardInvalidKeyException` class to the `Craft` namespace
- Improves the **Logged Submissions** template with faster load times
- Improves documentation for **Guest Entries** support
- Removes external libraries
- Removes composer dependency
- Removes the `SpamGuardVariable` class
- Removes the `spamGuardDetectSpam()` plugin hook
- Removes the `InvalidKeyException` class

### 0.5.5
- Adds `InvalidKeyException` reference to `submitSpam()` and `submitHam()`
- Adds the ability to use `{siteUrl}` as the **Origin URL** in settings
- Fixes missing `InvalidKeyException` reference in issue #7

### 0.5.4
- Fixes deprecation issue #6 in Craft 2.0
- Fixes some typos in code comments and the README
- Improves styling for logged submissions

### 0.5.3
- Adds native support for [Guest Entries][guestentries] by [P&T][pixelandtonic]
- Fixes a typo in the footer from code reuse outlined on issue #2
- Improves the way the *User IP* is retrieved
- Improves settings handling methods

### 0.5.2
- Adds automatic redirection to `Settings > Plugins > Spam Guard` after install

### 0.5.1
- Adds setting to opt out of submission logging
- Adds the audit column properties to the `SpamGuardModel`
- Fixes line endings and platform inconsistencies
- Improves the layout and clarity of the settings template

### 0.5.0
_This version introduces breaking changes, a fresh installed is required_

- Adds native support for [Contact Form][contactform] by [P&T][pixelandtonic]
- Adds an [Akismet][akismet] client built on **Guzzle**
- Adds a basic Unit Test Suite
- Adds basic submission logging
- Improves codebase by removing proprietary form functionality

### 0.4.7
- Fixes an issue caused by accessing an undefined constant @ `SpamGuardService`
- Improves the settings UI by adding icons and a setting for [Contact Form][contactform]
- Removes the bridge package that was used to autoload the **Akismet** library

### 0.4.6
- Removes class constants for plugin properties
- Adds `plugin.json` to define plugin properties
- Cleans up trailing spaces and enforces new line (EOF)

### 0.4.5
- Adds editor behavior to the `emailTemplate` textarea

### 0.4.4
- Removes `actionSpamGuardTest()`
- Adds `SpamGuard_MessageModel`
- Adds `SpamGuard_MessagingService`
- Adds `actionSendMessage()` to enable contact form submissions

### 0.4.3
- Renames `rocket` to `bridge`
- Makes the `bridge` helper library play nice with other plugins
- Adds `safeOutput()` to flag content as safe and avoid using the raw filter

### 0.4.2
_This version contains breaking changes meant to improve the foundation to build on_

- Adds `spamGuardDetectSpam()` to `SpamGuardPlugin` *LTS*
- Adds `detectSpam` to the `SpamGuardService` *LTS*
- Removes `spamGuardPostedContent()` from `SpamGuardPlugin`
- Removes `spamGuardSubmittedContent()` from `SpamGuardPlugin`
- Removes `isSpam` from `SpamGuardService`

### 0.4.1
- Adds the `Akismet` model
- Adds the `spamGuardSubmittedContent()` action
- Adds placeholder Controller/Actions to be implemented
- Fixes inaccurate fetching of setting values
- Improves missing `API Key` handling
- Removes code examples in source code
- Removes empty `hookRegisterCpRoutes()`

### 0.4.0
- Removes `composer.json` and the composer `vendor` package
- Improves the `SpamGuardService` constructor
- Removes `rocket/packages` from `.gitignore` since composer is not being used

### 0.3.0
- Adds the `spamGuardPostedContent()` action
- Adds callback support based on `spamGuardPostedContent()` results
- Improves semantics by placing complex logic in the `SpamGuardService`
- Improves `SpamGuardController` by only exposing the `actionIsSpam()`

### 0.2.0
- Adds `SpamGuardModel`
- Adds `SpamGuardController`
- Adds `Rocket::loadClass()` to load dependencies
- Removes composer dependency

### 0.1
Initial proof of concept release

[developer]:http://twitter.com/selvinortiz "@selvinortiz"
[craftcms]:http://buildwithcraft.com "Craft CMS"
[akismet]:http://akismet.com "Akismet"
[contactform]:https://github.com/pixelandtonic/ContactForm "Contact Form"
[guestentries]:https://github.com/pixelandtonic/GuestEntries "Guest Entries"
[sproutforms]:http://sprout.barrelstrengthdesign.com/craft-plugins/forms "Sprout Forms"
[pixelandtonic]:http://pixelandtonic.com "Pixel & Tonic"
