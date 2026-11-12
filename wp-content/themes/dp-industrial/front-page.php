<?php
/** Dynamic homepage: content and order are managed through ACF. */
defined('ABSPATH') || exit;
get_header();
$home_id = is_page() ? get_queried_object_id() : (int) get_option('page_on_front');
$sections = dp_home_ids($home_id, 'dp_home_sections', 'dp_home_section');
$used_anchors = [];
$contact_rendered = false;
?>
<main id="main" class="dp-home"><h1 class="screen-reader-text"><?php echo esc_html(get_the_title($home_id)); ?></h1>
<?php foreach ($sections as $section_id) require get_template_directory().'/inc/home-section.php'; ?>
<?php if (!$sections && current_user_can('edit_post', $home_id)): ?><div class="wrap section"><p>Chọn các khối trong Trang → Trang chủ → DP — Thiết kế trang chủ để hiển thị nội dung.</p><a class="btn" href="<?php echo esc_url(get_edit_post_link($home_id)); ?>">Chỉnh sửa trang chủ</a></div><?php endif; ?>
</main>
<?php if (dp_hv($home_id, 'dp_home_dock', 1)): ?><aside class="contact-dock" aria-label="Liên hệ nhanh"><?php $phone=dp_hv($home_id,'dp_home_phone'); if($phone){ ?><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/','',$phone)); ?>">☎<span><?php echo esc_html(dp_hv($home_id,'dp_home_phone_label','Hotline')); ?></span></a><?php } dp_home_link($home_id,'dp_home_contact_link','dock-link'); ?></aside><?php endif; ?>
<?php get_footer(); ?>