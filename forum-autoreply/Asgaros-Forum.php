<?php
if (! defined('ABSPATH')) {
    exit;
}

function autoreply_ai_log_asgarosforum_submission($post_id, $topic_id, $subject, $content, $link, $author_id)
{
    global $wpdb;
    if (preg_match('/<blockquote\b[^>]*>/', $content)) {
        return;
    }

    $selected_user = get_option('autoreply_ai_selected_user', '1');
    $table_name = $wpdb->prefix . "forum_posts";

    $topic_content = $wpdb->get_row($wpdb->prepare("SELECT `text` FROM `$table_name` WHERE parent_id = %d ORDER BY date ASC LIMIT 1", $topic_id), ARRAY_A);

    $topic_content = $topic_content['text'];
    $author_info = get_userdata($author_id);
    $uploads = array();
    $date = current_time('mysql');

    $auto_reply_text = autoreply_ai_get_ai_reply($content, $topic_content);

    $msg = ' <blockquote><a class="profile-link highlight-admin" href="' . $link . '"> Quote from ' . $author_info->display_name . '<div class="quotetitle"></div></a>
' . $content . '</blockquote>' . $auto_reply_text;

    $data = array(
        'text'      => $msg,
        'parent_id' => $topic_id,
        'forum_id'  => 1,
        'date'      => $date,
        'author_id' => $selected_user,
        'uploads'   => maybe_serialize($uploads),
    );

    // Insert data
    $wpdb->insert(
        $wpdb->prefix . 'forum_posts', // Table name with prefix
        array(
            'text'      => $msg,
            'parent_id' => $topic_id,
            'forum_id'  => 1,
            'date'      => $date,
            'author_id' => $selected_user,
            'uploads'   => maybe_serialize($uploads),
        ),
        array('%s', '%d', '%d', '%s', '%d', '%s') // Data format
    );
}
// Hook for both post and topic submissions

$selected_forum = get_option('autoreply_ai_selected_forum', '');
if ($selected_forum == "Asgaros Forum") {
    add_action('asgarosforum_after_add_post_submit', 'autoreply_ai_log_asgarosforum_submission', 10, 6);
}