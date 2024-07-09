<?php

namespace Xgenious\PopupBuilder\Admin;

class Admin {
    private $analytics_page;

    public function __construct() {
        new PopupPostType();
        new PopupMetaFields();
        $this->analytics_page = new AnalyticsPage();

        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function enqueue_styles($hook) {
        // Check if we're on the Xgenious Popup settings page or custom post type page
        if ($this->is_xgenious_popup_admin_page($hook)) {
            wp_enqueue_style('xgenious-popup-admin', XGENIOUS_POPUP_URL . 'assets/admin/css/popup-admin.css', array(), XGENIOUS_POPUP_VERSION, 'all');
            if ($this->is_settings_page($hook)) {
                wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.7.0', true);
            }
        }
    }
    public function enqueue_scripts($hook) {
        // Check if we're on the Xgenious Popup settings page or custom post type page
        if ($this->is_xgenious_popup_admin_page($hook)) {
            wp_enqueue_script('xgenious-popup-admin', XGENIOUS_POPUP_URL . 'assets/admin/js/popup-admin.js', array('jquery'), XGENIOUS_POPUP_VERSION, false);
        }
    }

    private function is_xgenious_popup_admin_page($hook) {
        global $post_type;

        // Check for the settings page
        $settings_page = 'toplevel_page_xgenious-popup-builder';

        // Check for the custom post type
        $is_popup_post_type = ($hook == 'post-new.php' || $hook == 'post.php') && $post_type == 'xgenious_popup';

        return $hook == $settings_page || $is_popup_post_type;
    }

    private function is_settings_page($hook) {
        return $hook == 'toplevel_page_xgenious-popup-builder';
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            'Xgenious Popup Builder',
            'Xgenious Popup',
            'manage_options',
            'xgenious-popup-builder',
            array($this, 'display_plugin_setup_page'),
            'dashicons-admin-generic',
            100
        );
        add_submenu_page(
            'xgenious-popup-builder',
            'Recent Views',
            'Recent Views',
            'manage_options',
            'xgenious-popup-recent-views',
            array($this, 'display_recent_views_page')
        );
    }
    public function display_recent_views_page() {
        $recent_views_page = new RecentViewsPage();
        $recent_views_page->render_page();
    }
    public function display_plugin_setup_page() {
        include_once __DIR__ . '/partials/popup-admin-display.php';
    }

    public function register_settings() {
        register_setting('xgenious_popup_options', 'xgenious_popup_options', array($this, 'validate_options'));

        add_settings_section(
            'xgenious_popup_general',
            __('General Settings', 'xgenious-popup-builder'),
            array($this, 'section_general_cb'),
            'xgenious-popup-builder'
        );

        add_settings_field(
            'default_delay',
            __('Default Delay (seconds)', 'xgenious-popup-builder'),
            array($this, 'field_default_delay_cb'),
            'xgenious-popup-builder',
            'xgenious_popup_general',
            array('label_for' => 'default_delay')
        );
    }

    public function section_general_cb() {
        echo '<p>' . __('General settings for the Xgenious Popup Builder.', 'xgenious-popup-builder') . '</p>';
    }

    public function field_default_delay_cb($args) {
        $options = get_option('xgenious_popup_options');
        $value = isset($options['default_delay']) ? $options['default_delay'] : '';
        ?>
        <input type="number" id="<?php echo esc_attr($args['label_for']); ?>"
               name="xgenious_popup_options[default_delay]"
               value="<?php echo esc_attr($value); ?>"
               min="0">
        <p class="description"><?php echo esc_html__('Enter the default delay in seconds before the popup appears.', 'xgenious-popup-builder'); ?></p>
        <?php
    }

    public function validate_options($input) {
        $valid = array();
        $valid['default_delay'] = (isset($input['default_delay']) && is_numeric($input['default_delay'])) ? intval($input['default_delay']) : 0;
        return $valid;
    }
}