<?php
defined('ABSPATH') || exit;
// Use English for public WordPress UI without changing stored content or admin preferences.
add_action('after_setup_theme', function () {
 if (!is_admin() && !wp_doing_ajax()) switch_to_locale('en_US');
});
add_action('after_setup_theme', function () { add_theme_support('title-tag'); add_theme_support('post-thumbnails'); add_theme_support('html5', ['search-form','gallery','caption']); });
add_action('wp_enqueue_scripts', function () { wp_enqueue_style('dp', get_stylesheet_uri(), [], (string) filemtime(get_stylesheet_directory().'/style.css')); wp_enqueue_style('dp-company', get_theme_file_uri('assets/company.css'), ['dp'], (string) filemtime(get_theme_file_path('assets/company.css'))); wp_enqueue_style('dp-home-layout', get_theme_file_uri('assets/home-layout.css'), ['dp-company'], (string) filemtime(get_theme_file_path('assets/home-layout.css'))); wp_enqueue_style('dp-typography', get_theme_file_uri('assets/typography.css'), ['dp-home-layout'], (string) filemtime(get_theme_file_path('assets/typography.css'))); wp_enqueue_script('dp', get_theme_file_uri('assets/site.js'), [], (string) filemtime(get_theme_file_path('assets/site.js')), true); });
function dp_image($post_id = 0) { $model=get_post_meta($post_id,'_dp_model',true); $fallback=['FL-30'=>'forklift.svg','GP-250'=>'generator.svg']; return ($post_id && get_the_post_thumbnail_url($post_id, 'large')) ?: get_theme_file_uri('assets/'.($fallback[$model] ?? 'equipment.jpg')); }
function dp_card() { ?>
<article class="card"><a href="<?php the_permalink(); ?>"><div class="card-image"><img src="<?php echo esc_url(dp_image(get_the_ID())); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy"><span class="tag">DP CATALOGUE</span></div><div class="card-body"><span class="eyebrow"><?php echo esc_html(get_post_meta(get_the_ID(), '_dp_model', true) ?: 'DP Technical Solution'); ?></span><h3><?php the_title(); ?></h3><p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p><div class="card-bottom"><span>View catalogue</span><span aria-hidden="true">↗</span></div></div></a></article>
<?php }
add_action('customize_register', function ($c) {
 $c->add_section('dp_brand', ['title'=>'DP • Branding & banner']);
 foreach (['company'=>['Company name','DP TECHNICAL SOLUTION'], 'headline'=>['Banner heading','Power for every project.'], 'intro'=>['Banner description','Explore construction, lifting and power equipment solutions for your operational needs.'], 'phone'=>['Hotline',''], 'email'=>['Contact email',''], 'address'=>['Address','']] as $key=>$field) {
  $c->add_setting('dp_'.$key, ['default'=>$field[1], 'sanitize_callback'=>$key==='email'?'sanitize_email':'sanitize_text_field']);
  $c->add_control('dp_'.$key,['label'=>$field[0],'section'=>'dp_brand','type'=>'text']);
 }
 $c->add_setting('dp_banner',['sanitize_callback'=>'absint']);
 $c->add_control(new WP_Customize_Media_Control($c,'dp_banner',['label'=>'Banner image','section'=>'dp_brand','mime_type'=>'image']));
});
function dp_contact() { ?>
<section class="contact-panel" id="lien-he"><span class="eyebrow">Connect with us</span><h3>Discuss your requirements with DP</h3><?php $phone=get_theme_mod('dp_phone'); $email=get_theme_mod('dp_email'); if ($phone) { ?><p>Hotline: <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/','',$phone)); ?>"><?php echo esc_html($phone); ?></a></p><?php } if ($email) { ?><p>Email: <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p><?php } if (!$phone && !$email) { ?><p>Contact information is being updated. The enquiry form will be available soon.</p><?php } ?><p><?php echo esc_html(get_theme_mod('dp_address')); ?></p></section>
<?php }

function dp_brand() { ?><img class="dp-logo" src="<?php echo esc_url((wp_get_attachment_image_url((int) dp_hv(dp_settings_id(), 'dp_shared_logo'), 'full') ?: get_theme_file_uri('assets/profile/logo.png'))); ?>" alt="Logo DP"><span class="brand-copy"><strong><?php echo esc_html(get_theme_mod('dp_company','DP TECHNICAL SOLUTION')); ?></strong><small><?php echo esc_html(dp_hv(dp_settings_id(), 'dp_shared_tagline', 'TECHNICAL SOLUTIONS – SUSTAINABLE VALUE')); ?></small></span><?php }

require_once get_template_directory().'/inc/pages.php';

require_once get_template_directory().'/inc/acf-home.php';

require_once get_template_directory().'/inc/acf-content.php';
