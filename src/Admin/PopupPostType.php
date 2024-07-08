<?php
namespace Xgenious\PopupBuilder\admin;

class PopupPostType {
    public function __construct() {
        add_action('init', array($this, 'register_popup_post_type'));
        add_action('admin_menu', array($this, 'remove_editor_from_post_type'));
    }

    public function register_popup_post_type() {
        $labels = array(
            'name'               => _x('Popups', 'post type general name', 'xgenious-popup-builder'),
            'singular_name'      => _x('Popup', 'post type singular name', 'xgenious-popup-builder'),
            'menu_name'          => _x('Popups', 'admin menu', 'xgenious-popup-builder'),
            'name_admin_bar'     => _x('Popup', 'add new on admin bar', 'xgenious-popup-builder'),
            'add_new'            => _x('Add New', 'popup', 'xgenious-popup-builder'),
            'add_new_item'       => __('Add New Popup', 'xgenious-popup-builder'),
            'new_item'           => __('New Popup', 'xgenious-popup-builder'),
            'edit_item'          => __('Edit Popup', 'xgenious-popup-builder'),
            'view_item'          => __('View Popup', 'xgenious-popup-builder'),
            'all_items'          => __('All Popups', 'xgenious-popup-builder'),
            'search_items'       => __('Search Popups', 'xgenious-popup-builder'),
            'parent_item_colon'  => __('Parent Popups:', 'xgenious-popup-builder'),
            'not_found'          => __('No popups found.', 'xgenious-popup-builder'),
            'not_found_in_trash' => __('No popups found in Trash.', 'xgenious-popup-builder')
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'popup'),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'elementor')
        );

        register_post_type('xgenious_popup', $args);
    }

    public function remove_editor_from_post_type() {
        remove_post_type_support('xgenious_popup', 'editor');
    }
}