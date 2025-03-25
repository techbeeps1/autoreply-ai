<?php
if (! defined('ABSPATH')) {
    exit;
}

function autoreply_ai_auto_reply_to_bbpress_comment($reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author, $false1, $reply_to)
{
    $selected_user = get_option('autoreply_ai_selected_user', '1');

    // Get the reply content
    $reply_text = bbp_get_reply_content($reply_id);
    $topic_text = bbp_get_reply_content($topic_id);
    // Generate the auto-reply message


    $auto_reply_text = autoreply_ai_get_ai_reply($reply_text, $topic_text);

    // Ensure it's not an automated reply (prevent loop)
    if ($reply_to != 0) {
        return;
    }
    if (get_post_meta($reply_id, '_auto_reply', true)) {
        return;
    }

    // Insert automatic reply as a second-level reply
    $auto_reply_id = bbp_insert_reply([
        'post_parent'    => $topic_id, // This ensures it's a second-level reply
        'post_content'   => $auto_reply_text,
        'post_author'    => $selected_user, // Change this to an admin ID if needed
        'post_status'    => bbp_get_public_status_id(),
        'post_type'      => bbp_get_reply_post_type(),
    ]);

    // Mark the reply as auto-generated
    if ($auto_reply_id) {

        update_post_meta($auto_reply_id, '_bbp_reply_to', $reply_id);
        update_post_meta($auto_reply_id, '_auto_reply', true);
    }
}
$selected_forum = get_option('autoreply_ai_selected_forum', '');
if ($selected_forum == "bbpress") {
    add_action('bbp_new_reply', 'autoreply_ai_auto_reply_to_bbpress_comment', 10, 7);
}