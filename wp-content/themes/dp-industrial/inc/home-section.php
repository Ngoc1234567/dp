<?php
defined('ABSPATH') || exit;
$layout = dp_hv($section_id, 'dp_hs_layout', 'content');
$items = dp_home_ids($section_id, 'dp_hs_items', 'dp_home_item');
$heading = dp_hv($section_id, 'dp_hs_heading');
$eyebrow = dp_hv($section_id, 'dp_hs_eyebrow');
$body = dp_hv($section_id, 'dp_hs_body');
$columns = (int) dp_hv($section_id, 'dp_hs_columns', 3);
if (!in_array($columns, [2,3,4], true)) $columns = 3;
$background = dp_hv($section_id, 'dp_hs_background', 'white');
if (!in_array($background, ['white','muted','dark'], true)) $background = 'white';
if (in_array($layout, ['hero','categories','brands','cards','accordion'], true) && !$items && !$heading && !$body) return;
if ($layout === 'hero' && !$items) return;
if ($layout === 'news') {
    $selected = array_filter(array_map('absint', (array) dp_hv($section_id, 'dp_hs_posts', [])));
    $args = ['post_type'=>'post','post_status'=>'publish','posts_per_page'=>max(1,min(12,(int)dp_hv($section_id,'dp_hs_count',3)))];
    if ($selected) { $args['post__in']=$selected; $args['orderby']='post__in'; }
    else $args['post__not_in']=array_filter([(int)(get_page_by_path('hello-world',OBJECT,'post')->ID??0)]);
    $news=new WP_Query($args);
    if (!$news->have_posts()) return;
}
$anchor = sanitize_title(dp_hv($section_id, 'dp_hs_anchor')) ?: 'home-block-'.$section_id;
if (isset($used_anchors[$anchor])) $anchor .= '-'.$section_id;
$used_anchors[$anchor] = true;
?>
<section id="<?php echo esc_attr($anchor); ?>" class="dp-home-block dp-layout-<?php echo esc_attr($layout); ?> dp-bg-<?php echo esc_attr($background); ?>" style="--home-columns:<?php echo (int)$columns; ?>">
<?php if ($layout === 'hero'): $count=count($items); ?>
<div class="home-slider" aria-label="<?php echo esc_attr($heading ?: 'Banner'); ?>" aria-roledescription="carousel" style="--slide-height:<?php echo max(360,min(900,(int)dp_hv($section_id,'dp_hs_height',540))); ?>px">
<?php foreach ($items as $i=>$item_id): $title=dp_hv($item_id,'dp_hi_heading') ?: get_the_title($item_id); $fit=dp_hv($item_id,'dp_hi_fit','cover')==='contain'?'contain':'cover'; ?>
<div class="home-slide dp-slide-<?php echo esc_attr($fit); ?>" <?php if($i)echo 'hidden'; ?> role="group" aria-roledescription="slide" aria-label="<?php echo (int)($i+1).' / '.(int)$count; ?>">
<?php dp_home_image($item_id,'dp_hi_image','',!$i); ?><div class="wrap slide-layout"><div class="slide-copy"><span class="eyebrow"><?php echo esc_html(dp_hv($item_id,'dp_hi_eyebrow')); ?></span><h2 class="slide-title"><?php echo esc_html($title); ?></h2><p><?php echo nl2br(esc_html(dp_hv($item_id,'dp_hi_body'))); ?></p><?php dp_home_link($item_id,'dp_hi_link'); ?></div></div></div>
<?php endforeach; ?>
<?php if ($count>1): ?><div class="slider-controls wrap"><button type="button" data-slide-prev aria-label="Banner trước">←</button><div class="slider-dots"><?php foreach($items as $i=>$item_id){ ?><button type="button" data-slide-to="<?php echo (int)$i; ?>" aria-label="Xem banner <?php echo (int)($i+1); ?>" aria-pressed="<?php echo $i===0?'true':'false'; ?>"></button><?php } ?></div><button type="button" data-slide-next aria-label="Banner tiếp theo">→</button><span class="slider-count" aria-live="polite">01 / <?php echo esc_html(str_pad((string)$count,2,'0',STR_PAD_LEFT)); ?></span></div><?php endif; ?>
</div>
<?php elseif ($layout === 'brands'): ?>
<div class="brand-band"><div class="wrap"><p><?php echo esc_html($heading); ?></p><div class="brand-names"><?php foreach($items as $item_id){ ?><div><?php dp_home_image($item_id,'dp_hi_image','brand-logo'); ?><span><?php echo esc_html(dp_hv($item_id,'dp_hi_heading') ?: get_the_title($item_id)); ?></span><?php dp_home_link($item_id,'dp_hi_link','brand-link'); ?></div><?php } ?></div></div></div>
<?php elseif ($layout === 'split' || $layout === 'accordion'): ?>
<div class="wrap section <?php echo $layout==='split'?'company-intro':'service-grid'; ?> <?php echo dp_hv($section_id,'dp_hs_side')==='left'?'dp-image-left':''; ?>"><div class="service-intro"><span class="eyebrow"><?php echo esc_html($eyebrow); ?></span><h2><?php echo esc_html($heading); ?></h2><div class="entry-content"><?php dp_home_rich($body); ?></div><?php dp_home_link($section_id,'dp_hs_link','btn outline'); ?>
<?php if($layout==='split' && $items){ ?><div class="values"><?php foreach($items as $item_id){ ?><span><?php echo esc_html(dp_hv($item_id,'dp_hi_heading') ?: get_the_title($item_id)); ?></span><?php } ?></div><?php } ?></div>
<?php if($layout==='split'){ dp_home_image($section_id,'dp_hs_image','intro-image'); } else { ?><div class="service-list service-accordion"><?php foreach($items as $i=>$item_id){ ?><details <?php if(!$i)echo 'open'; ?>><summary><span><?php echo esc_html(str_pad((string)($i+1),2,'0',STR_PAD_LEFT)); ?></span><?php echo esc_html(dp_hv($item_id,'dp_hi_heading') ?: get_the_title($item_id)); ?></summary><p><?php echo nl2br(esc_html(dp_hv($item_id,'dp_hi_body'))); ?></p><?php dp_home_image($item_id,'dp_hi_image'); dp_home_link($item_id,'dp_hi_link','btn outline'); ?></details><?php } ?></div><?php } ?></div>
<?php elseif ($layout === 'cta'): ?>
<div class="wrap section"><div class="cta"><div><span class="eyebrow"><?php echo esc_html($eyebrow); ?></span><h2><?php echo esc_html($heading); ?></h2><div class="entry-content"><?php dp_home_rich($body); ?></div></div><?php dp_home_link($section_id,'dp_hs_link'); ?></div></div>
<?php else: ?>
<div class="wrap section"><div class="home-heading"><span class="eyebrow"><?php echo esc_html($eyebrow); ?></span><h2><?php echo esc_html($heading); ?></h2><div class="entry-content"><?php dp_home_rich($body); ?></div></div>
<?php if($layout==='categories'): ?><div class="category-tiles">
<?php foreach($items as $item_id){
    $link=dp_hv($item_id,'dp_hi_link'); $url=is_array($link)?($link['url']??''):'';
    $term=absint(dp_hv($item_id,'dp_hi_category'));
    if(!$url && $term){$term_url=get_term_link($term,'dp_category');if(!is_wp_error($term_url))$url=$term_url;}
    $tag=$url?'a':'div';$blank=is_array($link)&&($link['target']??'')==='_blank';
    echo '<'.$tag.' class="category-tile"'.($url?' href="'.esc_url($url).'"':'').($url&&$blank?' target="_blank" rel="noopener noreferrer"':'').'>';
    dp_home_image($item_id,'dp_hi_image'); ?><div class="category-caption"><div><h3><?php echo esc_html(dp_hv($item_id,'dp_hi_heading') ?: get_the_title($item_id)); ?></h3><p><?php echo esc_html(dp_hv($item_id,'dp_hi_body')); ?></p></div><?php if($url){ ?><span aria-hidden="true">↗</span><?php } ?></div><?php echo '</'.$tag.'>'; } ?></div>
<?php elseif($layout==='cards'): ?><div class="grid"><?php foreach($items as $item_id){ ?><article class="card"><?php dp_home_image($item_id,'dp_hi_image','dp-card-image'); ?><div class="card-body"><span class="eyebrow"><?php echo esc_html(dp_hv($item_id,'dp_hi_eyebrow')); ?></span><h3><?php echo esc_html(dp_hv($item_id,'dp_hi_heading') ?: get_the_title($item_id)); ?></h3><p><?php echo nl2br(esc_html(dp_hv($item_id,'dp_hi_body'))); ?></p><?php dp_home_link($item_id,'dp_hi_link','btn outline'); ?></div></article><?php } ?></div>
<?php elseif($layout==='news'): ?><div class="grid"><?php while($news->have_posts()){$news->the_post();dp_news_card();} wp_reset_postdata(); ?></div>
<?php else: dp_home_image($section_id,'dp_hs_image','page-cover'); endif; ?>
<div class="home-more"><?php dp_home_link($section_id,'dp_hs_link','btn outline'); ?></div></div>
<?php endif; ?>
<?php if(dp_hv($section_id,'dp_hs_contact')): $contact_anchor=$contact_rendered?'lien-he-'.$section_id:'lien-he';$contact_rendered=true; ?>
<div class="wrap dp-home-contact"><div class="contact-panel" id="<?php echo esc_attr($contact_anchor); ?>"><h3><?php echo esc_html(dp_hv($home_id,'dp_home_contact_heading')); ?></h3><?php $phone=dp_hv($home_id,'dp_home_phone');$email=dp_hv($home_id,'dp_home_email');if($phone){ ?><p><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/','',$phone)); ?>"><?php echo esc_html($phone); ?></a></p><?php } if($email){ ?><p><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p><?php } ?><p><?php echo nl2br(esc_html(dp_hv($home_id,'dp_home_address'))); ?></p></div></div>
<?php endif; ?>
</section>
