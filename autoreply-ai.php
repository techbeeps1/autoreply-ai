<?php
/*
Plugin Name: AutoReply AI
Plugin URI:  https://plugins.techbeeps.com/
Description: A plugin that enables automatic replies using AI, allowing admins to configure API keys, word limits, and user selection.
Version:     1.2
Author:      Techbeeps
Author URI:  https://techbeeps.co.in/
Text Domain: autoreply-ai
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (! defined('ABSPATH')) { exit; }

define('AUTO_REPLY_AI_TBS', '1.2');
define('AUTO_REPLY_AI_TBS_PATH', plugin_dir_path(__FILE__));
define('AUTO_REPLY_AI_TBS_URL',  __FILE__);


require_once AUTO_REPLY_AI_TBS_PATH . 'admin.php';
require_once AUTO_REPLY_AI_TBS_PATH . 'autoreply-comments.php';
require_once AUTO_REPLY_AI_TBS_PATH . 'custom-handle-ai-reply.php';
require_once AUTO_REPLY_AI_TBS_PATH . 'ai-generator.php';
require_once AUTO_REPLY_AI_TBS_PATH . 'includes/functions.php';
require_once AUTO_REPLY_AI_TBS_PATH . 'forum-autoreply/buddypress-auto-reply.php';
require_once AUTO_REPLY_AI_TBS_PATH . 'forum-autoreply/wpForo-Forum.php';
require_once AUTO_REPLY_AI_TBS_PATH . 'forum-autoreply/ForumWP.php';
require_once AUTO_REPLY_AI_TBS_PATH . 'forum-autoreply/Asgaros-Forum.php';
require_once AUTO_REPLY_AI_TBS_PATH . 'forum-autoreply/bbpress-auto-reply.php';