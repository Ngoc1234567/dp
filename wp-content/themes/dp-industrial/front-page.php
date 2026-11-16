<?php
/** Dynamic homepage: content and order are managed through ACF. */
defined('ABSPATH') || exit;
get_header();
$home_id = is_page() ? get_queried_object_id() : (int) get_option('page_on_front');
$sections = dp_home_ids($home_id, 'dp_home_sections', 'dp_home_section');
$used_anchors = [];
?>
<main id="main" class="dp-home"><h1 class="screen-reader-text"><?php echo esc_html(get_the_title($home_id)); ?></h1>
<?php foreach ($sections as $section_id) require get_template_directory().'/inc/home-section.php'; ?>
<?php if (!$sections && current_user_can('edit_post', $home_id)): ?><div class="wrap section"><p>Chọn các khối trong Trang → Trang chủ → DP — Thiết kế trang chủ để hiển thị nội dung.</p><a class="btn" href="<?php echo esc_url(get_edit_post_link($home_id)); ?>">Chỉnh sửa trang chủ</a></div><?php endif; ?>
</main>
<?php if (dp_hv($home_id, 'dp_home_dock', 1)):
    $phone = preg_replace('/[^0-9+]/', '', dp_hv($home_id, 'dp_home_phone'));
    $phone_label = dp_hv($home_id, 'dp_home_phone_label') ?: 'Hotline';
    $zalo_phone = preg_replace('/^84/', '0', ltrim($phone, '+'));
    $zalo_url = dp_hv($home_id, 'dp_home_zalo_url') ?: ($zalo_phone ? 'https://zalo.me/' . $zalo_phone : '');
    $contact = dp_hv($home_id, 'dp_home_contact_link');
    $contact_url = is_array($contact) ? esc_url($contact['url'] ?? '') : '';
    $contact_label = is_array($contact) ? ($contact['title'] ?? '') : '';
    $contact_label = $contact_label ?: 'Liên hệ';
    if ($phone || $contact_url || $zalo_url): ?>
<nav class="dp-floating-contact" aria-label="Liên hệ nhanh">
    <?php if ($contact_url): ?>
    <a class="dp-floating-button dp-floating-message" href="<?php echo $contact_url; ?>" aria-label="<?php echo esc_attr($contact_label); ?>" title="<?php echo esc_attr($contact_label); ?>"<?php if (($contact['target'] ?? '') === '_blank') echo ' target="_blank" rel="noopener noreferrer"'; ?>>
        <span class="dp-floating-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8z" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><circle cx="8" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="16" cy="12" r="1"/></svg></span>
    </a>
    <?php endif; if ($zalo_url): ?>
    <a class="dp-floating-button dp-floating-zalo" href="<?php echo esc_url($zalo_url); ?>" aria-label="Chat qua Zalo" title="Chat qua Zalo" target="_blank" rel="noopener noreferrer">
        <span class="dp-floating-icon" aria-hidden="true"><span class="dp-floating-zalo-text">Zalo</span></span>
    </a>
    <?php endif; if ($phone): ?>
    <a class="dp-floating-button dp-floating-phone" href="tel:<?php echo esc_attr($phone); ?>" aria-label="<?php echo esc_attr($phone_label); ?>" title="<?php echo esc_attr($phone_label); ?>">
        <span class="dp-floating-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M7.1 2.9 4.6 4.1c-.9.4-1.5 1.4-1.4 2.4.6 7.5 6.8 13.7 14.3 14.3 1 .1 2-.5 2.4-1.4l1.2-2.5c.3-.6.1-1.4-.5-1.8l-3.4-2.2c-.6-.4-1.3-.3-1.8.2l-1.4 1.4a11.4 11.4 0 0 1-4.5-4.5l1.4-1.4c.5-.5.6-1.2.2-1.8L8.9 3.4c-.4-.6-1.2-.8-1.8-.5z"/></svg></span>
    </a>
    <?php endif; ?>
</nav>
<?php endif; endif; ?>
<?php get_footer(); ?>
