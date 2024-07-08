<?php

namespace Xgenious\PopupBuilder\Includes;
use Xgenious\PopupBuilder\Admin\Admin;

class XgeniousPopupBuilder {
    public function run() {
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        // Load any additional dependencies here
    }

    private function set_locale() {
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'xgenious-popup-builder',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    private function define_admin_hooks() {
        $plugin_admin = new Admin();
        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($plugin_admin, 'enqueue_scripts'));
        add_action('admin_menu', array($plugin_admin, 'add_plugin_admin_menu'));
        add_action('admin_init', array($plugin_admin, 'register_settings'));
    }

    private function define_public_hooks() {
        $plugin_public = new Display();
        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($plugin_public, 'enqueue_scripts'));
        add_action('wp_footer', array($plugin_public, 'display_popup'));

        $plugin_analytics = new Analytics();
        add_action('wp_ajax_record_popup_view', array($plugin_analytics, 'record_popup_view'));
        add_action('wp_ajax_nopriv_record_popup_view', array($plugin_analytics, 'record_popup_view'));
    }
}



