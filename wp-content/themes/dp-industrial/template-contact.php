<?php
/* Template Name: DP — Contact */
defined('ABSPATH') || exit;
get_header();
$phone = get_theme_mod('dp_phone');
$email = get_theme_mod('dp_email');
$address = get_theme_mod('dp_address');
$status = sanitize_key($_GET['contact_status'] ?? '');
$headquarters_address = $address ?: '132/39 Nguyen Tri Phuong Street, Tam Thang Ward, Ho Chi Minh City, Vietnam';
$headquarters_address = dp_hv(get_queried_object_id(), 'dp_contact_map_address') ?: $headquarters_address;
$map_query = rawurlencode($headquarters_address);
?>
<main id="main" class="wrap dp-contact-page">
    <div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html(dp_content('dp_copy_template_contact_1', get_queried_object_id())); ?></a><?php echo esc_html(dp_content('dp_copy_template_contact_2', get_queried_object_id())); ?></div>
    <div class="dp-contact-layout">
        <section class="dp-contact-info" aria-labelledby="contact-title">
            <h1 id="contact-title"><?php echo esc_html(dp_content('dp_copy_template_contact_3', get_queried_object_id())); ?></h1><div class="dp-contact-rule" aria-hidden="true"></div>
            <p class="dp-contact-intro"><?php echo esc_html(dp_content('dp_copy_template_contact_4', get_queried_object_id())); ?></p>
            <div class="dp-contact-cards">
                <?php foreach ([[dp_hv(get_queried_object_id(),'dp_card_address_label','Address'), $address, '', '⌖'], [dp_hv(get_queried_object_id(),'dp_card_phone_label','Hotline'), $phone, $phone ? 'tel:'.preg_replace('/[^0-9+]/','',$phone) : '', '☎'], [dp_hv(get_queried_object_id(),'dp_card_email_label','Email'), $email, $email ? 'mailto:'.$email : '', '✉'], [dp_hv(get_queried_object_id(),'dp_card_service_label','Technical advice'), dp_hv(get_queried_object_id(),'dp_card_service_text','Equipment · Spare parts · Services'), dp_page_url('dich-vu'), '⚙']] as [$label,$value,$url,$icon]): ?>
                <article class="dp-contact-card"><span class="dp-contact-icon" aria-hidden="true"><?php echo esc_html($icon); ?></span><h2><?php echo esc_html($label); ?></h2><?php if ($url): ?><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($value); ?></a><?php else: ?><p><?php echo nl2br(esc_html($value ?: 'Coming soon')); ?></p><?php endif; ?></article>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="dp-contact-form-panel" aria-label="Send an enquiry">
            <?php if ($status): ?><p class="dp-contact-result" role="status"><?php echo $status === 'sent' ? 'Your enquiry has been sent. Thank you for contacting DP.' : 'Your enquiry could not be sent. Please check your details or contact us by phone or email.'; ?></p><?php endif; ?>
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" class="dp-contact-form">
                <input type="hidden" name="action" value="dp_contact_request">
                <?php wp_nonce_field('dp_contact_request', 'dp_contact_nonce'); ?>
                <label for="contact-name"><?php echo esc_html(dp_content('dp_copy_template_contact_5', get_queried_object_id())); ?><span>*</span></label><input id="contact-name" name="contact_name" autocomplete="name" placeholder="<?php echo esc_attr(dp_content('dp_placeholder_template_contact_php_22', get_queried_object_id())); ?>" maxlength="150" required>
                <label for="contact-company"><?php echo esc_html(dp_content('dp_copy_template_contact_6', get_queried_object_id())); ?></label><input id="contact-company" name="contact_company" autocomplete="organization" placeholder="<?php echo esc_attr(dp_content('dp_placeholder_template_contact_php_23', get_queried_object_id())); ?>" maxlength="200">
                <label for="contact-phone"><?php echo esc_html(dp_content('dp_copy_template_contact_7', get_queried_object_id())); ?><span>*</span></label><input id="contact-phone" type="tel" name="contact_phone" autocomplete="tel" placeholder="<?php echo esc_attr(dp_content('dp_placeholder_template_contact_php_24', get_queried_object_id())); ?>" maxlength="30" required>
                <label for="contact-email"><?php echo esc_html(dp_content('dp_copy_template_contact_8', get_queried_object_id())); ?><span>*</span></label><input id="contact-email" type="email" name="contact_email" autocomplete="email" placeholder="<?php echo esc_attr(dp_content('dp_placeholder_template_contact_php_25', get_queried_object_id())); ?>" maxlength="254" required>
                <label for="contact-message"><?php echo esc_html(dp_content('dp_copy_template_contact_9', get_queried_object_id())); ?><span>*</span></label><textarea id="contact-message" name="contact_message" rows="5" placeholder="<?php echo esc_attr(dp_content('dp_placeholder_template_contact_php_26', get_queried_object_id())); ?>" maxlength="5000" required></textarea>
                <div hidden aria-hidden="true"><label><?php echo esc_html(dp_content('dp_copy_template_contact_10', get_queried_object_id())); ?><input name="contact_website" tabindex="-1" autocomplete="off"></label></div>
                <p class="dp-contact-note"><?php echo esc_html(dp_content('dp_copy_template_contact_11', get_queried_object_id())); ?></p>
                <button class="btn" type="submit"><?php echo esc_html(dp_content('dp_copy_template_contact_12', get_queried_object_id())); ?><span aria-hidden="true">↗</span></button>
            </form>
        </section>
    </div>
    <?php if (dp_hv(get_queried_object_id(), 'dp_contact_show_map', 1)): ?><section class="dp-headquarters" aria-labelledby="headquarters-title">
        <div class="dp-headquarters-heading"><h2 id="headquarters-title"><?php echo esc_html(dp_content('dp_copy_template_contact_13', get_queried_object_id())); ?></h2><div class="dp-contact-rule" aria-hidden="true"></div></div>
        <div class="dp-headquarters-map">
            <iframe title="DP head office map" src="<?php echo esc_url('https://www.google.com/maps?q='.$map_query.'&output=embed'); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
        <div class="dp-headquarters-details">
            <div><span class="dp-footer-label"><?php echo esc_html(dp_content('dp_copy_template_contact_14', get_queried_object_id())); ?></span><h3><?php echo esc_html(dp_content('dp_copy_template_contact_15', get_queried_object_id())); ?></h3><address><?php echo esc_html($headquarters_address); ?></address></div>
            <a class="btn" href="<?php echo esc_url('https://www.google.com/maps/dir/?api=1&destination='.$map_query); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html(dp_content('dp_copy_template_contact_16', get_queried_object_id())); ?><span aria-hidden="true">↗</span></a>
        </div>
    </section>
    <?php endif; if (dp_hv(get_queried_object_id(), 'dp_contact_show_parts', 1)): ?><section class="dp-parts-feature" aria-labelledby="parts-feature-title">
        <div class="dp-parts-feature-copy">
            <span class="dp-parts-kicker"><?php echo esc_html(dp_content('dp_copy_template_contact_17', get_queried_object_id())); ?></span>
            <h2 id="parts-feature-title"><?php echo esc_html(dp_content('dp_copy_template_contact_18', get_queried_object_id())); ?><br><?php echo esc_html(dp_content('dp_copy_template_contact_19', get_queried_object_id())); ?></h2>
            <p><?php echo esc_html(dp_content('dp_copy_template_contact_20', get_queried_object_id())); ?></p>
            <p><strong><?php echo esc_html(dp_content('dp_copy_template_contact_21', get_queried_object_id())); ?></strong></p>
            <a class="btn dp-parts-feature-button" href="<?php echo esc_url((dp_hv(get_queried_object_id(), 'dp_contact_parts_link')['url'] ?? '') ?: dp_page_url('phu-tung')); ?>"><?php echo esc_html(dp_content('dp_copy_template_contact_22', get_queried_object_id())); ?><span aria-hidden="true">↗</span></a>
        </div>
        <div class="dp-parts-feature-image"><img src="<?php echo esc_url(wp_get_attachment_image_url((int) dp_hv(get_queried_object_id(), 'dp_contact_parts_image'), 'large') ?: get_theme_file_uri('assets/profile/spares.png')); ?>" alt="Spare parts and industrial supplies from the DP company profile" loading="lazy"></div>
    </section>
<?php endif; dp_page_sections(); ?></main>
<?php get_footer(); ?>
