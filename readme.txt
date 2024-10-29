=== Ask Me Anything (Anonymously) ===
Contributors: arunbasillal
Donate link: http://millionclues.com/donate/
Tags: ask me anything, ama, tumblr style questions, qanda, question and answer, anonymous questions, ask questions anonymously, shortcode, Sarahah
Requires at least: 2.7
Tested up to: 6.3.1
Requires PHP: 5.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add a page or widget using a shortcode where users can ask you questions anonymously and list them with your answers. Works on Multisite.

== Description ==

The Ask Me Anything (AMA) plugin lets you add a form on your website where users can ask you questions anonymously. 

The AMA plugin uses the shortcode **[askmeanythingpeople]** to add the form. It also lists the questions and answers neatly. There are 20 attributes and few example configurations are listed towards the end of this description. 

The included shortcode generator lets you easily configure and customize the shortcode. You will find it in WordPress Admin > Settings > Ask Me Anything (see screenshots page). All you have to do to create the AMA page, is to install the plugin, and copy the shortcode from the generator into a page or post. 

[Live Example](http://kuttappi.com/ask/) [**NOTE: Questions and tests posted here are unanswered and deleted. Please use the [support forum](https://wordpress.org/support/plugin/ask-me-anything-anonymously) for questions.**]

AMA saves questions as WordPress comments. So **comments have to be enabled** on the page or post. You can use the shortcode on a text widget by using the page ID of the page or post in the post_or_page_id attribute of the shortcode. Please refer to the Installation page for more details. 

Questions from users can be found along with your WordPress comments in WordPress Admin > Comments. You can reply to them here. Depending on your settings in Settings > Discussion, new questions will be held for moderation (if 'Comment author must have a previously approved comment' is checked) and you will be notified via email (if 'Anyone posts a comment' or 'A comment is held for moderation' is checked).

**Main Features:**

*   Questions and answers are WordPress comments. New database tables are not created.
*   Fully customizable using 20 shortcode attributes. 
*   Easy to use shortcode generator in Settings > Ask Me Anything.
*   Compatible with most themes out of the box since it mostly uses WordPress default styles.
*   Proper CSS ID's and classes to easily style if necessary. 
*	Easily style your AMA page with built-in CSS box. Find it in Settings > Ask Me Anything > 'Custom CSS' tab.
*	Custom CSS can be minified and is loaded only on AMA pages.
*   Simple and effective spam/bot check via a test question. Test question can be customized. 
*   Multisite compatible. 
*   Translation ready.

**How To Use Ask Me Anything** 

To create an AMA page, add the shortcode into a WordPress Page or Post. 

Default usage:

`[askmeanythingpeople]`

List the 5 most recent questions and answers on the sidebar:

`[askmeanythingpeople post_or_page_id="123" questions_per_page="5" show_question_box="false" show_navigation="false"]`

Note: This assumes that 123 is the page or post id that is the dedicated Ask Me Anything page. 
Refer the 'Installation' page for more details.`

**Complete Attribute List With Default Values**

Here are all the available attributes and their default values. 

`post_or_page_id="0"
latest_first="true"
anonymous_name="Someone"
success_text="Your question is saved and will appear when it is answered."
question_box_title="Your Question"
placeholder="In the spirit of keeping it anonymous please do not leave any personal information."
test_question="What is 7+5?"
test_answer="12"
test_placeholder="Enter your answer."
ask_button_text="Ask Anonymously"
answer_list_title="Answers So Far.."
no_answers_text="Be the first to ask!"
questions_per_page="10"
avatar="monsterid"
avatar_size="96"
show_question_box="true"
show_answers="true"
show_navigation="true"
show_test_question="true"
show_do_action="false"
give_thanks="false"`

Please read the Installation page and FAQ page for more information. If you still have questions, please the Support page for help, bug reports and new feature requests. 

If you like my plugin, please [rate it](https://wordpress.org/support/plugin/ask-me-anything-anonymously/reviews/?rate=5#new-post) and give me credit by setting the give_thanks attribute to true (default is false) in the shortcode. Thanks!

GitHub: [arunbasillal/ask-me-anything-anonymously](https://github.com/arunbasillal/ask-me-anything-anonymously)

== Installation ==

To Install This Plugin:

1. Install the plugin through the WordPress admin interface, or upload the plugin folder to /wp-content/plugins/ using ftp.
2. Activate the plugin through the 'Plugins' screen in WordPress. On a Multisite you can either network activate it or let users activate it individually. 

To Create A Page / Post With The Ask Me Anything Form:

1. Go to WordPress Admin > Settings > Ask Me Anything to generate and customize the shortcode. Copy this shortcode. 
2. Add a new Page / Post and make sure comments are enabled. Refer FAQ if you do not know how to enable comments.
3. Paste the shortcode that you generated in step 1 and publish the page. You will have your brand new Ask Me Anything page. 

To Add The Form Into A Widget: 

1. Questions are saved as WordPress comments and all comments need to be associated with a page or a post. So you need to create a page or a post with the Ask Me Anything form as described in the previous steps. 
2. Once the Page is created, go to WordPress Admin > Settings > Ask Me Anything.
3. Select Location in 'Choose Location' as 'Sidebar Widget'. 
4. Enter the page ID of the Page or Post created for the Ask Me Anything form in 'Page / Post ID'.
5. Play with the rest of the options to customize the shortcode. Copy the generated shortcode.
6. Go to Appearance > Widgets and add a new Text widget to your sidebar. Paste the shortcode here and Save it. Your Ask Me Anything widget is ready. 

== Frequently Asked Questions ==

= Do I need to register for a third party service to use this plugin? =

No.

= Where can I see a live example before I install this plugin? =

You can see a [live example here on my travelogue](http://kuttappi.com/ask/). All plugin questions and test questions are deleted, please use the support forum for questions.

= Can I use this plugin without creating a dedicated Page or Post? =

No. Since the plugin saves questions and answers as WordPress comments, it needs to be associated with a page or post. However, once you have it on a page, you can add the form as a sidebar widget as well.

= How do I enable comments on a page? =

1. Edit the page or post and on the top right corner click on 'Screen Options'. 
2. In the drawer that drops down, check 'Discussion'. Close the drawer by clicking on 'Screen Options' again.
3. In the 'Discussion' meta box, check 'Allow comments'.
4. Publish / Update the page.

= My sidebar widget shows the shortcode instead of the AMA form =

Add this to the functions.php of your theme to enable shortcode rendering on sidebar widgets. 

`add_filter('widget_text','do_shortcode');`

= I get an 'Empty CAPTCHA' error when I sumbit a question. What do I do? =

This probably comes from a captcha plugin that adds it to the comment form. In Settings > Ask Me Anything set 'Additional Form Elements' to 'Show' along with all the other options you want. Then copy paste the new shortcode into your page / post. This will display the captcha and the user can fill it up at the time of submitting the question. 

Note: The AMA plugin does not come with its own captcha. 

= I need more features. Can I hire you? =

Yes. Please [get in touch via my contact form](http://millionclues.com/contact/) with a brief description of your requirement and budget for the project. I will be in touch shortly.

= I found this plugin very useful, how can I show my appreciation? =

I am glad to hear that! You can either [make a donation](http://millionclues.com/donate/) or leave a [rating](https://wordpress.org/support/plugin/ask-me-anything-anonymously/reviews/?rate=5#new-post) to motivate me to keep working on the plugin. 

== Screenshots ==

1. Ask Me Anything Shortcode Generator in Settings > Ask Me Anything.
2. Easily Stlye Your AMA Page With Custom CSS.
3. A Sample Ask Me Anything Page.
4. A Sidebar Widget Showing Recent Questions And Their Answers.

== Changelog ==

= 1.6 =
* Date: 04.October.2023
* Tested with WordPress 6.3.1
* Enhancement: Added compatibility with PHP 8.x

= 1.5 =
* Date: 27.January.2023.
* Tested with WordPress 6.1.1.
* Enhancement: !IMPORTANT! Main plugin file was renamed to meet WordPress standards. This will deactivate the plugin on update. Simply reactivate the plugin to fix the issue.
* Enhancement: Added new attribute `test_placeholder` to edit the placeholder text of the verification question answer box.
* Enhancement: Updated CSSTidy to 1.7.3.
* Security Fix: Deleted unwanted files in CSSTidy.
* I18n: Updated text domain from `abl_amamc_td` to `ask-me-anything-anonymously` as per WordPress [internationalization guidelines](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#text-domains).

= 1.4 =
* Date: 07.March.2021
* Tested with WordPress 5.7.
* Bug fix: Fixed PHP notice: Undefined index: amashowtestquestion amamc_ask-me-anything.php on line 820.

= 1.3.1 =
* Date: 23.October.2017
* Corrected words that were not translation ready.
* Generated and added .POT file for translations. 

= 1.3 =
* Date: 02.September.2017
* Updated: CSSTidy classes to meet PHP 7.x standards.
* Updated: Uses wp_kses() instead of esc_html() during output to prevent stripping of useful html tags.
* Updated: Custom CSS loads only on AMA pages. It used to load on all front end pages before. 
* Added: option to minify CSS. Related code uses functions introduced in PHP 5.5, if your PHP version is less than 5.5, you should upgrade first. How to check? [Read this thread](https://wordpress.org/support/topic/fatal-error-after-update-from-1-2-to-1-3/#post-9461535)
* Code improvements

= 1.2 =
* Options page moved from WordPress Admin > Tools > Ask Me Anything to WordPress Admin > Settings > Ask Me Anything.
* Added ability to add custom CSS.
* Added link to the showcase thread in readme.txt.
* Changed time-stamp from date to date and time in hours and minutes.

= 1.01 =
* New shortcode attribute: show_test_question to show (default) or hide the test question for spam check.
* New shortcode attribute: show_do_action to optionally show (hide by default) additional comment form elements added by other plugins like captcha plugins.

= 1.0 =
* First release of the plugin.

== Upgrade Notice ==

= 1.6 =
* Date: 04.October.2023
* Tested with WordPress 6.3.1
* Enhancement: Added compatibility with PHP 8.x

= 1.5 =
* Date: 27.January.2023.
* Tested with WordPress 6.1.1.
* Enhancement: !IMPORTANT! Main plugin file was renamed to meet WordPress standards. This will deactivate the plugin on update. Simply reactivate the plugin to fix the issue.
* Enhancement: Added new attribute `test_placeholder` to edit the placeholder text of the verification question answer box.
* Enhancement: Updated CSSTidy to 1.7.3.
* Security Fix: Deleted unwanted files in CSSTidy.
* I18n: Updated text domain from `abl_amamc_td` to `ask-me-anything-anonymously` as per WordPress [internationalization guidelines](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#text-domains).

= 1.4 =
* Date: 07.March.2021
* Tested with WordPress 5.7.
* Bug fix: Fixed PHP notice: Undefined index: amashowtestquestion amamc_ask-me-anything.php on line 820.

= 1.3.1 =
* Date: 23.October.2017
* Corrected words that were not translation ready.
* Generated and added .POT file for translations. 

= 1.3 =
* Date: 02.September.2017
* Updated: CSSTidy classes to meet PHP 7.x standards.
* Updated: Uses wp_kses() instead of esc_html() during output to prevent stripping of useful html tags.
* Updated: Custom CSS loads only on AMA pages. It used to load on all front end pages before. 
* Added: option to minify CSS. Related code uses functions introduced in PHP 5.5, if your PHP version is less than 5.5, you should upgrade first. How to check? [Read this thread](https://wordpress.org/support/topic/fatal-error-after-update-from-1-2-to-1-3/#post-9461535)
* Code improvements

= 1.2 =
* Add inbuilt custom CSS box. Tested with 4.7 and 4.7 Multisite.

= 1.01 =
* Added new shortcode attribute show_do_action. For compatibility with comment spam check captcha plugins set it to "true".

= 1.0 =
* First release of the plugin.