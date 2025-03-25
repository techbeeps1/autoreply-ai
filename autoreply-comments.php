<?php
if (! defined('ABSPATH')) {
    exit;
}
$auto_reply = get_option('autoreply_ai_auto_reply');

if ($auto_reply == 1) {
    if (get_option('autoreply_ai_activated')) {
        add_action('comment_post', 'autoreply_ai_auto_comment', 10, 2);
        function autoreply_ai_auto_comment($comment_id, $comment_approved)
        {
            $selected_user = get_option('autoreply_ai_selected_user');
            $selected_comment_basedr = get_option('autoreply_ai_selected_comment_based', 'content');
            $user = get_userdata($selected_user);


            if (1 === $comment_approved) {
                // Get the comment data
                $comment = get_comment($comment_id);

                $post_id = $comment->comment_post_ID;
                $post = get_post($post_id);

                // Get the post content and comment content
                $post_content = $selected_comment_basedr == 'content' ? $post->post_content : $post->post_title;

                // Ensure it's not the comment itself that is replying
                if ($comment->comment_parent == 0) {
                    // Get the content of the comment
                    $comment_content = $comment->comment_content;

                    // Call AI API to generate a reply
                    $ai_reply = autoreply_ai_get_ai_reply($comment_content, $post_content);

                    // Formulate the reply data
                    $data = array(
                        'comment_post_ID' => $comment->comment_post_ID,
                        'comment_content' => $ai_reply,
                        'comment_author' => $user->display_name ?? '', // Set your name or the admin name
                        'comment_author_email' => $user->user_email ?? '', // Set your admin email
                        'comment_parent' => $comment_id,
                        'comment_approved' => 1,
                        'user_id' => $selected_user // Replace with the admin user ID
                    );

                    // Insert the AI-generated reply
                    if ($ai_reply != false) {
                        wp_insert_comment($data);
                    } else {
                        wp_set_comment_status($comment_id, 'hold');
                    }
                }
            }
        }
    }
}