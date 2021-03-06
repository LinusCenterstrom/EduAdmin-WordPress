=== EduAdmin Booking ===
Contributors: mnchga
Tags: booking, participants, courses, events, eduadmin, lega online
Requires at least: 4.7
Tested up to: 4.9
Stable tag: 2.0.9
Requires PHP: 5.2
License: GPL3
License-URI: https://www.gnu.org/licenses/gpl-3.0.en.html
EduAdmin plugin to allow visitors to book courses at your website. Requires EduAdmin-account.

== Description ==

Plugin that you connect to [EduAdmin](https://www.eduadmin.se) to enable booking on your website.

[<img src="https://img.shields.io/wordpress/plugin/v/eduadmin-booking.svg" alt="Plugin version" />](https://wordpress.org/plugins/eduadmin-booking/)
[<img src="https://img.shields.io/wordpress/plugin/dt/eduadmin-booking.svg" alt="Downloads" />](https://wordpress.org/plugins/eduadmin-booking/)
[<img src="https://img.shields.io/wordpress/v/eduadmin-booking.svg" alt="Tested up to" />](https://wordpress.org/plugins/eduadmin-booking/)

[<img src="https://badges.gitter.im/MultinetInteractive/EduAdmin-WordPress.png" alt="Gitter" />](https://gitter.im/MultinetInteractive/EduAdmin-WordPress)
[<img src="https://travis-ci.org/MultinetInteractive/EduAdmin-WordPress.svg?branch=master" alt="Build status" />](https://travis-ci.org/MultinetInteractive/EduAdmin-WordPress)
[<img src="https://scrutinizer-ci.com/g/MultinetInteractive/EduAdmin-WordPress/badges/quality-score.png?b=master" alt="Code quality" />](https://scrutinizer-ci.com/g/MultinetInteractive/EduAdmin-WordPress/?branch=master)

[<img src="https://img.shields.io/github/commits-since/MultinetInteractive/EduAdmin-WordPress/latest.svg" alt="Commits since latest plugin version" />](https://wordpress.org/plugins/eduadmin-booking/)



== Installation ==

- Upload the zip-file (or install from WordPress) and activate the plugin
- Provide the API key from EduAdmin.
- Create pages for the different views and give them their shortcodes

== Upgrade Notice ==

= 2.0 =
We have replaced everything with a new API-client, so some things may be broken. If you experience any bugs (not new feature-requests), please contact the MultiNet Support.
If you notice that your API key doesn't work any more, you have to contact us.

== Changelog ==

### 2.0.9 ###
- fix: Added attribute to list of valid attributes, so that `eventprice` works in `[eduadmin-detailinfo]`

### 2.0.8 ###
- fix: Fixed sort order on event dates ([Issue #178](https://github.com/MultinetInteractive/EduAdmin-WordPress/issues/178))
- fix: Adding extra parameter to links that could contain sensitive information ([Issue #170](https://github.com/MultinetInteractive/EduAdmin-WordPress/issues/170))
- chg: My Bookings now only include non-cancelled events
- chg: Changing the text for the "Use match"-dropdown, to something the you can understand.
- chg: Suffix all transients with version of plugin ([Issue #164](https://github.com/MultinetInteractive/EduAdmin-WordPress/issues/164))

### 2.0.7 ###
- fix: Fixed the date format in the event schedule when the event is withing different months
- fix: The events in the list-view are now sorted by `startDate` when the `Sort order` is set to `Sort index`
- fix: Events in the event-list can now be sorted with event properties as well as course template properties

### 2.0.6 ###
- fix: `get_option` does only return booleans when they are empty (fixed on booking page)
- fix: When checking price on a single-participant-booking, we should fill the participant name if it's empty
- fix: Fix from clestial that fixes permalink reload when you change settings