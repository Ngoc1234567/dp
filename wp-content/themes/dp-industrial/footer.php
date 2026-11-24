<?php
/** Site-wide company footer. */
defined('ABSPATH') || exit;
$footer_phone = get_theme_mod('dp_phone');
$footer_email = get_theme_mod('dp_email');
$footer_address = get_theme_mod('dp_address');
$footer_terms = get_terms(['taxonomy' => 'dp_category', 'hide_empty' => true, 'number' => 5]);
?>
<footer class="dp-footer">
    <div class="wrap">
        <div class="dp-footer-grid">
            <div class="dp-footer-company">
                <a class="dp-footer-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="DP Technical Solution — Home"><img src="<?php echo esc_url((wp_get_attachment_image_url((int) dp_hv(dp_settings_id(), 'dp_shared_logo'), 'full') ?: get_theme_file_uri('assets/profile/logo.png'))); ?>" alt="DP Technical Solution" loading="lazy"></a>
                <p class="dp-footer-name"><?php echo esc_html(dp_content('dp_copy_footer_1', dp_settings_id())); ?><br><?php echo esc_html(dp_content('dp_copy_footer_2', dp_settings_id())); ?></p>
                <p><?php echo esc_html(dp_content('dp_copy_footer_3', dp_settings_id())); ?></p>
            </div>
            <nav class="dp-footer-column" aria-label="Explore DP">
                <h3><?php echo esc_html(dp_content('dp_copy_footer_4', dp_settings_id())); ?></h3>
                <?php foreach (['gioi-thieu'=>'About us', 'san-pham'=>'Products', 'phu-tung'=>'Spare parts', 'dich-vu'=>'Services', 'giai-phap'=>'Solutions', 'tin-tuc'=>'News'] as $slug=>$label): ?>
                <a href="<?php echo esc_url(dp_page_url($slug)); ?>"><?php echo esc_html($label); ?></a>
                <?php endforeach; ?>
            </nav>
            <nav class="dp-footer-column" aria-label="Product catalogue">
                <h3><?php echo esc_html(dp_content('dp_copy_footer_5', dp_settings_id())); ?></h3>
                <?php if (!is_wp_error($footer_terms)) foreach ($footer_terms as $term): $term_url = get_term_link($term); if (is_wp_error($term_url)) continue; ?>
                <a href="<?php echo esc_url($term_url); ?>"><?php echo esc_html($term->name); ?></a>
                <?php endforeach; ?>
                <a class="dp-footer-all" href="<?php echo esc_url(get_post_type_archive_link('dp_product')); ?>"><?php echo esc_html(dp_content('dp_copy_footer_6', dp_settings_id())); ?><span aria-hidden="true">↗</span></a>
            </nav>
            <div class="dp-footer-column dp-footer-contact">
                <h3><?php echo esc_html(dp_content('dp_copy_footer_7', dp_settings_id())); ?></h3>
                <?php if ($footer_phone): ?><div><span class="dp-footer-label"><?php echo esc_html(dp_content('dp_copy_footer_8', dp_settings_id())); ?></span><?php echo dp_phone_links($footer_phone, 'dp-footer-phone'); ?></div><?php endif; ?>
                <?php if ($footer_email): ?><div><span class="dp-footer-label"><?php echo esc_html(dp_content('dp_copy_footer_9', dp_settings_id())); ?></span><a href="mailto:<?php echo esc_attr($footer_email); ?>"><?php echo esc_html($footer_email); ?></a></div><?php endif; ?>
                <?php if ($footer_address): ?><div><span class="dp-footer-label"><?php echo esc_html(dp_content('dp_copy_footer_10', dp_settings_id())); ?></span><address><?php echo nl2br(esc_html($footer_address)); ?></address></div><?php endif; ?>
                <a class="dp-footer-all" href="<?php echo esc_url(dp_page_url('lien-he')); ?>"><?php echo esc_html(dp_content('dp_copy_footer_11', dp_settings_id())); ?><span aria-hidden="true">↗</span></a>
            </div>
        </div>
        <div class="dp-footer-bottom"><p>© <?php echo esc_html(wp_date('Y')); ?> DP Technical Solution.</p><p><?php echo esc_html(dp_content('dp_copy_footer_12', dp_settings_id())); ?></p></div>
    </div>
</footer>
<?php wp_footer(); ?>
</body></html>
