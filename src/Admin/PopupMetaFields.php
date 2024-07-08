<?php
namespace Xgenious\PopupBuilder\admin;

class PopupMetaFields {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_popup_meta_box'));
        add_action('save_post', array($this, 'save_popup_meta_box_data'));
    }

    public function add_popup_meta_box() {
        add_meta_box(
            'popup_settings',
            __('Popup Settings', 'xgenious-popup-builder'),
            array($this, 'render_popup_meta_box'),
            'xgenious_popup',
            'normal',
            'high'
        );
    }

    public function render_popup_meta_box($post) {
        wp_nonce_field('popup_meta_box', 'popup_meta_box_nonce');

        $delay = get_post_meta($post->ID, '_popup_delay', true);
        $display_on = get_post_meta($post->ID, '_popup_display_on', true);
        $specific_pages = get_post_meta($post->ID, '_popup_specific_pages', true);

        ?>
        <p>
            <label for="popup_delay"><?php _e('Delay (seconds):', 'xgenious-popup-builder'); ?></label>
            <input type="number" id="popup_delay" name="popup_delay" value="<?php echo esc_attr($delay); ?>" min="0">
        </p>
        <p>
            <label for="popup_display_on"><?php _e('Display on:', 'xgenious-popup-builder'); ?></label>
            <select id="popup_display_on" name="popup_display_on">
                <option value="all" <?php selected($display_on, 'all'); ?>><?php _e('All Pages', 'xgenious-popup-builder'); ?></option>
                <option value="specific" <?php selected($display_on, 'specific'); ?>><?php _e('Specific Pages', 'xgenious-popup-builder'); ?></option>
            </select>
        </p>
        <div id="specific_pages_container" style="<?php echo $display_on === 'specific' ? 'display:block;' : 'display:none;'; ?>">
            <p>
                <label for="popup_specific_pages"><?php _e('Select Pages:', 'xgenious-popup-builder'); ?></label>
                <select id="popup_specific_pages" name="popup_specific_pages[]" multiple style="width: 100%; max-width: 400px;">
                    <?php
                    $pages = get_pages();
                    foreach ($pages as $page) {
                        $selected = in_array($page->ID, (array)$specific_pages) ? 'selected' : '';
                        echo '<option value="' . esc_attr($page->ID) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                    }
                    ?>
                </select>
            </p>
        </div>
        <script>
            jQuery(document).ready(function($) {
                $('#popup_display_on').on('change', function() {
                    if ($(this).val() === 'specific') {
                        $('#specific_pages_container').show();
                    } else {
                        $('#specific_pages_container').hide();
                    }
                });
            });
        </script>
        <?php
    }

    public function save_popup_meta_box_data($post_id) {
        if (!isset($_POST['popup_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['popup_meta_box_nonce'], 'popup_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $delay = isset($_POST['popup_delay']) ? sanitize_text_field($_POST['popup_delay']) : '';
        $display_on = isset($_POST['popup_display_on']) ? sanitize_text_field($_POST['popup_display_on']) : '';
        $specific_pages = isset($_POST['popup_specific_pages']) ? array_map('intval', $_POST['popup_specific_pages']) : array();

        update_post_meta($post_id, '_popup_delay', $delay);
        update_post_meta($post_id, '_popup_display_on', $display_on);
        update_post_meta($post_id, '_popup_specific_pages', $specific_pages);
    }
}