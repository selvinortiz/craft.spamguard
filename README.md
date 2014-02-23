![Spam Guard](resources/img/spamguard.png)

## Spam Guard 0.5.2
*by* [Selvin Ortiz](http://twitter.com/selvinortiz)

----
### Download Notes
You must download the [latest release](https://github.com/selvinortiz/craft.spamguard/releases) with the following name pattern `spamguard.v*.*.*.zip`

The official release is the only distribution meant for production and it is required when requesting support or reporting a bug.

_This version should be a fresh install if your previous version is below Spam Guard 0.5.0_

----
### Description
Spam Guard allows you to harness the power of Akismet to fight spam

### Features
* The most effective way to fight _contact form spam_ in **Craft**
* Lightweight Akismet API client built on **Guzzle**
* Basic submission logging to avoid losing incorrectly flagged emails
* Native support for the [Contact Form](https://github.com/pixelandtonic/ContactForm) plugin

### Minimum Requirements
- Craft 1.3 build 2415
- [Contact Form 1.3](https://github.com/pixelandtonic/ContactForm) by [P & T](http://pixelandtonic.com)

### Installation
1. Download the [latest release](https://github.com/selvinortiz/craft.spamguard/releases) with the following name pattern `spamguard.v*.*.*.zip`
2. Extract the archive and place `spamguard` inside your `craft/plugins` directory
3. Adjust file permissions as necessary
4. Install **Spam Guard** from the **Control Panel**
5. Set up your **Akismet/WordPress API Key** (get one from [akismet.com](http://akismet.com))
6. Enable **contact form support** from the plugin settings screen

----

### Usage
*Spamg Guard* can protect you from *spam* submitted via your _contact form_. To enable it, just follow the instructions below.

1. Set up your form as outlined in the [Contact Form](https://github.com/pixelandtonic/ContactForm) examples.
2. Make sure you follow the installation instructions for **Spam Guard**
3. Run some tests by using *viagra-123-test* as the value for your *name* field.

### Feedback & Support
If you have any feedback or questions please reach out to me on twitter [@selvinortiz](http://twitter.com/selvinortiz)

### Changelog

----
#### 0.5.2
- Adds automatic redirection to settings after install
- Updates documentation and license

----
#### 0.5.1
- Adds a new release distribution
- Adds submission logging setting to make logging optional and avoid db hits
- Fixes line endings and platform inconsistencies
- Improves the settings template by including logging setting
- Improves error reporting and notices
- Improves the `SpamGuardModel` by accepting audit properties

#### 0.5.0 (Production Preview)
This version must be installed after any previous version has been removed.

- Adds native support for `contactform` by P&T
- Adds a brand new `akismet` client built on **Guzzle** written by **Selvin Ortiz**
- Adds a basic `unit test suite` skeleton
- Adds a build script to aid in distribution
- Adds basic submission logging
- Removes all proprietary form functionality
- Simplifies codebase

#### 0.4.7
- Adds support for **Contact Form 1.3** by **P&T**
- Removes the bridge package that was loading akismet
- Removes the plugin.json file that was loading plugin properties
- Renames license, templates (.html), and the messaging service
- Updates codebase to make it as simple and clean as possible
- Updates the settings UI with new icons and a new setting for **Contact Form 1.3**
- Fixes fatal error caused by missing class constant in `SpamGuardService.php`
- @TODO: Remove built in form functionality and prefer **Contact Form 1.3**

----
#### 0.4.6
- Removes class constants for plugin properties
- Adds plugin.json` to define the plugin metadata
- Cleans up trailing spaces and enforces new line (EOF)

----
#### 0.4.5
- Cleans up spaces
- Adds editor behavior to the `emailTemplate` textarea

----
#### 0.4.4
- Approaching stable
- Removes `actionSpamGuardTest()`
- Adds a message model
- Adds a messaging service
- Adds `actionSendMessage()` to enable contact form submissions
- Allows you to create a `Spam Guard` enabled contact form with a custom template
- Extends the plugins settings

----
#### 0.4.3
- Renames `rocket` to `bridge`
- Makes the `bridge` helper library play nice with other plugins that may use it
- Adds `safeOutput()` to mark template output as safe without having to use a raw filter from the template
- Implements small fixes and improvements throughout

----
#### 0.4.2
- Adds `spamGuardDetectSpam()` to `SpamGuardPlugin` *LTS*
- Adds `detectSpam` to the `SpamGuardService` *LTS*
- Removes `spamGuardPostedContent()` from `SpamGuardPlugin`
- Removes `spamGuardSubmittedContent()` from `SpamGuardPlugin`
- Removes `isSpam` from `SpamGuardService`
- Adds the migrations folder explicitly with `.gitkeep`

*The breaking changes in this version ensure a better foundation on which to build on for __Spam Guard__
and it also ensures that the functions have easy to understand names that hint at their behavior and return values.*

----
#### 0.4.1
- Fixes inaccurate fetching of setting values
- Corrects a `plugins call` example in the readme
- Adds the `spamGuardSubmittedContent() action
- Removes code examples in source code
- Handles missing `API Key` more gracefully
- Adds placeholder Controller/Actions to be implemented
- Adds the Akismet model
- Removes empty `hookRegisterCpRoutes()`

----
#### 0.4
- Adds the `migrations` folder due to some errors while self updating if not present
- Removes `composer.json` and the composer `vendor` package
- Adds `arrayGet($key, $arr=array(), $def=false)` as a helper function
- Cleans up the `SpamGuardService` constructor
- Removes `rocket/packages` from the `.gitignore` since composer is not being used
- Redirects you to the `settings` page for __Spam Guard__ if no `API Key` has been set
- Updates the readme examples

----
#### 0.3
- Adds the `spamGuardPostedContent()` action
- Implements callback execution based on `spamGuardPostedContent()` results
- Delegates most of the complex logic to the `SpamGuardService`
- Cleans up the controller and only exposes a submit action `actionIsSpam()`

----
#### 0.2
- Renames the `service` class
- Extends a new Akismet Class
- Adds the spam controller
- Adds the spam model
- Removes composer autoloader
- Loads Akismet via `Rocket::loadClass()`
- Cleans up whitespace and tabs

----
#### 0.1
Initial preview release

### License
**Spam Guard** for _craft_ is open source software licensed under the [MIT license](http://opensource.org/licenses/MIT)

![Open Source Initiative](resources/img/osilogo.png)
