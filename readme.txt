=== Paid Memberships Pro - Custom Level Cost Text Add On  ===
Contributors: strangerstudios
Tags: paid memberships pro, pmpro, memberships, ecommerce, level cost
Requires at least: 4.0
Tested up to: 5.0.2
Stable tag: .3.2

Modify the default level cost text per level, per discount code, or globally via advanced settings.

== Description ==
This plugin adds a "level cost text" field to Membership Levels and Discount Codes, allowing you to override PMPro's default level cost text. You can also modify the cost text globally via Advanced Settings. 

On the "Edit Membership Level" or "Edit Discount Code" page, you can override the default level cost using the available placeholders or custom text. On the Memberships > Advanced Settings admin page, you can manage the global rules for all generated membership level cost text.

Please use with care as sometimes the level cost text is the only way a user will know how much their credit card is being charged.

This plugin requires Paid Memberships Pro.

== Installation ==

1. Upload the `pmpro-level-cost-text` directory to the `/wp-content/plugins/` directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Edit a Membership Level, Discount Code or the Memberships > Advanced Settings page to modify generated level cost text.

== Changelog = 
= .3.2 =
* BUG FIX: Didn't display billing amount, initial payment and trial amount values correctly.
* BUG FIX: Better handling of non-US currencies.
* BUG FIX: Fixes issue where lots of 0's can show up after the decimal when PMPro 2.0.2 is active.

= .3.1 =
* SECURITY: Better sanitizing of the level cost text fields.
* BUG/ENHANCEMENT: Fixed styling of the variable references shown on the edit discount code page.

= .3 =
* ENHANCEMENT: Added settings to the Memberships > Advanced Settings page to globally override level cost text.
* ENHANCEMENT: Added variables to be used in custom level cost text fields.

= .2 =
* Adds custom level cost text field to edit membership level page as well as edit discount code page.