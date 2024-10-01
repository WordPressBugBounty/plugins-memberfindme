=== Plugin Name ===
Contributors: sourcefound
Donate link: https://membershipworks.com
Tags: membership, member access, tickets, calendar, directory
Requires at least: 3.0.2
Tested up to: 6.6.2
Stable tag: 6.12
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

All-in-one membership, directory, events and donations for organizations. Secure member profiles, renewals, upgrades and limit member only access to content, events or items. 

== Description ==

[MembershipWorks](https://membershipworks.com/) is an advanced all-in-one membership, directory, events, and donation platform for chambers, associations, professional, networking and other membership groups. This plugin integrates your MembershipWorks account to your Wordpress site. MembershipWorks is free for small groups and also free to try with our 50 member/account plan.

= Easy WordPress Setup =

* Use shortcodes to place the membership forms, events calendar, member directory and more on pages.
* Fully responsive and works with most themes

= Third Party Integrations =

* Payment Gateways - Stripe, Paypal Website Payments Pro, Paypal Payments Pro/Payflow, Authorize.net, Braintree
* Accounting Software - Xero, QuickBooks Desktop, QuickBooks Online
* Emails/Newsletters - MailChimp
* Forums - Website Toolbox

= Membership =

* Unlimited membership levels, add-ons and billing configurations
* Configurable pro-rating, fixed anniversary dates, trial periods, application fees and discount codes
* Members can signup, renew, or upgrade/downgrade their membership at any time
* Automated emails for payment receipts, renewal and past-due notices
* Segment members by labels or folders
* View/Export metrics and financials
* Daily membership report email
* Send bulk emails to members
* Custom fields and customizable membership forms

= Events =

* Display events in calendar or list
* Upcoming events widget
* Event categories
* Create unlimited paid and free event tickets
* Limit event ticket quantity, event capacity, event tickets per registration, and more
* Restrict event tickets by membership
* Customizable event registration and ticketing questions
* Automatic event registration confirmation email with iCalendar attachment
* Edit or cancel registrations and issue full or partial refunds 

= Member Only Access =

* Restrict content on any page or post to members or to specific membership levels with a shortcode
* Member only event tickets or cart items
* Automatically retire access when membership is past due
* Allow members to add or edit events

= Directory and Deals =

* Search by keyword, location, fields or by labels/folders
* Interactive map
* Multiple locations for a business or organization
* Customizable business cards
* Customizable member profiles with logos, pictures, map, social media links and more
* Enable/disable features by membership
* Create a member deals or offers page
* Comply with CAN-SPAM act and protect member email addresses from spam with our messaging system
* Member slideshow widget

= General Forms, Shopping Cart and Donations =

* Create forms for donations, committee or volunteer signup, contact forms, and more
* Sell items or collect donations
* Setup item quantities and checkout limits
* Create member only forms or shopping carts
* Automated confirmation and notification emails
* Checkout actions to add or remove labels/folders, allows for advanced workflow

= Job Board, Classifieds and Announcements =

* Create boards for jobs, classifieds, announcements and other listings
* Monetize listings by charging listing fee or make it a membership privilege
* Restrict listings to members only or make it public 
* Option to require admin approval for new listings
* Allow members to manage their own listings

= Billing and Accounting =

* Financial dashboard
* Setup tax rates by city, state, zip or country
* Export transactions to Xero, QuickBooks or spreadsheet

= SEO =

* Events and directory listings optimized with Rich Snippets

== Installation ==

1. Install and activate the plugin
1. Sign in with your MembershipWorks account
1. Create pages for your membership sign up form, member sign-in form, member directory, member deals page, event calendar using our shortcodes
1. Create your membership levels, and setup your payment gateway
1. Setup your tax rates if applicable
1. Import your members with a csv file
1. For help, videos and documentation, see the Help section in the plugin

== Frequently Asked Questions ==

= Is this a paid plugin? =

This plugin is an interface to your MembershipWorks account, much like how MailChimp or Google Maps plugins are an interface to those systems. When you embed a Google Map on your web page, the map is not generated from your WordPress server. Similarly the heavy lifting and processing for our system happens on our side, including all data storage (triple redundant with daily backups), email delivery, security, updates and compliance. The plugin provides the convenience of integrating features on the website with shortcodes, provides an admin panel on the WordPress dashboard, and checks access on member only pages/posts.

= How do I setup the pages for membership sign-up, directory, deals, event calendar, etc? =

Create pages and put the corresponding shortcode on the page. The shortcode generator can be found in the Help section under MembershipWorks > Help > Basics & Setup > WordPress > WordPress Shortcodes.

= Where can I find the documentation? =

The Help section provides all the documentation. Once you activate the plugin, you can access that under Help from the MembershipWorks menu.

= Can I use Paypal Standard for membership billing? =

No, at this time Paypal Pro is required to interface directly with Paypal for our system. This allows for one page checkout, and for us to track fees and payments, issue refunds, and more.

If you do not have Paypal Pro and do not want to deal with the fees, we recommend using Stripe.

= Can I customize the colors in the membership forms, events, etc? =

Information about customizing CSS can be found in the plugin under MembershipWorks > Help > Customization > CSS & colors

= How do I restrict access to a page or post to members only? =

To restrict the content of the entire post/page to members, place the \[memberonly\] shortcode at the beginning of the post/page. For advanced options please see Help > Basics & Setup > WordPress > Creating Member Only Pages/Posts.

== Screenshots ==

1. Member profiles.
2. Event calendar.
3. Membership Timeline \(CRM\).
4. Membership metrics, signups, renewals and event registrations.
5. Chart membership and event revenue.
6. Interactive member directory map.
7. Member deals.

== Changelog ==

= 0.1 =
* Initial release

= 1.0 =
* Stable release

= 1.2 =
* SEO improvements

= 1.5 =
* Adds menu for label and membership

= 1.6 =
* Adds menu for advanced customization

= 1.6.1 =
* Adds option to disable social share buttons
* Adds option to load js/css inline

= 1.6.2 =
* Widget can now pull contacts from a label
* Adds option to display contact name on cards

= 1.7 =
* Moves plugin settings under MembershipWorks menu
* Improved http request compatibility

= 1.7.1 =
* Adds listlabel and listfolder shortcodes
* Adds support for label specific directory

= 1.7.2 =
* Adds support for group specific event calendar

= 1.7.3 =
* Minor bug fix

= 1.8 =
* Adds listevents shortcode
* Adds ability redirect to another page after signing in \(from account manage screen\)

= 1.8.1 =
* Fixes minor bug with member slideshow widget

= 1.8.2 =
* Fix for members slideshow widget for themes that do not set ID on widget

= 1.8.3 =
* Fix for page titles on themes that do not provide correct number of parameters
* Adds support for directories by folder

= 2.0 =
* Adds support for Member Sign in and redirect to page
* Adds forms, shopping cart and donations feature

= 2.1 =
* Fixes escaped html in event and member widgets
* Search engine support for directories by folder

= 2.1.1 =
* Adds separator between dates in event widget and event list shortcode
* Minor bug fixes

= 2.2 =
* Prevents W3 Total Cache and WP Super Cache from caching dynamic pages
* Adds setting for redirect url on log out

= 3.0 =
* Adds page top offset setting for themes with fixed menu bars
* Supports improved WordPress user synchronization with MembershipWorks Login Connector

= 3.0.1 =
* Fixes compatibility with older MembershipWorks Login Connector version

= 3.0.2 =
* Fixes compatibility with WordPress 4.0.1

= 3.1 =
* Fixes compatibility with WordPress 4.0.1

= 3.2 =
* Improves compatibility with WordPress 4.0.1 with i8n and visual editor plugins

= 3.3 =
* Fixes some PHP warnings
* Thumbnail option for upcoming events widget

= 3.4 =
* Fixes compatibility with Yoast SEO 1.7.3 and up
* Adds membership level filter for signup form
* Adds option to always display name for member slideshow widget

= 3.5 =
* Updated resources to not load from MaxCDN, which may be blocked due to SpamHaus listing

= 3.6 =
* Fixes compatibility with WordPress 4.0.1 and up
* Add options to enable or disable event date/times and location to event list widet

= 3.7 =
* Fixes missing title with Yoast SEO when force rewrite titles is enabled

= 3.8 =
* Removes a deprecated plugin option
* URL encodes certain parameters when calling for server generated pages

= 3.8.1 =
* Renamed function due to function name conflict with Swift Framework

= 3.9 =
* Fixes issue with certain themes/frameworks that call wp_title within wp_head

= 4.0 =
* Fixes issue with unicode quotation marks inside shortcode 

= 4.1 =
* Fixed issue with Facebook share image on HTTPS pages

= 5.0 =
* MemberFindMe is now MembershipWorks!
* Display event or folder widgets with shortcode
* Supports member only content directly
* Customize failure messages for memberonly shortcode
* Closure for memberonly content
* Multiple memberonly blocks per page

= 5.1 =
* Fixes bug with memberonly shortcode that crops off last 13 characters of content when no closure is used

= 5.2 =
* Fixes error with wp_register_style called incorrectly

= 5.3 =
* Fixes issue with shortcodes with certain language settings

= 5.4 =
* Fixes escaped html bug with listevents and listfolder shortcodes
* Fixes issue with wp_enqueue_style not working
* Session expired response for memberonly shortcode
* Adds false and nomessage option for memberonly shortcode
* Adds name shortcode

= 5.5 =
* Fixes potential infinite loop refresh with corrupted cookies
* Fixes undeclared variable warning

= 5.6 =
* Fixes escaped HTML in member widget

= 5.7 =
* Provides option for using HTTP connection to retrieve data for customers running old versions of OpenSSL that do not support PCI compliant HTTPS connections

= 5.8 =
* Provides option to open directory profile links in new tab

= 5.9 =
* Improved shortcode handling

= 6.0 =
* Sets cookie to prevent caching for member only pages on WP Engine
* Adds compatibility with themes that support title-tag

= 6.1 =
* Changes admin dashboard menu to require "edit_users" instead of "add_users" role
* Improvements to bypass caching on member only pages or Ajax crawling protocol requests
* Improvements to progressive enhancement for Ajax crawling protocol

= 6.2 =
* Deprecates Ajax crawling scheme for Googlebot
* Adds login form for page with memberonly content if user session expired
* Increased priority for document_title_parts filter

= 6.3 =
* Tested for WP 5.0+

= 6.4 =
* Fixes "Use of undefined constant" warning

= 6.5 =
* Formally escapes HTML attributes

= 6.6 =
* Minor fixes

= 6.7 =
* Moves script load to footer

= 6.8 =
* Updates code for Facebook crawling of event/directory pages when Yoast SEO is installed

= 6.9 =
* Adds admin menu navigation for Boards

= 6.10 =
* Tweaks to widget image alt text for WCAG

= 6.11 = 
* Adds support for WP ext object cache core functions
* Added meta crawler user agent

= 6.12 =
* Fixes issues with event widget in block editor