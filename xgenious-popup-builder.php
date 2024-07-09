<?php
/*
Plugin Name: Xgenious Popup Builder
Description: A plugin to create and manage popups using Elementor
Version: 1.0.1
Author: Xgenious
Text Domain: xgenious-popup-builder
Domain Path: /languages
*/


if (!defined('ABSPATH')) exit;

// Check if Composer's autoloader exists
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
} else {
    wp_die('Composer autoloader not found. Please run "composer install" in the plugin directory.');
}

const XGENIOUS_POPUP_VERSION = '1.0.0';
define('XGENIOUS_POPUP_PATH', plugin_dir_path(__FILE__));
define('XGENIOUS_POPUP_URL', plugin_dir_url(__FILE__));

function activate_xgenious_popup_builder() {
    \Xgenious\PopupBuilder\Includes\Activator::activate();
}

function deactivate_xgenious_popup_builder() {
    \Xgenious\PopupBuilder\Includes\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_xgenious_popup_builder');
register_deactivation_hook(__FILE__, 'deactivate_xgenious_popup_builder');

function run_xgenious_popup_builder() {
    $plugin = new \Xgenious\PopupBuilder\Includes\XgeniousPopupBuilder();
    $plugin->run();
}
run_xgenious_popup_builder();