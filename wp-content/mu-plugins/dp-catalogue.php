<?php
/** Plugin Name: DP Catalogue
 * Description: Quản lý sản phẩm, thông số và SEO độc lập với theme.
 */
defined('ABSPATH') || exit;
add_action('init', function () {
 register_post_type('dp_product',['labels'=>['name'=>'Thiết bị','singular_name'=>'Thiết bị','add_new_item'=>'Thêm thiết bị','edit_item'=>'Sửa thiết bị'],'public'=>true,'has_archive'=>'thiet-bi','rewrite'=>['slug'=>'thiet-bi'],'show_in_rest'=>true,'menu_icon'=>'dashicons-hammer','supports'=>['title','editor','excerpt','thumbnail','revisions']]);
 register_taxonomy('dp_category','dp_product',['labels'=>['name'=>'Nhóm thiết bị','singular_name'=>'Nhóm thiết bị'],'public'=>true,'hierarchical'=>true,'show_in_rest'=>true,'rewrite'=>['slug'=>'nhom-thiet-bi']]);
});
add_action('add_meta_boxes',function(){add_meta_box('dp_fields','Thông tin thiết bị & SEO',function($post){
 wp_nonce_field('dp_save','dp_nonce');
 foreach(['model'=>'Mã model','specs'=>'Thông số: mỗi dòng Tên thông số | Giá trị','pdf'=>'URL tài liệu PDF (để trống nếu chưa có)','seo_title'=>'Tiêu đề SEO','seo_description'=>'Mô tả SEO'] as $key=>$label){echo '<p><label for="dp_'.esc_attr($key).'"><strong>'.esc_html($label).'</strong></label></p><textarea style="width:100%" rows="'.($key==='specs'?'7':'2').'" id="dp_'.esc_attr($key).'" name="dp_'.esc_attr($key).'">'.esc_textarea(get_post_meta($post->ID,'_dp_'.$key,true)).'</textarea>';}
 },'dp_product');});
add_action('save_post_dp_product',function($id){
 if(defined('DOING_AUTOSAVE')&&DOING_AUTOSAVE || wp_is_post_revision($id) || !isset($_POST['dp_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['dp_nonce'])),'dp_save') || !current_user_can('edit_post',$id)) return;
 foreach(['model','specs','pdf','seo_title','seo_description'] as $key){if(isset($_POST['dp_'.$key]) && is_string($_POST['dp_'.$key])){$value=wp_unslash($_POST['dp_'.$key]);update_post_meta($id,'_dp_'.$key,$key==='pdf'?esc_url_raw($value):sanitize_textarea_field($value));}}
});
add_filter('pre_get_document_title',function($title){if(is_singular('dp_product')){$custom=get_post_meta(get_queried_object_id(),'_dp_seo_title',true);if($custom)return $custom;}return $title;});
add_action('wp_head',function(){if(is_singular('dp_product')){$desc=get_post_meta(get_queried_object_id(),'_dp_seo_description',true);if($desc)echo '<meta name="description" content="'.esc_attr($desc).'">'."\n";}});
