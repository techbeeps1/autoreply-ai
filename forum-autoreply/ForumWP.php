<?php
if (! defined('ABSPATH')) {
    exit;
}
$selected_forum = get_option('autoreply_ai_selected_forum', '');
if ($selected_forum == "ForumWP") {
    // We are using ForumWP plugin hook. This hook runs when a new topic is created in ForumWP
    add_action('fmwp_reply_create_completed', 'autoreply_ai_forumwp_reply', 10, 1);
}
function autoreply_ai_forumwp_reply($reply_id)
{
    $selected_user = get_option('autoreply_ai_selected_user', '1');

    // Prevent auto-reply loop
    if (get_post_meta($reply_id, '_auto_reply', true)) {
        return;
    }
    // Get the reply object
    $reply = get_post($reply_id);
    if (!$reply) {
        return; // Exit if the reply doesn't exist
    }
    // first level reply only
    if ($reply->post_parent != 0) {
        return;
    }
    // Get the original reply content
    $reply_text = $reply->post_content;

    $topic_id = get_post_meta($reply_id, "fmwp_topic", true);
    $topic = get_post($topic_id);
    // Prepare the auto-reply content

    $auto_reply_text = autoreply_ai_get_ai_reply($reply_text, $topic->post_content);
    // Prepare the reply data
    $reply_data = array(
        'topic_id'   => $topic_id,
        'content'    => $auto_reply_text,
        'author_id'  => $selected_user,
        'post_parent' => $reply_id, // Set the parent as the original reply
    );

    $auto_reply_id = autoreply_ai_get_forumwp_create_reply($reply_data);

    if ($auto_reply_id) {
        update_post_meta($auto_reply_id, '_auto_reply', true);
    }
}
function autoreply_ai_get_forumwp_create_reply($data)
{
    $topic  = get_post($data['topic_id']);
    $author = ! empty($data['author_id']) ? $data['author_id'] : get_current_user_id();
    $orig_content = wp_kses_post($data['content']);
    $post_content = wpautop($orig_content);
    $fmwp_forum =   get_post_meta($data['post_parent'], 'fmwp_forum', true);

    $args = array(
        'post_type'    => 'fmwp_reply',
        'post_status'  => 'publish',
        'post_title'   => 'Reply To: ' . $topic->post_title,
        'post_content' => $post_content,
        'post_author'  => $author,
        'post_parent'  => isset($data['post_parent']) ? $data['post_parent'] : 0,
        'meta_input'   => array(
            'fmwp_original_content' => $orig_content,
            'fmwp_forum'            => $fmwp_forum,
            'fmwp_topic'            => $data['topic_id'],
        ),
    );

    $args = apply_filters('fmwp_create_reply_args', $args, $data);

    $reply_id = wp_insert_post($args);
    if (! is_wp_error($reply_id)) {
        do_action('fmwp_reply_create_completed', $reply_id);  // this is ForumWP plugin hook
    }
    return $reply_id;
}