=== Forum Beginner Posts ===
Contributors: fliz, kona
Tags: bbpress, visual editor, visual, tinymce, teenymce, teeny, wysiwyg, paste, paste as text, paste-as-text, paste error, bad paste
Requires at least: 3.9
Tested up to: 4.8
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enables the Visual Editor for bbPress posts, in paste-as-text mode by default. Optionally enables extended Visual Editor buttons.

== Description ==

This plugin enables the Visual Editor pane in bbPress, allowing novice or non-technical users to create and format forum posts using a familiar, word-processor-like set of tool buttons. 

However, novice users may not know that text copied from applications such as word processors, web browsers and email clients can contain a lot of hidden mark-up and styling.  Pasting such text into the Visual Editor can lead to published posts that are garbled with mark-up code or which break the overall page styling or layout.

To avoid such problems, this plugin defaults to paste-as-text mode for Visual Editor forum post editing.  Paste-as-text mode attempts to strip all mark-up and styling from pasted text before it is inserted into the Visual Editor.  

If you don't want to enforce paste-as-text mode, it can be disabled in the plugin settings.

Extended Visual Editor buttons can be enabled in the plugin settings, but may not work nicely with some plugin and theme configurations.

*Note:*

Even if paste-as-text is forced, links in pasted text may show up as embedded content (videos, images, external Wordpress posts etc.) in the displayed forum posts.

To disable this embedded content and show ordinary link text, please switch "Auto-embed links" OFF in the BBPress Forums settings.

== Installation ==

1. Install this plugin via the WordPress plugin control panel, or by manually downloading it and uploading the extracted folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. To change configurable settings, visit the plugin settings page via the 
admin menu (Settings/Forum Beginner Posts.)

== Frequently Asked Questions ==

= The plugin is active but I'm not seeing either the Visual or the Text tab when creating forum posts - what should I do? =

Make sure that 'Post Formatting' is switched on in bbPress.  You can find it under Forum Features on the bbPress Forums Settings page.  See screenshot 3 for details.

= I'm seeing an admin error message saying "Please enable 'Post Formatting'..." - what should I do? =

As above.

= Does this plugin affect the behaviour of the Visual Editor when editing ordinary WordPress posts and pages? =

No. It only affects the behaviour of the Visual Editor in bbPress.

== Screenshots ==

1. Default bbPress post creation without Visual Editor.

2. Post creation with Visual Editor enabled by Forum Beginner Posts.

3. Where to enable "Post Formatting" in bbPress options.

4. Admin preferences pane for this plugin.

== Changelog ==

= 1.1.0 =
* Added options to disable paste-as-text and enable extra TinyMCE buttons.

* Added admin preferences screen to manage new options.

* Moved most plugin initialisation from __construct() to plugin_init() method,
to ensure dependency checks occur after all plugins are loaded.

= 1.0.0 =
* Initial version

== Upgrade Notice ==

= 1.0.0 =
Initial version.
