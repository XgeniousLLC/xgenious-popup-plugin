<?php
/**
 * Elementor Widget
 * @package xgenous
 * @since 1.0.0
 */
namespace Xgenious\PopupBuilder\Elementor\Widgets;

use Elementor\Plugin;
use Elementor\Widget_Base;

class PopupWidgetTwo extends Widget_Base
{

    /**
     * Get widget name.
     *
     * Retrieve Elementor widget name.
     *
     * @return string Widget name.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_name() {
        return 'xgenious-popup-area-two-widget';
    }

    /**
     * Get widget title.
     *
     * Retrieve Elementor widget title.
     *
     * @return string Widget title.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_title() {
        return esc_html__( 'Popup Two', 'xgenous-master' );
    }

    /**
     * Get widget icon.
     *
     * Retrieve Elementor widget icon.
     *
     * @return string Widget icon.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_icon() {
        return 'eicon-archive-title';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the Elementor widget belongs to.
     *
     * @return array Widget categories.
     * @since 1.0.0
     * @access public
     *
     */
    public function get_categories() {
        return [ 'xgenious_popup' ];
    }

    /**
     * Register Elementor widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function register_controls() {


        $this->start_controls_section(
            'contact_Form_settings_section',
            [
                'label' => esc_html__( 'General Settings', 'xgenous-master' ),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
        $this->add_control(
            'bg_image',
            [
                'label' => esc_html__( 'Background Image', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'banner_right_image',
            [
                'label' => esc_html__( 'Banner Right Image', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'brand_image',
            [
                'label' => esc_html__( 'Brand Image', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'brand_text',
            [
                'label' => esc_html__( 'Brand Title Text', 'hexcoupon-master' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'HexCoupon', 'hexcoupon-master' ),
                'placeholder' => esc_html__( 'Type your text here', 'hexcoupon-master' ),
            ]
        );

        $this->add_control(
            'offer_title_text',
            [
                'label' => esc_html__( 'Offer Title Text', 'hexcoupon-master' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'EARLY BIRD SPECIAL', 'hexcoupon-master' ),
                'placeholder' => esc_html__( 'Type your text here', 'hexcoupon-master' ),
            ]
        );

        $this->add_control(
            'offer_discount_text',
            [
                'label' => esc_html__( 'Offer Discount Text', 'hexcoupon-master' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( '60% DISCOUNT', 'hexcoupon-master' ),
                'placeholder' => esc_html__( 'Type your text here', 'hexcoupon-master' ),
            ]
        );

        $this->add_control(
            'limited_time_offer_text',
            [
                'label' => esc_html__( 'Limited Time Offer Text', 'hexcoupon-master' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'LIMITED TIME OFFER 路 OFFER ENDS SOON', 'hexcoupon-master' ),
                'placeholder' => esc_html__( 'Type your text here', 'hexcoupon-master' ),
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label' => esc_html__( 'Button Text', 'hexcoupon-master' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Grab The Deal', 'hexcoupon-master' ),
                'placeholder' => esc_html__( 'Type your text here', 'hexcoupon-master' ),
            ]
        );

        $this->add_control(
            'button_link',
            [
                'label' => esc_html__( 'Button Link', 'hexcoupon-master' ),
                'type' => \Elementor\Controls_Manager::URL,
                'options' => [ 'url', 'is_external', 'nofollow' ],
                'default' => [
                    'url' => '',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'label_block' => true,
            ]
        );
        $this->end_controls_section();

    }

    /**
     * Render Elementor widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $bg_image_link = ! empty( $settings['bg_image']['url'] ) ? $settings['bg_image']['url'] : [];
        $banner_right_image = ! empty( $settings['banner_right_image']['url'] ) ? $settings['banner_right_image']['url'] : [];
        $image_link = ! empty( $settings['brand_image']['url'] ) ? $settings['brand_image']['url'] : [];
        ?>
        <div id="popup" class="xgenious-popup">
            <div class="popup-content">
                <div class="banner-two">
                    <div class="container">
                        <div class="banner-two-wraper" style="background-image: url('<?php echo esc_url( $bg_image_link ); ?>') ">
                            <div class="row g-4">
                                <span class="close text-end m-auto">&times;</span>
                                <div class="col-lg-5">
                                    <div class="logo-part">
                                        <div class="logo-img text-center">
                                            <img src="<?php echo esc_url( $image_link ); ?>" alt="icon">
                                        </div>
                                        <h3 class="logo-text text-center"><?php printf( esc_html__( '%s', 'hexcoupon-master' ), esc_html( $settings['brand_text'] ) ); ?></h3>
                                    </div>
                                    <div class="discount-part">
                                        <div class="early-bird">
                                            <h3><?php printf( esc_html__( '%s', 'hexcoupon-master' ), esc_html( $settings['offer_title_text'] ) ); ?></h3>
                                            <div class="bird-img">
                                                <svg width="37" height="29" viewBox="0 0 37 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3.93488 7.57288C4.92424 4.4858 7.81752 2.24983 11.231 2.24983C15.1465 2.24983 18.3707 5.18844 18.8319 8.98042L18.8342 8.98269C20.6905 8.71523 22.4313 5.81515 24.2151 0.750488C28.1963 5.48196 27.9538 10.7279 26.7559 12.6931C29.291 12.9719 34.1676 11.4895 36.2959 10.329C32.5866 18.6213 27.2806 22.6717 22.0867 23.7834C20.3256 24.7172 18.3231 25.259 16.1891 25.259C15.9636 25.259 15.7415 25.2352 15.5193 25.2272L16.5767 27.0189L18.8104 27.7511L18.6302 28.303L16.4656 27.5947L15.4479 28.6396L15.032 28.2361L16.0237 27.2195L14.8224 25.1853C13.9962 25.0946 13.195 24.928 12.4266 24.6889L10.1362 27.6162L12.0776 28.3358L11.8758 28.8798L9.67385 28.0627L7.6464 28.9682L7.40954 28.439L9.47892 27.5131L11.8543 24.4793C7.12849 22.7465 3.72296 18.2802 3.5779 12.9911C3.57563 12.905 0.295898 12.7781 0.295898 12.7781L3.60509 10.4401L0.366161 7.67714L3.93488 7.57288ZM8.23911 10.363C9.39959 10.363 10.3402 9.42127 10.3402 8.25852C10.3402 7.0969 9.39959 6.15514 8.23911 6.15514C7.07749 6.15514 6.13573 7.0969 6.13573 8.25852C6.13573 9.42127 7.07749 10.363 8.23911 10.363Z" fill="white"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="discount">
                                            <h3><?php printf( esc_html__( '%s', 'hexcoupon-master' ), esc_html( $settings['offer_discount_text'] ) ); ?></h3>
                                        </div>
                                    </div>
                                    <div class="pera">
                                        <p class="offer"><?php printf( esc_html__( '%s', 'hexcoupon-master' ), esc_html( $settings['limited_time_offer_text'] ) ); ?></p>
                                        <div class="btn-wraper">
                                            <a href="<?php echo esc_url( $settings['button_link']['url'] ); ?>" class="cmn-btn grab-deal-btn"><?php printf( esc_html__( '%s', 'hexcoupon-master' ), esc_html( $settings['button_text'] ) ); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="left-banner-image">
                                        <img src="<?php echo esc_url( $banner_right_image ); ?>" alt="banner-right-part" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

Plugin::instance()->widgets_manager->register( new PopupWidgetTwo() );