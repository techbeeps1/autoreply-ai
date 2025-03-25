<?php
if (! defined('ABSPATH')) {
    exit;
}


function autoreply_ai_js_plugin($hook)
{

    wp_enqueue_script(
        'autoreply-ai-admin-script',
        plugins_url('assets/js/auto_reply_ai.js', AUTO_REPLY_AI_TBS_URL),
        array('jquery'),
        filemtime(plugin_dir_path(AUTO_REPLY_AI_TBS_URL) . 'assets/js/auto_reply_ai.js'),
        true
    );
}

add_action('admin_enqueue_scripts', 'autoreply_ai_js_plugin');

function autoreply_ai_css_plugin()
{
    wp_enqueue_style(
        'autoreply-ai-admin-style',
        plugins_url('assets/css/style.css', AUTO_REPLY_AI_TBS_URL),
        array(),
        filemtime(plugin_dir_path(AUTO_REPLY_AI_TBS_URL) . 'assets/css/style.css')
    );
}
add_action('admin_enqueue_scripts', 'autoreply_ai_css_plugin');