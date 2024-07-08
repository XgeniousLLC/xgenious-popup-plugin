<?php
// Check if user has the right capabilities
if (!current_user_can('manage_options')) {
    return;
}
?>

<div class="wrap">
    <h1><?= esc_html(get_admin_page_title()); ?></h1>
    <form action="options.php" method="post">
        <?php
        // Output security fields for the registered setting "xgenious_popup_options"
        settings_fields('xgenious_popup_options');
        // Output setting sections and their fields
        do_settings_sections('xgenious-popup-builder');
        // Output save settings button
        submit_button('Save Settings');
        ?>
    </form>
</div>