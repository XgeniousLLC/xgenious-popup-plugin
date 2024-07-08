<?php
/**
 * All Elementor widget init
 * @package Xgenious
 * @since 1.0.0
 */
namespace Xgenious\PopupBuilder\Elementor;

    class ElementorWidgetInit{
        /*
        * $instance
        * @since 1.0.0
        * */
        private static $instance;
        /*
        * construct()
        * @since 1.0.0
        * */
        public function __construct() {
            add_action( 'elementor/elements/categories_registered', array($this,'_widget_categories') );
            //elementor widget registered
            add_action('elementor/widgets/register',array($this,'_widget_registered'));
        }
        /*
       * getInstance()
       * @since 1.0.0
       * */
        public static function getInstance(){
            if ( null == self::$instance ){
                self::$instance = new self();
            }
            return self::$instance;
        }
        /**
         * _widget_categories()
         * @since 1.0.0
         * */
        public function _widget_categories($elements_manager){
            $elements_manager->add_category(
                'xgenious_popup',
                [
                    'title' => __( 'Xgenious Popup', 'xgenious-master' ),
                    'icon' => 'fa fa-plug',
                ]
            );
        }

        /**
         * _widget_registered()
         * @since 1.0.0
         * */
        public function _widget_registered(){
            if( !class_exists('Elementor\Widget_Base') ){
                return;
            }
            $elementor_widgets = array(
                'popup',
            );

            $elementor_widgets = apply_filters('xgenious_popup_elementor_widget',$elementor_widgets);
            sort($elementor_widgets);
            if ( is_array($elementor_widgets) && !empty($elementor_widgets) ) {
                foreach ( $elementor_widgets as $widget ){
                    if(file_exists(XGENIOUS_POPUP_PATH.'elementor/widgets/class-elementor-'.$widget.'-widget.php')){
                        require_once XGENIOUS_POPUP_PATH.'elementor/widgets/class-elementor-'.$widget.'-widget.php';
                    }
                }
            }

        }

    }

//if ( class_exists('Elementor_Widget_Init') ){
ElementorWidgetInit::getInstance();
//}
