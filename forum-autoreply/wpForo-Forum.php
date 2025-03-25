<?php
if (! defined('ABSPATH')) {
    exit;
}
function autoreply_ai_wpforo_post($post)
{
    if (!isset($post['topicid']) || !isset($post['postid'])) {
        return;
    }
    $selected_user = get_option('autoreply_ai_selected_user', '1');
    $topic_id = $post['topicid'];
    $reply_id = $post['postid'];

    // Get parent post
    $parent_post = WPF()->post->get_post($reply_id);

    // Ensure it's a first-level reply (direct reply to topic, not a reply to another reply)
    if ($parent_post['parentid'] != 0) {
        return; // Exit if it's a nested reply
    }
    // Prevent duplicate auto-replies
    if (get_post_meta($reply_id, '_auto_reply', true)) {
        return;
    }

    // Get the reply text
    $reply_post = WPF()->post->get_post($reply_id);
    $reply_text = isset($reply_post['body']) ? $reply_post['body'] : '';

    $topic = WPF()->topic->get_topic($topic_id);
    $first_post_id = $topic['first_postid']; // Get the first post ID
    $first_post = WPF()->post->get_post($first_post_id); // Retrieve the post content

    $auto_reply_text = autoreply_ai_get_ai_reply($reply_text, $first_post['body']);
    // Insert the auto-reply
    $auto_reply_id = WPF()->post->add([
        'topicid'  => $topic_id,
        'parentid' => $reply_id,
        'userid'   => $selected_user, // Change this to admin ID if needed
        'body'     => $auto_reply_text,
        'status'   => 0,
    ]);

    // Mark topic as auto-replied
    if ($auto_reply_id) {
        update_post_meta($auto_reply_id, '_auto_reply', true);
    }
}
$selected_forum = get_option('autoreply_ai_selected_forum', '');
if ($selected_forum == "wpForo") {
    add_action('wpforo_after_add_post', 'autoreply_ai_wpforo_post', 10, 1);
}