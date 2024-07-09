<?php
// Check if user has the right capabilities
if (!current_user_can('manage_options')) {
    return;
}
?>
<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <h2 class="nav-tab-wrapper">
        <a href="?page=xgenious-popup-builder&tab=settings" class="nav-tab <?php echo (!isset($_GET['tab']) || $_GET['tab'] == 'settings') ? 'nav-tab-active' : ''; ?>">Settings</a>
        <a href="?page=xgenious-popup-builder&tab=analytics" class="nav-tab <?php echo (isset($_GET['tab']) && $_GET['tab'] == 'analytics') ? 'nav-tab-active' : ''; ?>">Analytics</a>
    </h2>

    <?php
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';

    if ($active_tab == 'analytics') {
        $this->analytics_page->render_analytics_page();
    } else {
        // Your existing settings form
        ?>
        <form action="options.php" method="post">
            <?php
            settings_fields('xgenious_popup_options');
            do_settings_sections('xgenious-popup-builder');
            submit_button('Save Settings');
            ?>
        </form>
        <?php
    }
    ?>
</div>