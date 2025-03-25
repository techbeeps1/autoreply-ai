<?php
if (! defined('ABSPATH')) {
    exit;
}
if (get_option('autoreply_ai_activated')) {
    function autoreply_ai_custom_comment_button($actions, $comment)
    {
        // Generate a nonce for security
        $nonce = wp_create_nonce('reply_with_ai_nonce');

        $custom_button = '<a href="' . esc_url(admin_url("edit-comments.php?id={$comment->comment_ID}&reply-with-ai=1&_wpnonce={$nonce}")) . '" class="reply-with-ai" data-comment-id="' . esc_attr($comment->comment_ID) . '">Reply with AI</a>';

        $actions['custom_action'] = $custom_button;
        return $actions;
    }
    add_filter('comment_row_actions', 'autoreply_ai_custom_comment_button', 10, 2);


    function autoreply_ai_handle_ai_reply_action()
    {
        // Check if the 'reply-with-ai' parameter exists in the URL

        if (isset($_REQUEST['reply-with-ai']) && isset($_REQUEST['id'])) {
            $comment_id = intval(sanitize_text_field(wp_unslash($_REQUEST['id'])));
            $nonce = isset($_REQUEST['_wpnonce']) ? sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])) : '';
            if (!isset($nonce) || !wp_verify_nonce($nonce, 'reply_with_ai_nonce')) {
                wp_die(esc_html('Nonce verification failed!'));
            }


            // Ensure the comment exists
            $comment = get_comment($comment_id);

            $selected_user = get_option('autoreply_ai_selected_user');
            $selected_comment_basedr = get_option('autoreply_ai_selected_comment_based', 'content');
            $user = get_userdata($selected_user);

            $post_id = $comment->comment_post_ID;
            $post = get_post($post_id);

            // Get the post content and comment content

            $post_content = $selected_comment_basedr == 'content' ? $post->post_content : $post->post_title;
            // Ensure it's not the comment itself that is replying
            if (true) {
                // Get the content of the comment
                $comment_content = $comment->comment_content;

                if ($comment->comment_approved == '1') {
                    // Call AI API to generate a reply
                    $ai_reply = autoreply_ai_get_ai_reply($comment_content, $post_content, true);

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
                    if ($ai_reply != 'false') {
                        wp_insert_comment($data);
                    }
                }
            }

            // Redirect back to comments page to avoid duplicate action on refresh

            wp_redirect(admin_url('edit-comments.php?reply_with_ai_done=1&_wpnonce=' . $nonce));
            exit;
        }
    }
    add_action('admin_init', 'autoreply_ai_handle_ai_reply_action');


    function autoreply_ai_add_custom_bulk_action($bulk_actions)
    {
        $bulk_actions['reply_with_ai'] = 'Reply with AI';
        return $bulk_actions;
    }
    add_filter('bulk_actions-edit-comments', 'autoreply_ai_add_custom_bulk_action');




    function autoreply_ai_handle_bulk_ai_reply($redirect_to, $doaction, $comment_ids)
    {
        if ($doaction !== 'reply_with_ai') {
            return $redirect_to;
        }
        $selected_user = get_option('autoreply_ai_selected_user');
        $selected_comment_basedr = get_option('autoreply_ai_selected_comment_based', 'content');
        $user = get_userdata($selected_user);

        foreach ($comment_ids as $comment_id) {
            $comment = get_comment($comment_id);
            if (!$comment) {
                continue;
            }

            $post_id = $comment->comment_post_ID;
            $post = get_post($post_id);

            // Get the post content and comment content
            $post_content = $selected_comment_basedr == 'content' ? $post->post_content : $post->post_title;

            // Ensure it's not the comment itself that is replying
            if (true) {
                // Get the content of the comment
                $comment_content = $comment->comment_content;
                if ($comment->comment_approved == '1') {
                    // Call AI API to generate a reply
                    $ai_reply = autoreply_ai_get_ai_reply($comment_content, $post_content, true);

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
                    if ($ai_reply != 'false') {
                        wp_insert_comment($data);
                    }
                }
            }
        }

        // Redirect back with a success message

        $redirect_to = add_query_arg(
            [
                'reply_with_ai_done' => count($comment_ids),
                '_wpnonce' => wp_create_nonce('reply_with_ai_nonce')
            ],
            $redirect_to
        );

        return $redirect_to;
    }
    add_filter('handle_bulk_actions-edit-comments', 'autoreply_ai_handle_bulk_ai_reply', 10, 3);


    function autoreply_ai_custom_admin_notice()
    {
        if (!empty($_GET['reply_with_ai_done'])) {
            $nonce = isset($_REQUEST['_wpnonce']) ? sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])) : '';
            if (!isset($nonce) || !wp_verify_nonce($nonce, 'reply_with_ai_nonce')) {
                wp_die(esc_html('Nonce verification failed!'));
            }
            $count = intval(sanitize_text_field(wp_unslash(isset($_REQUEST['reply_with_ai_done']) ? $_REQUEST['reply_with_ai_done'] : '')));
            echo '<div class="updated notice is-dismissible"><p>' . esc_html($count) . ' comments replied with AI.</p></div>';
        }
        if (isset($_REQUEST['reply_with_ai_error']) && !empty(sanitize_text_field(wp_unslash($_REQUEST['reply_with_ai_error'])))) {
            $count = sanitize_text_field(wp_unslash($_REQUEST['reply_with_ai_error']));
            echo '<div class="error notice is-dismissible"><p>' . esc_html($count) . '</p></div>';
        }
    }
    add_action('admin_notices', 'autoreply_ai_custom_admin_notice');
}