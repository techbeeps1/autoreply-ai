=== AutoReply AI ===
Contributors: techbeeps, gurjeet6090  
Tags:  auto reply, auto comment reply, ai comment reply, comment AI bots, Automatically responds to comments using AI
Requires at least: 5.0  
Tested up to: 6.7  
Requires PHP: 7.4  
Stable tag: 1.2  
License: GPL-2.0+  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Auto-generate AI replies to user comments for better engagement. Supports WordPress, BuddyPress, wpForo, ForumWP, Asgaros, and bbPress.

== Description ==  
AutoReply AI is an intelligent WordPress plugin that automatically generates **human-like replies** to user comments, ensuring **context-aware** and **engaging interactions** on your website. It analyzes comments for **relevance, meaning, and context** before generating **smart responses**.  
With built-in **spam detection** and **abusive content filtering**, AutoReply AI ensures a safe and positive discussion environment. The plugin **automatically detects gibberish, promotional content, and offensive language**, preventing low-quality comments from affecting your website’s credibility.  

**Features:**  
– Auto-replies to comments using AI  
– Supports WordPress comments, BuddyPress, wpForo, ForumWP, Asgaros Forum, and bbPress  
– Customizable AI behavior  
– Secure API key storage  
– Lightweight and efficient  

== Installation ==  
1. Upload the plugin to the `/wp-content/plugins/` directory.  
2. Activate it through the 'Plugins' menu in WordPress.  
3. Navigate to the settings page and enter your OpenAI API key.  
4. Enjoy automatic AI-powered comment replies.  

== Frequently Asked Questions ==  
= How do I get an OpenAI API key? =  
You can obtain an API key by signing up on OpenAI's website.  

= Does this work with custom comment systems? =  
Yes, the plugin supports WordPress comments, BuddyPress, wpForo, ForumWP, Asgaros Forum, and bbPress.  

== Screenshots ==  
1. Example of AI-generated reply in a WordPress comment.  
2. Plugin settings page for API configuration.  

== Changelog ==  
= 1.0 =  
* Initial release with AI-powered automatic comment replies.  
* Added support for BuddyPress, wpForo, ForumWP, Asgaros Forum, and bbPress.  

= 1.1 =
* Added External Services section.
* Updated API integration details.
* Prefixed all functions to prevent conflicts.
* Improved security by preventing direct file access.
* Code optimization and minor bug fixes.

= 1.2 =
* Removed pop-up activation form
* Removed newsletter form
* Added API key protection and encryption message

== Upgrade Notice ==  
= 1.2 =  
This update removes the pop-up activation form and newsletter form while adding a message confirming that API keys are fully protected and encrypted.

== External Services ==  
This plugin connects to external services to generate AI-based replies.  

1. **OpenAI API** (https://api.openai.com/v1/chat/completions)  
   - **What it does:** Generates AI-based responses.  
   - **Data sent:** The user's message query and API key.  
   - **Terms of Service:** https://openai.com/terms  
   - **Privacy Policy:** https://openai.com/privacy  
