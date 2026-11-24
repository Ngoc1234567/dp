<?php
/** ACF-managed page content and shared identity; compatible with ACF Free. */
defined('ABSPATH') || exit;
function dp_settings_id() { return (int) get_option('dp_settings_post_id'); }
function dp_content($key, $id = 0) {
    if ($id && metadata_exists('post', $id, $key)) return get_post_meta($id, $key, true);
    static $defaults;
    if ($defaults === null) {
        $defaults = [];
        foreach (json_decode(file_get_contents(__DIR__.'/content-defaults.json'), true) as $items) foreach ($items as [$name,$value]) $defaults[$name]=$value;
    }
    return $defaults[$key] ?? '';
}
add_action('init', function () {
    register_post_type('dp_site_settings', ['labels'=>['name'=>'Cấu hình website DP','singular_name'=>'Cấu hình website'], 'public'=>false,'show_ui'=>true,'menu_icon'=>'dashicons-admin-settings','supports'=>['title'],'capability_type'=>'page','map_meta_cap'=>true,'capabilities'=>['create_posts'=>'do_not_allow']]);
});
add_action('admin_init', function () {
    if (!current_user_can('manage_options') || get_post(dp_settings_id())) return;
    $id=wp_insert_post(['post_type'=>'dp_site_settings','post_status'=>'publish','post_title'=>'Thông tin chung & giao diện DP']);
    if ($id && !is_wp_error($id)) update_option('dp_settings_post_id',$id);
});
add_action('acf/init', function () {
    $f='dp_home_field';
    $fields=[];
    foreach (['company'=>'Tên thương hiệu','phone'=>'Hotline','email'=>'Email nhận yêu cầu','address'=>'Địa chỉ trụ sở'] as $name=>$label) $fields[]=$f('dp_shared_'.$name,$label,$name==='email'?'email':($name==='phone'?'textarea':'text'),array_merge(['default_value'=>get_theme_mod('dp_'.$name)], $name==='phone'?['rows'=>3,'new_lines'=>'','instructions'=>'Nhập mỗi số hotline trên một dòng.']:[]));
    $fields[]=$f('dp_shared_logo','Logo','image',['return_format'=>'id']);
    $fields[]=$f('dp_shared_tagline','Khẩu hiệu dưới logo','text',['default_value'=>'TECHNICAL SOLUTIONS – SUSTAINABLE VALUE']);
    foreach (json_decode(file_get_contents(__DIR__.'/content-defaults.json'),true) as $file=>$items) {
        $copy=[];
        foreach ($items as [$name,$value]) $copy[]=$f($name,mb_substr($value,0,70),'textarea',['rows'=>2,'default_value'=>$value]);
        if ($file==='template-contact.php') {
            $copy[]=$f('dp_contact_parts_image','Ảnh khối phụ tùng','image',['return_format'=>'id']);
            $copy[]=$f('dp_contact_parts_link','Nút khối phụ tùng','link',['return_format'=>'array']);
            $copy[]=$f('dp_contact_map_address','Địa chỉ tìm trên bản đồ','text',['instructions'=>'Để trống để dùng địa chỉ trụ sở trong Cấu hình website DP.']);
            $copy[]=$f('dp_contact_show_map','Hiện khối trụ sở / bản đồ','true_false',['ui'=>1,'default_value'=>1]);
            $copy[]=$f('dp_contact_show_parts','Hiện khối phụ tùng','true_false',['ui'=>1,'default_value'=>1]);
            acf_add_local_field_group(['key'=>'group_dp_contact_content','title'=>'DP — Nội dung trang liên hệ','fields'=>$copy,'location'=>[[['param'=>'page_template','operator'=>'==','value'=>'template-contact.php']]]]);
        } else {
            $labels = ['footer.php'=>'Footer', 'archive-dp_product.php'=>'Trang thiết bị (/thiet-bi/)', 'single-dp_product.php'=>'Chi tiết thiết bị', 'template-products.php'=>'Nhãn giao diện sản phẩm'];
            $fields=array_merge($fields,[$f('dp_tab_'.sanitize_key($file),$labels[$file] ?? $file,'tab')],$copy);
        }
    }
    acf_add_local_field_group(['key'=>'group_dp_shared','title'=>'DP — Cấu hình dùng chung','fields'=>$fields,'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'dp_site_settings']]]]);
    acf_add_local_field_group(['key'=>'group_dp_page_blocks','title'=>'DP — Các khối nội dung bổ sung','fields'=>[
        $f('dp_page_sections','Các khối hiển thị cuối trang','relationship',['post_type'=>['dp_home_section'],'return_format'=>'id','instructions'=>'Chọn và kéo để sắp xếp. Tạo nội dung tại Khối trang chủ; có thể dùng chung khối giữa nhiều trang.']),
    ],'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'page'],['param'=>'page_template','operator'=>'!=','value'=>'template-home.php'],['param'=>'page_type','operator'=>'!=','value'=>'front_page']]]]);
    $product=[];
    foreach (['_dp_model'=>['Model','text'],'_dp_specs'=>['Thông số (mỗi dòng: Tên | Giá trị)','textarea'],'_dp_pdf'=>['Liên kết tài liệu PDF','url']] as $name=>[$label,$type]) $product[]=$f($name,$label,$type);
    acf_add_local_field_group(['key'=>'group_dp_product_fields','title'=>'DP — Thông tin sản phẩm','fields'=>$product,'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'dp_product']]]]);
});
foreach (['company','phone','email','address'] as $key) add_filter('theme_mod_dp_'.$key, function ($fallback) use ($key) {
    $id=dp_settings_id(); return $id && metadata_exists('post',$id,'dp_shared_'.$key) ? get_post_meta($id,'dp_shared_'.$key,true) : $fallback;
});
function dp_page_sections($id = 0) {
    $id=$id ?: get_queried_object_id(); $home_id=(int)get_option('page_on_front'); $used_anchors=[];
    foreach (dp_home_ids($id,'dp_page_sections','dp_home_section') as $section_id) {
        echo '<div class="dp-home">'; require __DIR__.'/home-section.php'; echo '</div>';
    }
}
add_action('wp_enqueue_scripts',function(){if(is_page())wp_enqueue_style('dp-home-acf',get_theme_file_uri('assets/home-acf.css'),['dp-pages'],(string)filemtime(get_theme_file_path('assets/home-acf.css')));});

add_action('after_setup_theme',function(){register_nav_menus(['dp_primary'=>'DP — Menu chính','dp_footer'=>'DP — Liên kết footer']);});
add_action('acf/init',function(){
    acf_add_local_field_group(['key'=>'group_dp_page_intro','title'=>'DP — Tiêu đề trang','fields'=>[
        dp_home_field('dp_page_eyebrow','Dòng nhãn phía trên tiêu đề'),
        dp_home_field('dp_page_title','Tiêu đề hiển thị (để trống dùng tên trang)'),
    ],'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'page']]]]);
});

add_action('acf/init',function(){
    $fields=[];
    foreach(['address'=>'Address','phone'=>'Hotline','email'=>'Email','service'=>'Technical advice'] as $key=>$label) $fields[]=dp_home_field('dp_card_'.$key.'_label','Nhãn thẻ '.$label,'text',['default_value'=>$label]);
    $fields[]=dp_home_field('dp_card_service_text','Nội dung thẻ tư vấn','text',['default_value'=>'Equipment · Spare parts · Services']);
    acf_add_local_field_group(['key'=>'group_dp_contact_cards','title'=>'DP — Thẻ thông tin liên hệ','fields'=>$fields,'location'=>[[['param'=>'page_template','operator'=>'==','value'=>'template-contact.php']]]]);
});

add_action('acf/init',function(){
    acf_add_local_field_group(['key'=>'group_dp_equipment_archive','title'=>'DP — Trang thiết bị (/thiet-bi/)','fields'=>[
        dp_home_field('dp_equipment_sections','Các khối cuối trang thiết bị','relationship',['post_type'=>['dp_home_section'],'return_format'=>'id','instructions'=>'Chọn và kéo để sắp xếp. Các khối này hiển thị sau danh sách thiết bị.']),
        dp_home_field('dp_equipment_contact','Hiện khối Kết nối với DP','true_false',['ui'=>1,'default_value'=>1]),
    ],'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'dp_site_settings']]]]);
});
function dp_equipment_sections(){
    $home_id=(int)get_option('page_on_front'); $used_anchors=[];
    foreach(dp_home_ids(dp_catalogue_page_id(),'dp_equipment_sections','dp_home_section') as $section_id){echo '<div class="dp-home dp-product-contact">';require __DIR__.'/home-section.php';echo '</div>';}
    if(dp_hv(dp_catalogue_page_id(),'dp_equipment_contact',1))dp_product_contact_cta();
}

/** The editable Products page supplies content for the /thiet-bi/ archive. */
function dp_catalogue_page_id(){
    $id=(int)get_option('dp_catalogue_settings_id');
    if($id && get_post_type($id)==='dp_catalogue_config' && get_post_status($id)!=='trash')return $id;
    $existing=get_posts(['post_type'=>'dp_catalogue_config','post_status'=>['publish','draft','private'],'posts_per_page'=>1,'fields'=>'ids']);
    if($existing){update_option('dp_catalogue_settings_id',(int)$existing[0]);return (int)$existing[0];}
    return 0;
}
add_action('init',function(){
    register_post_type('dp_catalogue_config',['labels'=>['name'=>'Nội dung trang thiết bị','singular_name'=>'Nội dung trang thiết bị'],'public'=>false,'show_ui'=>true,'show_in_menu'=>false,'supports'=>['title'],'capability_type'=>'page','map_meta_cap'=>true,'capabilities'=>['create_posts'=>'do_not_allow']]);
    if(is_admin() && current_user_can('manage_options') && !dp_catalogue_page_id()){
        $id=wp_insert_post(['post_type'=>'dp_catalogue_config','post_status'=>'publish','post_title'=>'Nội dung trang thiết bị'],true);
        if(!is_wp_error($id))update_option('dp_catalogue_settings_id',$id);
    }
});
add_action('admin_menu',function(){
    $id=dp_catalogue_page_id();
    if($id)add_submenu_page('edit.php?post_type=dp_product','Nội dung trang thiết bị','Nội dung trang thiết bị','edit_pages','post.php?post='.$id.'&action=edit');
});
add_action('acf/init',function(){
    $id=dp_catalogue_page_id();if(!$id)return;
    $fields=[];
    $labels=['Dòng nhãn phía trên tiêu đề','Tiêu đề dòng 1','Tiêu đề dòng 2','Mô tả danh mục'];
    foreach([3,4,5,6] as $i=>$number){$key='dp_copy_archive_dp_product_'.$number;$fields[]=dp_home_field($key,$labels[$i],'textarea',['rows'=>2,'default_value'=>dp_content($key,dp_settings_id())]);}
    $fields[]=dp_home_field('dp_catalogue_products','Thiết bị hiển thị','relationship',['post_type'=>['dp_product'],'return_format'=>'id','instructions'=>'Để trống để hiện toàn bộ thiết bị. Chọn và kéo để sắp xếp danh sách riêng.']);
    $fields[]=dp_home_field('dp_catalogue_per_page','Số thiết bị mỗi trang','number',['min'=>1,'max'=>48,'default_value'=>9]);
    $fields[]=dp_home_field('dp_equipment_sections','Khối nội dung cuối trang','relationship',['post_type'=>['dp_home_section'],'return_format'=>'id']);
    $fields[]=dp_home_field('dp_equipment_contact','Hiện Kết nối với DP','true_false',['ui'=>1,'default_value'=>1]);
    acf_add_local_field_group(['key'=>'group_dp_catalogue_page','title'=>'DP — Nhập liệu trang thiết bị (/thiet-bi/)','fields'=>$fields,'location'=>[[['param'=>'post_type','operator'=>'==','value'=>'dp_catalogue_config']]]]);
});
add_action('pre_get_posts',function($query){
    if(is_admin() || !$query->is_main_query() || !$query->is_post_type_archive('dp_product'))return;
    $id=dp_catalogue_page_id();
    $query->set('posts_per_page',max(1,min(48,(int)dp_hv($id,'dp_catalogue_per_page',9))));
    $products=array_filter(array_map('absint',(array)dp_hv($id,'dp_catalogue_products',[])));
    if($products){$query->set('post__in',$products);$query->set('orderby','post__in');}
});
