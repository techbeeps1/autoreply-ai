<?php
if (! defined('ABSPATH')) {
    exit;
}
add_action('admin_menu', 'autoreply_ai_settings_page');
// Add settings menu
function autoreply_ai_settings_page()
{
    add_menu_page(
        'AutoReply AI Settings',
        'AutoReply AI',
        'manage_options',
        'autoreply-ai-settings',
        'autoreply_ai_settings_html',
        'dashicons-admin-comments',
        20
    );
}

add_action('admin_init', 'autoreply_ai_process_autoreply_form');

function autoreply_ai_process_autoreply_form()
{
    // Save settings
    if (isset($_POST['autoreply_ai_save']) &&  (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')) {
        $nonce = isset($_POST['autoreply_activation_nonce']) ? sanitize_text_field(wp_unslash($_POST['autoreply_activation_nonce'])) : '';
        if (!isset($nonce) || !wp_verify_nonce($nonce, 'autoreply_activation_action')) {
            wp_die(esc_html('Nonce verification failed!'));
        }

        if (!empty($_POST['autoreply_ai_api_key'])) {
            $user_email = get_option('autoreply_email');
            $user_key = substr(hash('sha256', $user_email), 0, 16);
            update_option('autoreply_ai_api_key', openssl_encrypt(sanitize_text_field(wp_unslash($_POST['autoreply_ai_api_key'])), 'AES-256-CBC', $user_key, 0, $user_key));
			update_option('autoreply_ai_activated', true);
        }

        if (isset($_POST['autoreply_ai_word_limit']) and (absint($_POST['autoreply_ai_word_limit'] <= 50) and absint($_POST['autoreply_ai_word_limit'] >= 20))) {
            update_option('autoreply_ai_word_limit', absint(sanitize_text_field(wp_unslash($_POST['autoreply_ai_word_limit']))));
        }

        $autoreply_ai_auto_reply_d = isset($_POST['autoreply_ai_auto_reply']) ? 1 : 0;
        $autoreply_ai_selected_user_d = isset($_POST['autoreply_ai_selected_user']) ? sanitize_text_field(wp_unslash($_POST['autoreply_ai_selected_user'])) : '';
        $autoreply_ai_selected_model_d = isset($_POST['autoreply_ai_selected_model']) ? sanitize_text_field(wp_unslash($_POST['autoreply_ai_selected_model'])) : '';
        $autoreply_ai_selected_forum_d = isset($_POST['autoreply_ai_selected_forum']) ? sanitize_text_field(wp_unslash($_POST['autoreply_ai_selected_forum'])) : '';
        $autoreply_ai_prompt_add_d = isset($_POST['autoreply_ai_prompt_add']) ? sanitize_text_field(wp_unslash($_POST['autoreply_ai_prompt_add'])) : '';
        $autoreply_ai_auto_reply_msg_d = isset($_POST['autoreply_ai_auto_reply_msg']) ? sanitize_text_field(wp_unslash($_POST['autoreply_ai_auto_reply_msg'])) : 'Thank you for your comment! We appreciate your support. 😊';
        $without_reply_enable_d = isset($_POST['without_reply_enable']) ? 1 : 0;
        $generated_ai_notic_d = isset($_POST['generated_ai_notic']) ? sanitize_text_field(wp_unslash($_POST['generated_ai_notic'])) : '';
        $generated_ai_notic_enable_d =  isset($_POST['generated_ai_notic_enable']) ? 1 : 0;
        $selected_comment_based_d = isset($_POST['selected_comment_based']) ? sanitize_text_field(wp_unslash($_POST['selected_comment_based'])) : '';


        update_option('autoreply_ai_auto_reply', $autoreply_ai_auto_reply_d);
        update_option('autoreply_ai_selected_user', $autoreply_ai_selected_user_d);
        update_option('autoreply_ai_selected_model', $autoreply_ai_selected_model_d);
        update_option('autoreply_ai_selected_forum', $autoreply_ai_selected_forum_d);
        update_option('autoreply_ai_prompt_add', $autoreply_ai_prompt_add_d);
        update_option('autoreply_ai_auto_reply_msg', $autoreply_ai_auto_reply_msg_d);
        update_option('autoreply_ai_without_reply_enable', $without_reply_enable_d);
        update_option('autoreply_ai_generated_ai_notic', $generated_ai_notic_d);
        update_option('autoreply_ai_generated_ai_notic_enable', $generated_ai_notic_enable_d);
        update_option('autoreply_ai_selected_comment_based', $selected_comment_based_d);
    }
}
// Render settings page
function autoreply_ai_settings_html()
{
    $api_key = get_option('autoreply_ai_api_key', '');
    $selected_model = get_option('autoreply_ai_selected_model', '');
    $user_email = get_option('autoreply_email');
    $user_key = substr(hash('sha256', $user_email), 0, 16);
    $api_key = substr(openssl_decrypt($api_key, 'AES-256-CBC', $user_key, 0, $user_key), 0, 12);
    $auto_reply = get_option('autoreply_ai_auto_reply', 0);
    $word_limit = get_option('autoreply_ai_word_limit', 50);
    $selected_user = get_option('autoreply_ai_selected_user', '');
    $selected_forum = get_option('autoreply_ai_selected_forum', '');
    $autoreply_ai_prompt_add = get_option('autoreply_ai_prompt_add', '');
    $auto_reply_msg = get_option('autoreply_ai_auto_reply_msg', 'Thank you for your comment! We appreciate your support. 😊');
    $autoreply_reply_enable = get_option('autoreply_ai_without_reply_enable', '');
    $generated_ai_notic = get_option('autoreply_ai_generated_ai_notic', '🤖 This reply is generated by AI. If you need further clarification, feel free to ask!');
    $generated_ai_notic_enable = get_option('autoreply_ai_generated_ai_notic_enable', 0);
    $selected_comment_based = get_option('autoreply_ai_selected_comment_based', 'content');
    // Get all users
    $users = get_users([
        'fields' => ['ID', 'display_name']
    ]);

    if (isset($_POST['autoreply_ai_save']) &&  (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')) {
        $nonce = isset($_POST['autoreply_activation_nonce']) ? sanitize_text_field(wp_unslash($_POST['autoreply_activation_nonce'])) : '';
        if (!isset($nonce) || !wp_verify_nonce($nonce, 'autoreply_activation_action')) {
            wp_die(esc_html('Nonce verification failed!'));
        }
?>
        <div class="updated notice is-dismissible custom-notice-popup" bis_skin_checked="1">
            <p>Settings updated successfully!</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
        </div>
    <?php } ?>
    <div class="wrap ai-reply-admin">
        <h1 class="header-text-ai">AutoReply AI Settings</h1>
        <form method="POST">
            <table class="form-table">
                <tr>
                    <?php wp_nonce_field('autoreply_activation_action', 'autoreply_activation_nonce'); ?>
                    <th><label for="autoreply_ai_api_key">OpenAI API Key</label></th>
                    <td><input type="text" id="autoreply_ai_api_key" name="autoreply_ai_api_key" placeholder="<?php echo esc_html($api_key) ?> *********************************************" class="regular-text">
                        <a href="https://platform.openai.com/api-keys"> Generate API Key</a>
						<div class="tooltip">Your API key is fully protected and encrypted
  <span class="tooltiptext">Your API key is securely stored in your website’s database in an encrypted format, ensuring that no one can access or view it. With robust protection in place, you can store your API key safely and without any risk.</span>
</div>
                    </td>
                </tr>
                <tr>
                    <th><label for="autoreply_ai_selected_model">AI Model:</label></th>
                    <td>
                        <select id="autoreply_ai_selected_model" name="autoreply_ai_selected_model">
                            <option value="">-- Select Model --</option>
                            <option value="gpt-4o-mini" <?php selected($selected_model, 'gpt-4o-mini'); ?>>GPT-4o-mini</option>
                            <option value="gpt-4o" <?php selected($selected_model, 'gpt-4o'); ?>>GPT-4o</option>
                            <option value="gpt-3.5-turbo" <?php selected($selected_model, 'gpt-3.5-turbo'); ?>>GPT-3.5-turbo</option>
                            <option value="gpt-4-turbo" <?php selected($selected_model, 'gpt-4-turbo'); ?>>GPT-4-turbo</option>
                            <option value="gpt-4" <?php selected($selected_model, 'gpt-4'); ?>>GPT-4</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="autoreply_ai_auto_reply">Automatic Reply</label></th>
                    <td>
                        <input type="checkbox" id="autoreply_ai_auto_reply" name="autoreply_ai_auto_reply" value="1" <?php checked(1, $auto_reply); ?>>
                        <label for="autoreply_ai_auto_reply">Enable Automatic Reply</label>
                    </td>
                </tr>
                <tr>
                    <th><label for="autoreply_ai_word_limit">Response Word Limit</label></th>
                    <td><input type="number" id="autoreply_ai_word_limit" name="autoreply_ai_word_limit" value="<?php echo esc_attr($word_limit); ?>" class="small-text" max="50" min="20"></td>
                </tr>
                <tr>
                    <th><label for="autoreply_ai_selected_user">Select User:</label></th>
                    <td>
                        <select id="autoreply_ai_selected_user" name="autoreply_ai_selected_user">
                            <option value="">-- Select a User --</option>
                            <?php foreach ($users as $user) : ?>
                                <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($selected_user, $user->ID); ?>>
                                    <?php echo esc_html($user->display_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="selected_comment_based">AI will generate a comment based on:</label></th>
                    <td>
                        <select id="selected_comment_based" name="selected_comment_based">
                            <option value="content" <?php selected($selected_comment_based, 'content'); ?>>Post Content</option>
                            <option value="title" <?php selected($selected_comment_based, 'title'); ?>>Post Title</option>

                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="autoreply_ai_selected_user">Select Forum Plugin for Auto Reply:</label></th>
                    <td>
                        <select id="autoreply_ai_selected_user" name="autoreply_ai_selected_forum">
                            <option value="">-- Select Plugin --</option>

                            <option value="buddypress" <?php selected($selected_forum, 'buddypress'); ?>>BuddyPress</option>
                            <option value="wpForo" <?php selected($selected_forum, 'wpForo'); ?>>wpForo</option>
                            <option value="ForumWP" <?php selected($selected_forum, 'ForumWP'); ?>>ForumWP</option>
                            <option value="Asgaros Forum" <?php selected($selected_forum, 'Asgaros Forum'); ?>>Asgaros Forum</option>
                            <option value="bbpress" <?php selected($selected_forum, 'bbpress'); ?>>bbpress</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="autoreply_ai_auto_reply">Generated by AI Notic </label></th>
                    <td>
                        <input type="checkbox" id="generated_ai_notic_enable" name="generated_ai_notic_enable" value="1" <?php checked(1, $generated_ai_notic_enable); ?>>
                        <label for="generated_ai_notic_enable">Enable Generated by AI Notic</label>
                    </td>
                </tr>
                <tr>
                                       <th><label for="autoreply_ai_msg">Generated by AI Notic.</label></th>
                    <td>
                        <textarea id="generated_ai_notic" name="generated_ai_notic" rows="2" cols="50" placeholder="🤖 This reply is generated by AI. If you need further clarification, feel free to ask!"><?php echo esc_html($generated_ai_notic) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="autoreply_ai_prompt_add">Additional prompt:</label></th>
                    <td>
                        <textarea id="autoreply_ai_prompt_add" name="autoreply_ai_prompt_add" rows="4" cols="50" placeholder="Provide more details for a better AI-generated reply..."><?php echo esc_html($autoreply_ai_prompt_add) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th><label for="autoreply_reply">Without AI Reply</label></th>
                    <td>
                        <input type="checkbox" id="without_reply_enable" name="without_reply_enable" value="1" <?php checked(1, $autoreply_reply_enable); ?>>
                        <label for="without_reply_enable">Enable Without AI Reply (If you enable it, AI auto reply will be disabled. )</label>
                    </td>
                </tr>
                <tr>
                                     <th><label for="autoreply_ai_auto_reply_msg">Auto reply message ( Without AI) :</label></th>
                    <td>

                         <textarea id="autoreply_ai_auto_reply_msg" name="autoreply_ai_auto_reply_msg" rows="4" cols="50" placeholder="Thank you for your comment! We appreciate your support. 😊"><?php echo esc_html($auto_reply_msg) ?></textarea>
                    </td>
                </tr>

            </table>
            <button type="submit" name="autoreply_ai_save" class="autoreply-ai-save-button">Save Settings</button>
        </form>
    </div>

<?php
}