=== Analytify - Analytics Dashboard for WordPress (Google Analytics 4) ===
Contributors: hiddenpearls
Donate link: https://paypal.me/Analytify
Tags: analytics, WordPress analytics, google analytics, google analytics dashboard, google analytics 4
Requires at least: 4.0
Tested up to: 7.0
Stable tag: 9.1.2
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The easiest WordPress analytics plugin for Google Analytics 4. See visitors, pages, and traffic in your dashboard.

== Description ==

[Homepage](https://analytify.io/?ref=27&utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=homepage-link) | [Documentation](https://analytify.io/documentation/?utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=documentation-link) | [Support](https://analytify.io/support/) | [Demo](https://www.youtube.com/watch?v=D02R6eP3olM) | [Premium Version](https://analytify.io/pricing/?ref=27&utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=premium-version-link)

= Google Analytics, without leaving WordPress =

**Analytify is the WordPress analytics plugin that brings Google Analytics 4 into your dashboard, so you can see how your site is actually performing without ever logging into Analytics.** Trusted by 20,000+ websites and an official Google Analytics Technology Partner, Analytify turns raw GA4 data into a dashboard you'll actually want to check.

Connect your site in one click, no code and no developer required. Once connected, your stats show up on the main WordPress dashboard and, for any individual post or page, right inside the editor too, so you can see how that specific piece of content is performing without switching tabs.

= What you get free =

* **Core stats at a glance.** Visitors, page views, new vs. returning visitors, top pages, and geographic data, all in one dashboard.
* **Page-level stats in the admin.** See views, users, bounce rate, and average time on page for any post, right where you're already editing it.
* **Geographic breakdown.** A visual map showing exactly which countries and cities send you traffic.
* **Social media stats.** See how much traffic your social channels are actually sending.
* **SEO insight.** See which posts and pages perform best, so you know what to write more of.
* **System stats.** Visitor breakdowns by operating system, browser, and device.
* **Cleaner tracking data** *(new)*. Tell Analytify which URL query parameters to ignore, so the same page doesn't get split into multiple entries in your reports.
* **Faster admin load times** *(new)*. Core plugin JavaScript is now minified.
* **Consent-ready.** Native integration with CookieYes and Google Consent Mode v2, plus developer filters for any other consent plugin (see "Built for developers" below).
* **Multilingual.** Fully translatable and WPML-compatible, with community translations including French, Turkish, and Hungarian.

= Upgrade to Pro for advanced tracking =

The free version covers what most sites need to get started. [Analytify Pro](https://analytify.io/pricing/?utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=analytify+io) adds:

* **Real-time reporting.** The same real-time view you'd get in Google Analytics, without leaving WordPress.
* **Enhanced eCommerce for [WooCommerce](https://analytify.io/add-ons/woocommerce/?utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=woocommerce-text-link).** Track product clicks, impressions, add-to-cart events, average order value, transaction revenue, and cart abandonment, right inside WordPress.
* **Enhanced eCommerce for [Easy Digital Downloads](https://analytify.io/add-ons/easy-digital-downloads/?utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=eCommerce+Tracking+for+Easy+Digital+Downloads).** Track digital sales, transactions, and revenue, synced with Google Analytics.
* **Multistep form tracking** *(new)*. Automatically detects multi-step forms and tracks impressions, step views, submissions, and abandonment rate.
* **LMS & membership tracking** *(new)*. Dedicated dashboards for LearnDash and LifterLMS course performance, and for Paid Memberships Pro membership growth, renewals, and cancellations.
* **Multi-platform pixel tracking** *(new)*. Manage pixels for Meta, TikTok, Microsoft, Snapchat, X, Pinterest, and LinkedIn from one settings screen.
* **[Campaign tracking](https://analytify.io/add-ons/campaigns/?utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=Campaigns+Tracking).** See which campaigns are actually driving traffic and conversions.
* **[Goals dashboard](https://analytify.io/add-ons/google-analytics-goals-wordpress/?utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=Goals+Dashboard), events, and forms tracking.** Track custom events and form submissions (Contact Form 7, Gravity Forms, WPForms, Formidable Forms, and more).
* **Custom dimensions.** Track the specific data points that matter to your site.
* **Author-specific analytics** *(new)*. Give each author a dashboard showing stats for their own posts only.
* **[Automated email reports](https://analytify.io/add-ons/email-notifications/?utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=Automated+Email+Notifications).** Schedule stats to land in your inbox, or your client's.
* **Shortcodes.** Simple and advanced custom stat displays for posts, pages, and widgets on the front end.
* **A free bonus:** the [Google Analytics Dashboard Widget](https://analytify.io/add-ons/google-analytics-dashboard-widget-wordpress/?utm_source=wp-org&utm_medium=description&utm_campaign=pro-upgrade&utm_content=Dashboard+Widget) add-on stays free either way.

= Setup =

1. Install and activate Analytify.
2. Go to Analytify > Settings and connect your Google Analytics account with one click.
3. Select your GA4 property, and your dashboard starts filling in with data.
4. Optional: for your own API rate limits, add your own Google API keys under the Advanced tab, using your own project from the [Google Console](https://console.developers.google.com/project). [Video guide](https://analytify.io/custom-api-keys-video) or [written tutorial](https://analytify.io/google-api-tutorial).

= Using Analytify with Google Tag Manager =

Already firing custom events through Google Tag Manager? Analytify can run alongside GTM without double-counting the same events. [Watch how to set it up](https://www.youtube.com/watch?v=nZPBqqvnYT8).

= Built for developers =

Analytify ships with filters for consent management, so you can wire it up to any cookie consent plugin without touching core code:

* `analytify_consent_server_blocks_tracking`, return true server-side when a visitor hasn't consented, so Analytify won't print gtag in the page head.
* `analytify_consent_defer_gtag`, return true to load gtag only after consent is granted in the browser.
* `analytify_consent_ready_dom_events`, the browser events that trigger deferred gtag loading (defaults to `analytifyConsentGranted`).
* `analytify_consent_deferred_js_loader_name`, the global function your front end can call to load gtag right after consent (defaults to `analytifyLoadGtagAfterConsent`).

When CookieYes is active, Analytify automatically outputs Google Consent Mode v2 defaults before loading gtag.js; disable this with `analytify_gtag_consent_defaults_enabled` if your CMP already handles it.

Analytify is also [open source on GitHub](https://github.com/Analytify/wp-analytify), issues and pull requests are welcome.

= What people are saying =

> "I've been using the Pro version for quite a while. I really love how it gives me in-depth information about each individual post. That kind of info is hard to navigate to in Google Analytics even if you're relatively familiar with it already."

> "This is something clients really love, they want to see traffic to their site without logging into Google Analytics and working through its reporting modules. It shows the headline stats and more, for every single page."

> "Many of my clients don't want to view analytics outside their dashboard. This plugin integrates GA beautifully within the WordPress admin panel."

If you find Analytify useful, a rating or review helps other WordPress users find it. Questions? Ask on the [support forum](https://wordpress.org/support/plugin/wp-analytify/) or through [premium support](https://analytify.io/support/).


== Installation ==

This section describes how to install the Google Analytics Dashboard by Analytify plugin and get it working.

= 1) Install =

= Modern Way: =
1. Go to the WordPress Dashboard "Add New Plugin" section.
2. Search For "Analytify".
3. Install, then Activate it.

= Old Way: =
1. Unzip (if it is zipped) and Upload `wp-analyify` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

= 2) Configure =
1. Reach out to the Analytify -> Settings Page
2. Go to "Step 1", click the "Log in with Google Analytics Account" button
3. Connect and give Analytify App access to your Google Analytics account.
4. After authentication, select your preferred profiles for front/admin Statistics via Settings -> Profile tab.

== Frequently Asked Questions ==

= What is Google Analytics 4 (GA4)? =

GA4 is Google's current version of Analytics, built around events rather than pageviews, with cross-device tracking and deeper Google Ads integration. A GA4 tracking ID looks like G-XXXXXXXXXX.

= Does Analytify support GA4? =

Yes. Analytify inserts the modern gtag.js tracking script by default, with GA4 support out of the box.

= Do I need a Google Analytics account to use this plugin? =

Yes, you'll need a free Google Analytics account before you can track your site. Here's a [setup guide](https://analytify.io/how-to-setup-your-account-at-google-analytics/) if you don't have one yet.

= How do I connect my Google Analytics account? =

Go to Analytify > Settings > Authentication and click "Log in with Google Analytics Account," then authorize access. Choose which property to track under the Profiles tab.

= Can I use my own Google API keys? =

Yes, and Google recommends it. Create a project in the [Google Console](https://console.developers.google.com/project) to get your own Client ID, Client Secret, and Redirect URL, then add them under the Advanced tab before connecting. [Video guide](https://analytify.io/custom-api-keys-video) or [written tutorial](https://analytify.io/google-api-tutorial).

= Google Analytics says my tracking code isn't detected, what should I check? =

Confirm your theme correctly calls wp_head and wp_footer, that "Install Google Analytics tracking code" is switched on in settings, and clear your cache if you're running a caching plugin. Also check whether admin users are excluded from tracking, that would explain why your own visits aren't showing up.

= How long until I see data in my dashboard? =

Give it up to 24 hours for full reports. Pro includes a real-time dashboard that updates immediately.

= Does Analytify slow down my site? =

No. Analytify's own JS and CSS assets only load on Analytify's plugin pages in wp-admin, not on your public-facing site.

= What does "(not set)" mean in my reports? =

It means Google Analytics didn't receive enough information for that dimension, often because a visitor didn't allow cookies.

= Is Analytify compatible with WPML and other multilingual setups? =

Yes. Analytify is fully translatable and WPML-compatible.

= How do I set up email reports, events, forms, or custom dimensions? =

Each lives under Analytify > Settings once the relevant add-on is active: [email reports](https://analytify.io/add-ons/email-notifications/), [events tracking](https://analytify.io/doc/get-started-with-the-google-events-tracking-addon/), [forms tracking](https://analytify.io/add-ons/forms-tracking/) (Contact Form 7, Gravity Forms, WPForms, Formidable Forms, and custom forms), and [custom dimensions](https://analytify.io/doc/get-started-with-custom-dimensions-addon/).

= Do free users get support? =

Yes, though support is prioritized for Pro customers. Free users can ask on the [WordPress.org support forum](https://wordpress.org/support/plugin/wp-analytify/); Pro customers get [priority support](https://analytify.io/doc/right-way-ask-support/).

= Can I request a feature or report a bug? =

Yes, reach out through our [support page](https://analytify.io/support/), or open an issue on [GitHub](https://github.com/Analytify/wp-analytify).


== Screenshots ==

1. Google Analytics by Analytify - [Real Time Stats](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Real+Time+Stats)
2. Google Analytics by Analytify - [General Statistics](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=General+Statistics)
3. Google Analytics by Analytify - [Top Countries](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Top+Countries)
4. Google Analytics by Analytify - [Top Referrers/Browsers](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Top+Browsers)
5. Google Analytics by Analytify - [Enhanced eCommerce Google Analytics Tracking for WooCommerce](https://analytify.io/add-ons/woocommerce/?utm_source=wp-org&amp;utm_medium=screenshots&amp;utm_content=woocommerce&amp;utm_campaign=pro-upgrade)
6. Google Analytics Dashboard By Analytify - [Settings Screen](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Settings)
7. Google Analytics by Analytify - [Automated Email Reports](https://analytify.io/add-ons/email-notifications/?utm_source=wp-org&amp;utm_medium=screenshots&amp;utm_content=email-notifications&amp;utm_campaign=pro-upgrade)
8. Google Analytics by Analytify - [Shopping Behavior funnel for WooCommerce Google Analytics](https://analytify.io/add-ons/woocommerce/?utm_source=wp-org&amp;utm_medium=readme-org-screenshots&amp;utm_content=woocommerce-funnel&amp;utm_campaign=pro-upgrade)
9. Google Analytics by Analytify - [UTM Campaigns Dashboard](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=UTM+Campaigns+Dashboard)
10. Google Analytics by Analytify - [Google Analytics dashboard widget for WordPress](https://analytify.io/add-ons/google-analytics-dashboard-widget-wordpress/?utm_source=analytify-lite&amp;utm_medium=readme-org-screenshots&amp;utm_content=dashboard-widget&amp;utm_campaign=pro-upgrade).
11. Google Analytics by Analytify - [Google Custom Dimensions Dashboard](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Custom+Dimensions+Dashboard)
12. Google Analytics by Analytify - [Events Tracking Dashboard](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Events+Tracking+Dashboard)
13. Google Analytics by Analytify - [Forms Tracking Settings](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Forms+Tracking+Settings)
14. Google Analytics by Analytify - [Forms Tracking Dashboard](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Forms+Tracking+Dashboard)
15. Google Analytics by Analytify - [Google Custom Dimensions Tracking](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Custom+Dimensions+Tracking)
16. Google Analytics by Analytify - [Google Optimize A/B Testing](https://analytify.io/pricing?utm_source=wp-org&utm_medium=screenshots&utm_campaign=pro-upgrade&utm_content=Google+Optimize)


== Changelog ==

= 9.1.2 – 2026-08-17 =
* Enhancement: All CSS/JS assets are now properly minified for faster page loads.

= 9.1.1 – 2026-08-04 =
* New Feature: Install and activate the Google Analytics Dashboard Widget add-on directly from the Add-ons page.
* Enhancement: Search Console Keywords csv export report now shows Click-Through Rate (CTR) and Average Position.
* Enhancement: More reliable Top Pages links and higher default row limit in reports.
* Bug Fix: Fixed dashboard/CSV/Excel export occasionally skipping chart data.
* Bug Fix: Fixed incorrect status messages while installing/activating an add-on.
* Bug Fix: Fixed translated text not loading on the Add-ons page.
* Security: Hardened CSV export against a formula-injection bypass in exported links.
* Security: Restored proper permission checks when activating add-ons.
* Security: Improved filtering of unsafe links in dashboard exports.

= 9.1.0 – 2026-07-15 =
* New Feature: Introduced new export dashboard reports to Excel, PDF, or CSV.
* New Feature: Show which Google account is connected under Authentication settings.
* Enhancement: Better compatibility with cookie consent plugins, including support for Google's latest consent standards.
* Enhancement: Added a tooltip to the Browser Breakdown chart.
* Enhancement: Review notice is now dismissed/snoozed per user instead of site-wide, and only shown to users with dashboard access.
* Enhancement: Limited dashboard access for Subscriber accounts.
* Enhancement: Smoother setup when connecting your Google Analytics account.
* Enhancement: Improved reliability of comparison data in scheduled email reports.
* Bug Fix: New vs. Returning Users now shows exact counts instead of rounded numbers.
* Bug Fix: Fixed 404, JavaScript, and AJAX error (Advanced settings) tracking.
* Security: Hardened CSV export to prevent unsafe file content when opened in Excel or Sheets.
* Security: Improved account verification email security.
* Compatibility: Compatible with PHP 8.5.
* Compatibility: Compatible with WordPress 7.0.

= 9.0.2 – 2026-05-12 =
* Bug Fix: Resolved Dutch, French, and Spanish translation issues.
* Enhancement: Refactored and optimized code for improved maintainability and performance.

= 9.0.1 – 2026-05-01 =
* Security: Bundled jsPDF and html2canvas locally for dashboard PDF exports; updated versions.
* Enhancement: Minor dashboard asset loading cleanup and improvements.

= 9.0.0 – 2026-04-29 =
* New Feature: Introduced new paid module Pixels Tracking.
* New Feature: Introduced new paid module LifterLMS Tracking.
* New Feature: Introduced new paid module LearnDash Tracking.
* New Feature: Introduced new paid module Paid Memberships Pro Tracking.
* New Feature: Added custom dimensions to support enhanced `tel:` link tracking ahead of Enhanced Tel Link Analytics reports.
* New Feature: Custom date range for scheduled email reports, including “yesterday” and safer scheduling.
* New Feature: Exclude selected URL query parameters from tracking (Advanced).
* Enhancement: Locally hosted GA serves minified `gtag.min.js` and drops legacy `gtag.js` handling.
* Enhancement: Switched from `error_log` to Analytify logger; improved diagnostic log and debugging UX.
* Enhancement: “Send Email” reports: loading states, strings, and more reliable background sends.
* Enhancement: Code re-factorization and improvements.
* Enhancement: Pie chart sizing and spacing on the dashboard.
* Bug Fix: YouTube video tracking script error and noisy console output.
* Bug Fix: General Stats email template styling and safer comparison label escaping.
* Bug Fix: Measurement Protocol GA4 class reference so MP events load and send correctly.
* Compatibility: Compatible with WordPress 7.0

= 8.1.3 – 2026-03-17 =
* Security: Added nonce verification and capability checks to prevent CSRF attacks.

= 8.1.2 - 2026-03-09 =
* Enhancement: Improve GA4 Measurement Protocol reliability by fixing secret handling and auto‑acknowledging User Data Collection.

= 8.1.1 - 2026-02-27 =
* Enhancement: Fixed mislabeled “Engaged Sessions” metric in single post/page analytics—restored correct “Pages / Session” label and added a proper Engaged Sessions box.
* Enhancement: Added the `analytify_session_date_range` filter to control session date range analytics on the dashboard screen.
* Enhancement: Added an admin notice when Measurement ID is not set for E-Commerce users.
* Enhancement: Minor bug fixes and code improvements.

= 8.1.0 - 2026-02-09 =
* Enhancement: Improve Search Console domain detection — handle trailing slashes, prefer domains with data, fallback to accepted domains.
* Enhancement: Removed the deprecated Force SSL, and Anonymize IP (UA-only) options as part of GA4 cleanup and UI simplification.
* Enhancement: Updated the POT file.
* Bug Fix: Fixed display alignment issues with the Sessions column on the Products page.
* Bug Fix: Restored the ability for authors to show/hide analytics data as intended.

= 8.0.1 - 2026-01-07 =
* Bug Fix: Google Analytics Authentication broke for some users i.e Stats appeared 0 Zeros after recent 8.0.0 update.

= 8.0.0 - 2026-01-06 =
* Major update: Applied coding standards and optimized the codebase for improved performance and faster loading times.
* New Feature: Added reset button for complete cleanup of plugin settings and cache.
* New Feature: Added Sessions (30 Days) column to the Post list.
* Enhancement: Improved settings tabs and JSON output for cleaner UI and enhanced security.
* Enhancement: Enhanced diagnostic logging with improved completeness, security, and error handling.
* Enhancement: Added browser breakdown statistics to the dashboard.
* Enhancement: Added a toggle in the Advanced tab to send email alerts when Google re-authentication fails.
* Enhancement: Added a Tools section in the Help tab for improved user experience.
* Enhancement: Improved Analytify notification layout for better readability.
* Bug Fix: Disabled extra error logging for cleaner logs.
* Compatibility: Compatible with WordPress 6.9

= 7.1.3 - 2025-11-20 =
* Bug Fix: Fix Custom Dimensions Reporting.
* Enhancement: Remove extra scripts.
* Enhancement: Remove extra console logs.

= 7.1.2 - 2025-11-14 =
* Enhancement: Updated the logic to handle GA4 refresh token requests errors.

= 7.1.1 - 2025-11-11 =
* Bug Fix: Fix Analytify Notice Error on failing GA4 refresh token.

= 7.1.0 - 2025-11-11 =
* New Feature: Introduced a new dimension for Archives statistics.
* Enhancement: Removed deprecated JavaScript code.
* Enhancement: Removed unnecessary error logs.
* Enhancement: Updated the logic to handle refresh token requests.
* Enhancement: Limited admin notices to Analytify-specific pages only.
* Enhancement: Corrected bar chart color display in the "What happens when users come to your site" widget.
* Bug Fix: Resolved gtag script compatibility issue with browser extensions.
* Bug Fix: Fixed New vs Returning visitors calculation and display.
* Bug Fix: Fixed profile ID mismatch issue.
* Bug Fix: Fixed duplicate script tag for custom JavaScript scripts.


= 7.0.4 - 2025-09-16 =
* Bug Fix: Resolved a conflict with the "WP Accessibility" plugin.
* Bug Fix: Corrected tooltip behavior in PDF reports.

= 7.0.3 - 2025-08-12 =
* Bug Fix: Resolved an issue with retrieving the Google Analytics 4 properties values from user GA4 account.
* Enhancement: Minor improvements to admin notice behavior.
* Compatibility: Compatible to WordPress 6.8

= 7.0.2 - 2025-07-31 =
* Bug Fix: Resolved issue with the review notice—now displays and functions as intended.

= 7.0.1 - 2025-07-24 =
* Bug Fix: Fixed a critical bug where the plugin would throw a PHP fatal error (Uncaught TypeError: Cannot access offset of type string on string) if the Google Analytics token was invalid or missing.
* Enhancement: Chart legend and data labels now support translations, ensuring correct display for localized label names.

= 7.0.0 - 2025-07-22 =
* Enhancement: Discarding Google Analytics 4 SDK and rolling out our own library for GA4 API calls. This would help in resolving conflicts with other plugins that were using the same library.
* Enhancement: Redesigned pagination layout in the dashboard for better user experience and improved navigation through large datasets.
* Enhancement: Enhanced sorting functionality in the dashboard with improved visual indicators and more intuitive controls.
* Enhancement: Streamlined import functionality in the dashboard with better error handling and user feedback.
* Enhancement: Various bug fixes, performance optimizations, and code improvements for enhanced stability and maintainability.
* Compatibility: Compatible to WordPress 6.8

= 6.1.0 - 2025-04-24 =
* New Feature: Analytify Paid add-ons (Forms, Goals, and Email) are now merged into Analytify Pro as modules. You can directly activate/deactivate these from the Add-ons page.
* New Feature: Download or export dashboard analytics in PDF format.
* New Feature: Introduces the Pie chart over doughnut chart in Analytify dashboard.
* New Feature: Introducing a new filter to customize the device Pie chart colors in Analytify dashboard: [analytify_visitor_devices_chart_colors](https://analytify.io/doc/analytify-filters/#changing-colors-of-visitors-devices-pie-chart)
* New Feature: Introducing a new filter to customize the visitor Pie chart colors in Analytify dashboard: [analytify_new_vs_returning_visitors_chart_colors](https://analytify.io/doc/analytify-filters/#changing-colors-of-new-vs-returning-vistors-pie-chart)
* Bug Fix: Added missing icon for Email report in mobile device stats.
* Compatibility: Compatible to WordPress 6.8

= 6.0.2 - 2025-03-10 =
* Bug Fix: Google Ads module not activating issue when Pro version is active, fixed.
* Compatibility: Compatible to WordPress 6.7

= 6.0.1 - 2025-03-05 =
* Bug Fix: Google Ads module not activating issue fixed.
* Compatibility: Compatible to WordPress 6.7


= 6.0.0 - 2025-03-05 =
* New Feature: Analytify Paid add-ons (Campaigns, Authors, Easy Digital Downloads, and WooCommerce) are now merged into Analytify Pro as modules. You can directly activate/deactivate these from the Add-ons page.
* New Feature: Introducing a new dashboard for Video Tracking in the Conversions tab. 
* New Feature: Introducing the Google PageSpeed Insights Dashboard in Acquisitions tab to analyze your website's performance.
* New Feature: Added a User Interests Stats in the Demographics Dashboard for deeper audience insights.
* New Feature: Added Conversion Rate stat in WooCommerce and EDD dashboards.  
* New Feature: Added First Purchasers stat in WooCommerce and EDD dashboards. 
* Bug Fix: CSV Export options not working for WooCommerce dashboards. 
* New Feature: Introducing a new filter to hide specific navigation tabs from Analytify dashboard: [analytify_filter_navigation_items](https://analytify.io/doc/analytify-filters/#hide-specific-navigation-tabs-from-the-analytify-dashboard)
* New Feature: Introducing a new filter to customize the Introduction message in Analytify Email Reports: [analytify_custom_email_message](https://analytify.io/doc/analytify-filters/#customizing-the-introduction-message-in-analytify-email-reports) 
* Bug Fix: Fixed the "View All" links in the Woocommerce Dashboard. 
* Security Fix: Missing Authorization to Authenticated (Subscriber) setting change.
* Enhancement: Added a notice to inform users that reporting is disabled when "Test Email" is triggered with Email Reporting turned off. 
* Enhancement: Minor bug fixes and code improvements.

= 5.5.1 - 2025-02-04 =
* Bug Fix: Mobile logos missing in System Stats section on Overview Dashboard. 
* Bug Fix: Translation loading too early issue in WP 6.7.0
* Security Fix: Missing Authorization to Authenticated (Subscriber) export_settings.
* Enhancement: Updated ECharts library to the latest version 5.5.1
* Enhancement: Email Reports Dark Mode style fixes.
* Enhancement: Removed deprecated Google Optimize add-on.
* Enhancement: CSS improvements for charts.
* Enhancement: Minor bug fixes and code improvements.

= 5.5.0 - 2024-11-06 =
* Enhancement: Removing Google Analytics V3 Library from the plugin completely.
* Enhancement: Disable Front end Stats Option for GA4 and will remove in next releases. ShortCodes will work though.
* Enhancement: Divi Theme Conflict Issue.
* Enhancement: Fetch_log Security issue.
* Enhancement: GA3 removal from core, pro and all addons.
* Enhancement: Email Reports style improved for Dark Mode.
* Bug Fix: Resolved the error occurring on activating the Email Notifications add-on.
* Compatibility: Compatible to WordPress 6.7

= 5.4.3 - 2024-09-11 =
* Enhancement: UI improvement in Opt-out form.
* Compatibility: Compatible to WordPress 6.6

= 5.4.2 - 2024-09-10 =
* Bug Fix: Rank Math plugin conflict fixed.
* Security Fix: Fixed Opt-out and Opt-in consent.

= 5.4.1 - 2024-08-22 =
* Bug Fix: TikTok plugin conflict fixed.
* Compatibility: Compatible to WordPress 6.6

= 5.4.0 - 2024-08-21 =
* Security Fix: Nonce and High User Permission in Opt-Out Action fixed.
* Enhancement: Google Search Console URL and Domains Match.
* Compatibility: Compatible to WordPress 6.6

= 5.3.1 - 2024-07-30 =
* Enhancement: Rollback Google SDK causing issues on many sites.
* Compatibility: Compatible to WordPress 6.6

= 5.3.0 - 2024-07-24 =
* New Feature: Added Google Search Console (GSC) visitors, CTR, clicks in email notifications.
* Enhancement: Google SDK Updated.
* Compatibility: Compatible to WordPress 6.6

= 5.2.5 - 2024-04-25 =
* Enhancement: Single stats email bugfix and improvement.
* Compatibility: Compatible to WordPress 6.5

= 5.2.4 - 2024-04-17 =
* Security Fix: login nonce applied on authenticating analytify with Google Analytics account.
* Compatibility: Compatible to WordPress 6.5

= 5.2.3 - 2024-03-27 =
* Security Fix: logout nonce applied on disconnect analytify with Google Analytics.
* Enhancement: made readable some parts of the diagnostic log.
* Compatibility: Compatible to WordPress 6.5

= 5.2.2 - 2024-03-06 =
* Bug Fix: End Date Selection on edit pages/posts analytics section.
* Compatibility: Compatible to WordPress 6.4

* New Feature: Introducing Google Ads Conversion Tracking Module in Analytify Pro. Track your Google Ads conversions in Google Analytics.
* New Feature: Custom Gutenberg block in Analytify Pro for creating ShortCodes. Google Analytics 4 Compatible.
* New Feature: Introducing locally host Google Analytics GA4 library.
* New Feature: Local analytics library file name will change every day to prevent ad blockers from blocking our tracking script.
* Enhancement: Dashboard Layouts have improved responsive interface.
* Enhancement: RankMath Pro compatibility with locally host Google Analytics GA4 library.
* Enhancement: Improved Turkish translation.
* Compatibility: Compatible to WordPress 6.4 and PHP 8.2.10

= 5.2.1 - 2024-01-22 =

* New Feature: Introducing Google Ads Conversion Tracking Module in Analytify Pro. Track your Google Ads conversions in Google Analytics.
* New Feature: Custom Gutenberg block in Analytify Pro for creating ShortCodes. Google Analytics 4 Compatible.
* New Feature: Introducing locally host Google Analytics GA4 library.
* New Feature: Local analytics library file name will change every day to prevent ad blockers from blocking our tracking script.
* Enhancement: Dashboard Layouts have improved responsive interface.
* Enhancement: RankMath Pro compatibility with locally host Google Analytics GA4 library.
* Enhancement: Improved Turkish translation.
* Compatibility: Compatible to WordPress 6.4 and PHP 8.2.10

= 5.2.0 - 2023-11-17 =

* New Feature: Settings option added to remove plugin instances from database on uninstallation of the plugin.
* Bug Fix: User id tracking bug fixed.
* Enhancement: Security added for single post email feature.
* Enhancement: Minor Style updates for Analytify admin bar icon and general stats section charts tooltip.
* Enhancement: Made the compare chart script to only load on relevant pages.
* Enhancement: Translation files updated.
* Compatibility: Compatible to WordPress 6.4

= 5.1.1 - 2023-09-04 =
* Bug Fix: security nonce checks applied.
* Enhancement: Referrers increased to 10 and pagination applied to show 30 records.
* Enhancement: Content updates.
* Compatibility: Compatible to WordPress 6.3

= 5.1.0 - 2023-08-10 =
* Bug Fix: PHP undefined warnings fixed.
* Bug Fix: RankMath instant indexing addon conflict fixed.
* Bug Fix: PHP 8.2.* deprecation notices fixed.
* Bug Fix: Non Selected sections emailed in reports fixed in Analytify Email addon.
* Enhancement: Cache cleared notification behavior changed.
* Enhancement: Real time dashboard header section layout changed.
* Enhancement: Help Chatbot icon location changed.
* Enhancement: Comparison graph problems fixed in Analytify Pro.
* Enhancement: No campaigns found message and layout modified in Analytify Campaigns.
* Enhancement: WPforms Pro compatibility added in Analytify Forms.
* Enhancement: Move to Google Analytics GA4 notice added for GA users.
* Compatibility: Compatible to WordPress 6.3

= 5.0.5 - 2023-06-20 =
* Bug Fix: Client ID used in mp secret.
* Bug Fix: Broken getting started link removed.
* Bug Fix: Comparison graph 0 index problem fixed in PRO.
* Bug Fix: Purchase session source/medium fixed in WooCommerce addon.
* Bug Fix: Purchase session source/medium fixed in edd addon.
* Bug Fix: custom form tracking option on/off state corrected.
* Enhancement: Unused code/files removed.
* Enhancement: Cache successfully cleared notice added.
* Enhancement: Delete cache button redirect behavior changed.
* Enhancement: Events tracking settings form restriction removed in PRO version.
* Enhancement: Links on plugin page behavior changed to open in new tab.
* Enhancement: Line graph data corrected in PRO version.
* Enhancement: Top numbers under "visitors" and "views" flipped.
* Enhancement: Events tracking js file minified in PRO.
* Enhancement: translation files updated.

= 5.0.4 - 2023-05-30 =
* Bug Fix: JS console errors.
* Bug Fix: Mobile layout fixed.
* Bug Fix: Google Analytics report url.
* Bug Fix: Comparison graph month view position corrected.
* Bug Fix: Comparison graph stats on line and bar fixed.
* Bug Fix: World map ShortCode errors.
* Bug Fix: AMP addon PHP warnings fixed.
* Bug Fix: WooCommerce addon PHP warnings fixed.
* New Feature: Stats cache time filter added analytify_stats_cache_time.
* New Feature: EDD addon source/medium UTM Analytics feature added for GA4.
* Enhancement: Refresh stats link redirect to the original page.
* Enhancement: Necessary spaces added in Analytify page titles.
* Enhancement: Changelog button text changed.
* Enhancement: Use the saved date from the main dashboard DatePicker.

= 5.0.3 - 2023-05-10 =
* Bug Fix: Fatal error fixed on php 8.2 when closing error log file.
* Bug Fix: Enable stats/analytics section under posts/pages fixed.
* Bug Fix: Not all stats section getting refreshed until two clicks after changing date from today to something else.
* New Feature: Yesterday option added in date picker.
* New Feature: Refresh stats link added in admin bar.
* Enhancement: Error handling added for js script.
* Enhancement: Miscellaneous tracking script added.
* Enhancement: Keyword section heading changed.
* Enhancement: Images optimized and duplicate images removed from the code.
* Compatibility: Backward compatibility added for php 7.2.
* Compatibility: Compatible to WordPress 6.2

= 5.0.2 - 2023-04-26 =
* Bug Fix: Guzzle removed from GA3 Google library
* Compatibility: Compatible to WordPress 6.2

= 5.0.1 - 2023-04-26 =
* Bug Fix:     Guzzle conflict with other plugins using guzzle library.
* Bug Fix:     Error handling when not selected both profile for posts and dashboard.
* Bug Fix:     Campaigns addon active/deactivate state corrected.
* Bug Fix:     Show Analytics under posts/pages to selected user roles.
* Enhancement: Guzzle removed from old library.
* Enhancement: Exception handling. errors added in logger instead of showing them on page.
* Enhancement: Remember date selection feature added on date picker.
* Enhancement: Online visitors real-time color changed to light yellow.

= 5.0.0 - 2023-04-18 =
* New Feature: Google Analytics GA4 integration is added.
* New Feature: Google Search Console (webmasters) Integrated.
* Enhancement: Pageload speed improvement.
* Enhancement: UI Improvements.

= Earlier versions =

Versions before 5.0.0 are in the [full changelog](https://plugins.trac.wordpress.org/browser/wp-analytify/tags/9.1.2/readme.txt?rev=3708452).

== Upgrade Notice ==

= 9.1.2 =
* Important Release, Update carefully. Report us back if you face any issues. Thanks for using Analytify.
