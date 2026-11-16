<?php
defined('ABSPATH') || exit;
function dp_page_url($slug) { if (in_array($slug, ['san-pham', 'thiet-bi'], true)) return get_post_type_archive_link('dp_product'); $p=get_page_by_path($slug); return $p?get_permalink($p):home_url('/#'.$slug); }
function dp_page_heading($title,$label='DP Technical Solution'){ if(is_page()){ $title=dp_hv(get_queried_object_id(),'dp_page_title') ?: $title; $label=dp_hv(get_queried_object_id(),'dp_page_eyebrow') ?: $label; } ?>
<div class="page-heading"><div class="wrap"><div class="breadcrumb"><a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a> / <?php echo esc_html($title); ?></div><span class="eyebrow"><?php echo esc_html($label); ?></span><h1><?php echo esc_html($title); ?></h1></div></div><?php }
function dp_news_card(){ ?><article class="card news-card"><a href="<?php the_permalink(); ?>"><?php if(has_post_thumbnail())the_post_thumbnail('large',['loading'=>'lazy']); ?><div class="card-body"><p class="post-meta"><?php echo esc_html(get_the_date()); ?></p><h2><?php the_title(); ?></h2><p><?php echo esc_html(wp_trim_words(get_the_excerpt(),28)); ?></p><span class="card-bottom">Đọc bài viết →</span></div></a></article><?php }
function dp_render_business_page($kind){get_header();while(have_posts()){the_post(); ?>
<main id="main"><?php dp_page_heading(get_the_title()); ?><div class="wrap section page-layout"><article class="page-article"><?php if(has_post_thumbnail())the_post_thumbnail('large',['class'=>'page-cover']); ?><div class="entry-content"><?php the_content();wp_link_pages(); ?></div><?php if($kind==='contact')dp_contact(); ?></article><aside class="page-sidebar" aria-label="Thông tin liên quan"><div class="sidebar-box"><h2>Khám phá DP</h2><nav><?php foreach(['gioi-thieu'=>'Giới thiệu','phu-tung'=>'Phụ tùng','dich-vu'=>'Dịch vụ','giai-phap'=>'Giải pháp','tin-tuc'=>'Tin tức','lien-he'=>'Liên hệ'] as $slug=>$label){ ?><a href="<?php echo esc_url(dp_page_url($slug)); ?>" <?php if(get_post_field('post_name',get_the_ID())===$slug)echo 'aria-current="page"'; ?>><?php echo esc_html($label); ?> →</a><?php } ?></nav></div><div class="sidebar-box sidebar-help"><h2>Cần tư vấn kỹ thuật?</h2><p>Chia sẻ yêu cầu thiết bị, phụ tùng hoặc hệ thống của bạn với DP.</p><a class="btn" href="<?php echo esc_url($kind==='contact'?'#lien-he':dp_page_url('lien-he')); ?>">Liên hệ với DP →</a></div></aside></div><?php dp_page_sections(); ?></main><?php }get_footer();}
add_action('wp_enqueue_scripts',function(){wp_enqueue_style('dp-pages',get_theme_file_uri('assets/pages.css'),['dp-typography'],(string)filemtime(get_theme_file_path('assets/pages.css')));});

function dp_handle_contact_request() {
    $redirect = dp_page_url('lien-he');
    $finish = static function ($status) use ($redirect) {
        wp_safe_redirect(add_query_arg('contact_status', $status, $redirect));
        exit;
    };
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dp_contact_nonce'] ?? '')), 'dp_contact_request')) $finish('error');
    if (!empty($_POST['contact_website'])) $finish('error');
    $name = sanitize_text_field(wp_unslash($_POST['contact_name'] ?? ''));
    $company = sanitize_text_field(wp_unslash($_POST['contact_company'] ?? ''));
    $phone = sanitize_text_field(wp_unslash($_POST['contact_phone'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['contact_email'] ?? ''));
    $message = sanitize_textarea_field(wp_unslash($_POST['contact_message'] ?? ''));
    if (!$name || !$phone || !is_email($email) || !$message || strlen($message)>20000 || strlen($name)>600 || strlen($company)>800 || strlen($phone)>120) $finish('error');
    $recipient = sanitize_email(get_theme_mod('dp_email')) ?: get_option('admin_email');
    $body = "Họ và tên: $name\nCông ty: $company\nĐiện thoại: $phone\nEmail: $email\n\nNội dung:\n$message";
    $sent = wp_mail($recipient, 'Yêu cầu tư vấn từ website DP', $body, ['Reply-To: '.$email]);
    $finish($sent ? 'sent' : 'error');
}
add_action('admin_post_dp_contact_request', 'dp_handle_contact_request');
add_action('admin_post_nopriv_dp_contact_request', 'dp_handle_contact_request');

// Keep the equipment archive as the single product listing URL.
add_filter('page_link', function ($url, $post_id) {
    return get_post_field('post_name', $post_id) === 'san-pham' ? get_post_type_archive_link('dp_product') : $url;
}, 10, 2);
add_action('template_redirect', function () {
    global $wp;
    $legacy_path = trim(preg_replace('~^index\.php/~', '', $wp->request), '/');
    if ((!is_page('san-pham') && $legacy_path !== 'san-pham') || is_preview()) return;
    $url = get_post_type_archive_link('dp_product');
    $paged = max((int) get_query_var('paged'), (int) get_query_var('page'));
    if ($paged > 1) $url = add_query_arg('paged', $paged, $url);
    wp_safe_redirect($url, 301);
    exit;
});
