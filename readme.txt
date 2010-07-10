=== Simplr User Registration Form ===
Contributors: mpvanwinkle77
Donate link: http://www.mikevanwinkle.com/
Tags: registration, signup, wordpress 3.0, cms, users, user management
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 0.1.2

This plugin allows users to easily add a custom user registration form anywhere on their site using simple shortcode.

== Description ==

The goal of this plugin is to give developers and advanced Wordpress users a simple way to create role-specific registration forms for their wordpress website. For instance, you might be running an education based site in which you wanted both teachers and students to particape. This plugin enables you to create distinct registration forms for each type of registrant.

Because the focus is on seperating registrants, I have not focused on creating a highly customizable form like <a href="http://wordpress.org/extend/plugins/register-plus/" title="Register Plus">Register Plus</a>. 

To use this plugin simplr employ the shortcode <code>[Register]</code> on any Wordpress post or page. The default role is "subscriber". To apply another role to the registration simply use the the role parameter, for instance: <code>[Regsiter role="editor"]</code>. If you have created custom roles you may use them as well. 

You can also use shortcode so specify a custom confirmation message for each form: <br>

<code>[Register role="teacher" <b>message="Thank you for registering for my site. If you would like to encourage your students to register, please direct them to http://www.domain.com/students"</b>]</code>

Finally, you can specify emails to be notified when a new user is registered. By defualt site admins will receive notice but to notify other simply use the notify parameter:

<code>[Register role="teacher" message="Thank you for registering for my site. If you would like to encourage your students to register, please direct them to http://www.domain.com/students" <b>notify="email1@email.com,email2@email.com"</b>]</code>

== Installation ==

1. Download `simplr_reg_page.zip` and upload contents to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `[Register role="YOUROLE"]` in the page or post you would like to contain a registration form.

== Frequently Asked Questions ==

See plugin settings page for detailed instructions

== Screenshots ==



== Changelog ==

= 1.0 =
* Initial Version
=1.1=
-fixed stylesheet path
