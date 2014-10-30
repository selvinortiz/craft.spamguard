![Spam Guard](resources/img/spamguard.png)

# Spam Guard 0.6.0

Lovingly crafted by [Selvin Ortiz][developer] for [Craft CMS][craftcms]

**Spam Guard** harnesses the power of [Akismet][akismet] to fight **Spam**

## Features
1. Proven _Spam Fighting_ capabilities with the help of [Akismet][akismet]
2. Native support for the [Contact Form Plugin][contactform]
3. Native support for the [Guest Entries Plugin][guestentries] 
4. Upcoming support for the **Sprout Forms Plugin**
5. Clean and efficient codebase with good documentation
6. Friendly developer support via **Github**, [Twitter][developer], and via email at <selvin@selv.in>

## Installation
1. Download the [Official Release][release]
2. Extract the archive into your `craft/plugins` directory
4. Install **Spam Guard** from the control panel **@** `Settings > Plugins`
5. Add your `API Key` and `Origin URL`
6. Enable support for [Contact Form][contactform] and/or [Guest Entries][guestentries]

## Upgrading
If you are upgrading to version **0.6.0** from an earlier version, please see the documentation for **Guest Entries** because the way hidden fields in your form are defined has changed.

1. Spam Guard now expects the following fields `spamguard[emailField|authorField|contentField]`
2. The values for those fields should now be wrapped in curly braces match entry field handles

## Dependencies
- PHP 5.3.2 _or above_
- Craft 1.3 Build 2415 _or above_
- [Contact Form 1.3+][contactform] _for contact form support_
- [Guest Entries 1.1+][guestentries]  _for guest entries support_

_Please note that [Guest Entries][guestentries] requires Craft **2.0** or above_

---

### Contact Form Usage
To fight spam coming from your contact form, you must do the following...

1. Follow the [Contact Form][contactform] setup guide if you haven't already
2. Make sure [Contact Form][contactform] support **@** `Settings > Plugins > Spam Guard`

_That is it, all future submissions will be monitored by spam guard_ **;)**

### Guest Entries Usage
To fight spam coming from your guest entry form, you must do the following...

1. Follow the [Guest Entries][guestentries] setup guide if you haven't already
2. Make sure [Guest Entries][guestentries] support is enabled **@** `Settings > Plugins > Spam Guard`
3. Add the `hidden input` fields to your form so **Spam Guard** knows what to validate

#### Hidden Input Fields
Add the following fields to your guest entry form.

```html
<input type="hidden" name="spamguard[emailField]" value="{guestEntryEmailFieldHandle}">
<input type="hidden" name="spamguard[authorField]" value="{guestEntryFullNameFieldHandle}">
<input type="hidden" name="spamguard[contentField]" value="{guestEntryBodyFieldHandle}">
```

These fields need to be defined so that **Spam Guard** knows what attributes to look for in the **guest entry** in order to prepare the data to pass along to [Akismet][akismet] for validation.

When the form is submitted and the **Guest Entry** is validated, the entry will be handed to **Spam Guard** which will then grab the `spamguard[emailField|authorField|contentField]` values containing **twig** placeholders which will then be **replaced** by attribute values found in the [EntryModel](http://buildwithcraft.com/docs/templating/entrymodel)

_Note that the emailField and authorField are not required!_

### Sprout Forms Usage
Sprout Forms support is implemented in the same way support is implemented for Guest Entries, the directions for Guest Entries should be followed. 

---

## Changelog
All noteworthy changes are listed on the official [changelog][changelog]

## Feedback
If you have any feedback or questions, please reach out to me on twitter [@selvinortiz][developer]


## Credits
* Huge thanks to the **WordPress/Akismet** team for their great efforts and service they provide.
* _Special thanks to [@themccallister](https://github.com/themccallister) for testing Spam Guard and for providing valuable feedback._

## License
Spam Guard is open source software licensed under the [MIT license][license]

![Open Source Initiative][osilogo]

[developer]:http://twitter.com/selvinortiz "@selvinortiz"
[release]:https://github.com/selvinortiz/craft.spamguard/releases "Official Release"
[license]:https://raw.github.com/selvinortiz/craft.spamguard/master/LICENSE "MIT License"
[changelog]:https://github.com/selvinortiz/craft.spamguard/blob/master/CHANGELOG.md "Changelog"
[craftcms]:http://buildwithcraft.com "Craft CMS"
[akismet]:http://akismet.com "Akismet"
[contactform]:https://github.com/pixelandtonic/ContactForm "Contact Form"
[guestentries]:https://github.com/pixelandtonic/GuestEntries "Guest Entries"
[pixelandtonic]:http://pixelandtonic.com "Pixel & Tonic"
[osilogo]:https://github.com/selvinortiz/craft.spamguard/raw/master/resources/img/osilogo.png "Open Source Initiative"
