<?php
defined('ABSPATH') || exit;
if(get_query_var('post_type')==='dp_product') {
    require get_template_directory().'/archive-dp_product.php';
    return;
}
get_header(); ?>
<main id="main"><?php dp_page_heading('Search results: '.get_search_query()); ?><section class="wrap section"><?php get_search_form(); ?><div class="grid"><?php if(have_posts()){while(have_posts()){the_post();if(get_post_type()==='dp_product')dp_card();else dp_news_card();}}else{ ?><p class="empty">No results found. Please try another keyword.</p><?php } ?></div><?php the_posts_pagination(); ?></section></main><?php get_footer(); ?>
