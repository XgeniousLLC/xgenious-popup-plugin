<?php
namespace Xgenious\PopupBuilder\Includes;

class Display {
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'display_popup'));
    }

    public function enqueue_styles() {
        wp_enqueue_style('xgenious-popup-public', XGENIOUS_POPUP_URL . 'public/css/popup-public.css', array(), XGENIOUS_POPUP_VERSION, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script('xgenious-popup-public', XGENIOUS_POPUP_URL . 'public/js/popup-public.js', array('jquery'), XGENIOUS_POPUP_VERSION, true);
        wp_localize_script('xgenious-popup-public', 'xgenious_popup', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('xgenious_popup_nonce')
        ));
    }

    public function display_popup() {
        $popups = $this->get_active_popups();

        foreach ($popups as $popup) {
            $this->render_popup($popup);
        }
    }

    private function get_active_popups() {
        $args = array(
            'post_type' => 'xgenious_popup',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_xgenious_popup_active',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );

        $popup_query = new \WP_Query($args);
        return $popup_query->posts;
    }

    private function render_popup($popup) {
        $popup_id = $popup->ID;
        $content = $this->get_popup_content($popup_id);
        $settings = $this->get_popup_settings($popup_id);

        if ($this->should_display_popup($settings)) {
            ?>
            <div id="xgenious-popup-<?php echo esc_attr($popup_id); ?>" class="xgenious-popup" data-popup-id="<?php echo esc_attr($popup_id); ?>" style="display:none;">
                <div class="xgenious-popup-overlay"></div>
                <div class="xgenious-popup-content">
                    <button class="xgenious-popup-close">&times;</button>
                    <?php echo $content; ?>
                </div>
            </div>
            <script>
                jQuery(document).ready(function($) {
                    var popupId = '<?php echo esc_js($popup_id); ?>';
                    var delay = <?php echo esc_js($settings['delay']); ?>;
                    setTimeout(function() {
                        $('#xgenious-popup-' + popupId).show();
                    }, delay * 1000);
                });
            </script>
            <?php
        }
    }

    private function get_popup_content($popup_id) {
        $popup = get_post($popup_id);
        return do_shortcode($popup->post_content);
    }

    private function get_popup_settings($popup_id) {
        $defaults = array(
            'delay' => 0,
            'display_on' => 'all',
            'specific_pages' => array(),
            'cookie_duration' => 7
        );

        $settings = get_post_meta($popup_id, '_xgenious_popup_settings', true);
        return wp_parse_args($settings, $defaults);
    }

    private function should_display_popup($settings) {
        // Check if popup has been closed recently
        if (isset($_COOKIE['xgenious_popup_closed'])) {
            return false;
        }

        // Check display conditions
        if ($settings['display_on'] === 'all') {
            return true;
        } elseif ($settings['display_on'] === 'specific' && is_singular()) {
            $current_page_id = get_the_ID();
            return in_array($current_page_id, $settings['specific_pages']);
        }

        return false;
    }
}