<?php
/**
 * Elementor Widget
 * @package xgenous
 * @since 1.0.0
 */
namespace Xgenious\PopupBuilder\Elementor\Widgets;

use Elementor\Plugin;
use Elementor\Widget_Base;

class PopupWidget extends Widget_Base
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
        return 'xgenious-popup-area-one-widget';
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
        return esc_html__( 'Popup', 'xgenous-master' );
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
        $this->add_control( 'subtitle', [
            'type'    => \Elementor\Controls_Manager::TEXT,
            'label'   => esc_html__( 'Subtitle', 'xgenious-master' ),
        ] );
        $this->add_control( 'title', [
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'label'   => esc_html__( 'Title', 'xgenious-master' ),
            'description' => esc_html__('use {c}color with anchor{/c} , use {b}break into new line{/b}')
        ] );
        $this->add_control( 'description', [
            'type'    => \Elementor\Controls_Manager::TEXTAREA,
            'label'   => esc_html__( 'Description', 'xgenious-master' ),
        ] );
        $this->add_control( 'image', [
            'type'    => \Elementor\Controls_Manager::MEDIA,
            'label'   => esc_html__( 'Image', 'xgenious-master' ),
        ] );
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
        ?>
        <section class="banner-area about-banner banner-padding section-bg-2 active">
            <div class="banner-bottom-shape">
                <img src="<?php echo XGENIOUS_IMG;?>/banner/bottom-shape.png" alt="">
            </div>
            <div class="banner-shapes">
                <img src="<?php echo XGENIOUS_IMG;?>/banner/banner-shape2.svg" alt="">
                <img src="<?php echo XGENIOUS_IMG;?>/banner/banner-shape4.svg" alt="">
            </div>
            <div class="container container_1430">
                <div class="row align-items-center justify-content-between flex-lg-row flex-column-reverse">
                    <div class="col-xxl-6 col-lg-6">
                        <div class="banner-content-wrapper">
                            <div class="banner-contents banner-content-padding">
                                <span class="banner-contents-subtitle mb-3"> <?php echo esc_html($settings['subtitle']);?> </span>
                                <?php
                                $title_markup = $settings['title'];
                                $title_markup = str_replace(['{c}','{/c}',],['<span class="banner-contents-title-shape"><img src="'.XGENIOUS_IMG.'/banner/shape-line.svg" alt="xgenious shape image">','</span>'],$title_markup);

                                printf('<h1 class="banner-contents-title">%1$s</h1>',$title_markup);
                                printf('<p class="banner-contents-para mt-4">%1$s</p>',$settings['description']);
                                ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-5 col-lg-6">
                        <div class="banner-image-wrapper">
                            <div class="banner-shape-image">
                                <img src="<?php echo XGENIOUS_IMG;?>/banner/about_banner_shape1.svg" alt="">
                                <img src="<?php echo XGENIOUS_IMG;?>/banner/about_banner_shape2.svg" alt="">
                                <img src="<?php echo XGENIOUS_IMG;?>/banner/about_banner_shape3.svg" alt="">
                            </div>
                            <div class="banner-image">
                                <?php
                                $image_id = $settings['image']['id'];
                                $image_src = !empty($image_id) ? wp_get_attachment_image_src($image_id,'full')[0] : '';
                                $image_alt = !empty($image_id) ? get_post_meta($image_id,'_wp_attachment_image_alt',true) : '';
                                printf('<img src="%1$s" alt="%2$s"/>',$image_src,$image_alt);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}

Plugin::instance()->widgets_manager->register( new PopupWidget() );