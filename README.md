![Spam Guard](resources/img/spamguard.png)

# Spam Guard 0.5.3

Lovingly crafted by [Selvin Ortiz][developer] for [Craft CMS][craftcms]

**Spam Guard** harnesses the power of [Akismet][akismet] to fight spam on your behalf.  
Potantial spam submitted via [Guest Entries][guestentries] and [Contact Form][contactform] can be monitored automatically or you may use the`spamguardDetectSpam` method provided by calling it from your own plugin.

## Installation
1. Download the [Official Release][release]
2. Extract the archive into your `craft/plugins` directory
4. Install **Spam Guard** from the control panel **@** `Settings > Plugins`
5. Add your `API Key` and `Origin URL`
6. Enable support for [Contact Form][contactform] and [Guest Entries][guestentries] (optional)

## Dependencies
- PHP 5.3.2 _or above_
- Craft 1.3 Build 2415 _or above_
- [Contact Form 1.3][contactform] _for contact form support_
- [Guest Entries 1.1][guestentries]  _for guest entries support_

_Please note that [Guest Entries][guestentries] requires Craft 1.4 or above_

## Usage
Spam Guard offers a few ways to help you fight spam, all of which are outlined below...

### Contact Form Usage
To fight spam coming from your contact form, you must do the following...

1. Follow the [Contact Form][contactform] setup guide if you haven't already
2. Enable contact form support from the control panel **@** `Settings > Plugins > Spam Guard`

_That is it, all future submissions will be monitored by spam guard_ **;)**

### Guest Entries Usage
To fight spam coming from your guest entry form, you must do the following...

1. Follow the [Guest Entries][guestentries] setup guide if you haven't already
2. Enable guest entries support from the control panel **@** `Settings > Plugins > Spam Guard`
3. Add a few `hidden input` fields to your form so spam guard knows what to do

```html
<input type="hidden" name="spamguard[emailField]" value="">
<input type="hidden" name="spamguard[authorField]" value="">
<input type="hidden" name="spamguard[validationFields]" value="body,article">
```

These input fields allow spam guard to check for entry fields matching values provided and then send that data along for validation.

_Note that the emailField and authorField are not required_

### Hook Usage 
`spamguardDetectSpam` is a hook meant to be called by your own plugin if it needs to validate potential spammy content and using it is as simple as the example that follows.

```php

// Data to validate
$data = array(
    'email'     => 'john@smith.com',
    'author'    => 'John Smith',
    'content'   => 'The Smith Company rocks, buy from us!'
);

// Call the hook
$results = craft()->plugins->call('spamguardDetectSpam', array('data' => $data));

// Check that the hook on spamguard was called and that spam was detected
if (array_key_exists('spamguard', $results) && $results['spamguard'] == true)
{
    // You can do something mean...
}

```

## Changelog
All noteworthy changes are listed on the official [changelog][changelog]

## Feedback
If you have any feedback or questions, please reach out to me on twitter [@selvinortiz][developer]


## Credits
* Huge thank you to the **WordPress / Akismet** team for their efforts and the awesome service they provide.
* _Special thanks to [@themccallister](https://github.com/themccallister) for testing Spam Guard and for providing valuable feedback._

## License
Spam Guard is open source software licensed under the [MIT license][license]

![Open Source Initiative][osilogo]

[developer]:http://twitter.com/selvinortiz "@selvinortiz"
[release]:https://github.com/selvinortiz/craft.spamguard/releases/download/v0.5.3/spamguard.v0.5.3.zip "Official Release"
[license]:https://raw.github.com/selvinortiz/craft.spamguard/master/LICENSE "MIT License"
[changelog]:https://github.com/selvinortiz/craft.spamguard/blob/master/CHANGELOG.md "Changelog"
[craftcms]:http://buildwithcraft.com "Craft CMS"
[akismet]:http://akismet.com "Akismet"
[contactform]:https://github.com/pixelandtonic/ContactForm "Contact Form"
[guestentries]:https://github.com/pixelandtonic/GuestEntries "Guest Entries"
[pixelandtonic]:http://pixelandtonic.com "Pixel & Tonic"
[osilogo]:https://github.com/selvinortiz/craft.spamguard/raw/master/resources/img/osilogo.png "Open Source Initiative"
