<?php

namespace Xgenious\PopupBuilder\admin;

require_once XGENIOUS_POPUP_PATH . 'admin/PopupPostType.php';
require_once XGENIOUS_POPUP_PATH . 'admin/PopupMetaFields.php';

class Admin {
    public function __construct() {
        new PopupPostType();
        new PopupMetaFields();
        // ... other admin-related code
    }
    public function enqueue_styles() {
        wp_enqueue_style('xgenious-popup-admin', XGENIOUS_POPUP_URL . 'admin/css/popup-admin.css', array(), XGENIOUS_POPUP_VERSION, 'all');
    }

    public function enqueue_scripts() {
        wp_enqueue_script('xgenious-popup-admin', XGENIOUS_POPUP_URL . 'admin/js/popup-admin.js', array('jquery'), XGENIOUS_POPUP_VERSION, false);
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
    }

    public function display_plugin_setup_page() {
        include_once 'partials/popup-admin-display.php';
    }

    public function register_settings() {
        register_setting('xgenious_popup_options', 'xgenious_popup_options', array($this, 'validate_options'));
    }

    public function validate_options($input) {
        // Validate and sanitize input
        return $input;
    }
}