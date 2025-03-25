<?php
if (! defined('ABSPATH')) {
    exit;
}
add_action('admin_notices', 'autoreply_ai_activation_notice');
function autoreply_ai_activation_notice()
{
    if (get_option('autoreply_ai_activated')) {
        //update_option('autoreply_ai_activated', false);
        return; // If activated, do nothing
    }
?>
    <div class="notice notice-warning is-dismissible custom-notice-popup">
        <p><strong>AutoReply AI:</strong> Please activate the plugin to use it.
            <button id="open-ai-activation-modal" class="button button-primary">Activate Now</button>
        </p>
    </div>


    <div id="ai-activation-modal" class="ai-modal">
        <div class="ai-modal-content">
            <span class="close-modal" id="close-btn-popup">&times;</span>
            <h2>AutoReply AI Activation</h2>
            <?php wp_nonce_field('autoreply_activation_action', 'autoreply_activation_nonce'); ?>
            <label for="ai-name">Name:</label>
            <input type="text" id="ai-name" required>

            <label for="ai-email">Email:</label>
            <input type="email" id="ai-email" required>

            <label for="ai-api-key">OpenAI API Key:</label>
            <input type="text" id="ai-api-key" required>
            <a href="https://platform.openai.com/api-keys" target="_blank" class="gen-text">Generate API Key</a>

            <div class="terms-container">
                <input type="checkbox" id="ai-agree">
                <label for="ai-agree">I agree to the <a href="https://www.plugins.techbeeps.com/autoreply-ai-terms-and-conditions/">Terms & Conditions</a></label>
            </div>

            <button id="ai-activate" class="button-primary" disabled>Activate</button>
        </div>
    </div>
<?php
}
add_action('wp_ajax_autocomment_ai_activate', 'autoreply_ai_comment_ai_activate');
function autoreply_ai_comment_ai_activate()
{


    $nonce = isset($_POST['autoreply_activation_nonce']) ? sanitize_text_field(wp_unslash($_POST['autoreply_activation_nonce'])) : '';
    if (!isset($nonce) || !wp_verify_nonce($nonce, 'autoreply_activation_action')) {
        wp_send_json_error(['message' => 'Security check failed!']);
    }

    if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['api_key'])) {
        wp_send_json_error(['message' => 'Missing required fields']);
    }


    $name = sanitize_text_field(wp_unslash($_POST['name']));
    $email = sanitize_email(wp_unslash($_POST['email']));

    update_option('autoreply_ai_name', $name);
    update_option('autoreply_email', $email);
    $user_key = substr(hash('sha256', $email), 0, 16);
    update_option('autoreply_ai_api_key', openssl_encrypt(sanitize_text_field(wp_unslash($_POST['api_key'])), 'AES-256-CBC', $user_key, 0, $user_key));

    $website_url = get_site_url();

    $remote_url = "https://www.plugins.techbeeps.com/autoreply-ai/";
    $response = wp_remote_post($remote_url, [
        'body' => [
            'name'       => $name,
            'email'      => $email,
            'website'    => $website_url
        ],
        'timeout' => 120,
        'blocking' => true
    ]);

    if (is_wp_error($response)) {
        wp_send_json_error(['message' => 'Activation failed: Could not connect to server.']);
    }
    update_option('autoreply_ai_activated', true);
    wp_send_json_success(['message' => 'Activated successfully']);
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