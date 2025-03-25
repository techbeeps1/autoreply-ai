<?php
if (! defined('ABSPATH')) {
    exit;
}
function autoreply_ai_buddypress_comment($comment_id, $params)
{
    $selected_user = get_option('autoreply_ai_selected_user', '1');

    $reply_text = $params['content'];
    $original_post = bp_activity_get_specific([
        'activity_ids' => $parent_activity_id
    ]);

    if (!empty($original_post['activities'][0])) {
        $original_post_text = $original_post['activities'][0]->content;
    } else {
        $original_post_text = 'Original post not found.';
    }
    $auto_reply_text = autoreply_ai_get_ai_reply($reply_text, $original_post_text);
    $parent_activity_id = $params['activity_id'];
    if ($parent_activity_id != $params['parent_id']) {
        return;
    }
    // Ensure it's not an automated reply (prevent loop)
    if (get_post_meta($comment_id, '_auto_reply', true)) {
        return;
    }

    // Insert automatic reply
    $auto_reply_id = bp_activity_add([
        'content'        => $auto_reply_text,
        'component'      => 'activity',
        'type'           => 'activity_comment',
        'user_id'        => $selected_user, // Change this to an admin ID if needed
        'item_id'        => $parent_activity_id,
        'secondary_item_id' => $comment_id,
    ]);

    // Mark as auto-replied
    if ($auto_reply_id) {
        update_post_meta($auto_reply_id, '_auto_reply', true);
    }
}
$selected_forum = get_option('autoreply_ai_selected_forum', '');
if ($selected_forum == "buddypress") {
    add_action('bp_activity_comment_posted', 'autoreply_ai_buddypress_comment', 10, 2);
}