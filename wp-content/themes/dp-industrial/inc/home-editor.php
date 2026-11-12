<?php
defined('ABSPATH') || exit;

function dp_inline_fields($group, $post_id, $prefix, $skip = []) {
    $fields = acf_get_fields($group);
    foreach ($fields ?: [] as $field) {
        if (in_array($field['name'], $skip, true)) continue;
        $field['prefix'] = $prefix;
        $field['conditional_logic'] = 0;
        $field['value'] = $post_id ? get_field($field['key'], $post_id, false) : ($field['default_value'] ?? null);
        if ($field['name']==='dp_hi_heading' && !$field['value'] && $post_id) $field['value']=get_the_title($post_id);
        acf_render_field_wrap($field);
    }
}
function dp_inline_row($kind, $key, $id, $parent = '') {
    $base = 'acf[field_dp_home_inline][sections]';
    $prefix = $kind === 'section' ? $base.'['.$key.']' : $base.'['.$parent.'][items]['.$key.']';
    $title = $id ? get_the_title($id) : ($kind==='section'?'Khối mới':'Mục mới');
    ?>
    <details class="dp-inline-row dp-inline-<?php echo esc_attr($kind); ?>" data-key="<?php echo esc_attr($key); ?>" <?php if(!$id)echo 'open'; ?>>
    <summary><span class="dp-drag" title="Kéo để sắp xếp">☰</span><strong class="dp-row-title"><?php echo esc_html($title); ?></strong><span class="dp-row-actions"><button type="button" class="button dp-row-up" aria-label="Chuyển lên">↑</button><button type="button" class="button dp-row-down" aria-label="Chuyển xuống">↓</button><button type="button" class="button dp-row-remove">Xóa</button></span></summary>
    <input type="hidden" name="<?php echo esc_attr($prefix.'[id]'); ?>" value="<?php echo (int)$id; ?>">
    <div class="acf-fields dp-inline-fields">
    <?php dp_inline_fields($kind==='section'?'group_dp_home_section':'group_dp_home_item', $id, $prefix.'[fields]', $kind==='section'?['dp_hs_items']:[]); ?>
    </div>
    <?php if($kind==='section'): ?>
    <div class="dp-inline-children"><h4>Slide / thẻ / mục trong khối</h4><div class="dp-inline-items">
    <?php foreach((array)dp_hv($id,'dp_hs_items',[]) as $item){$item=absint($item);if(get_post_type($item)==='dp_home_item')dp_inline_row('item','i'.$item,$item,$key);} ?>
    </div><button type="button" class="button dp-add-item">+ Thêm slide / thẻ</button></div>
    <?php endif; ?>
    </details>
    <?php
}
function dp_register_inline_acf() {
    if (!class_exists('acf_field') || class_exists('DP_Home_Inline_Field')) return;
    class DP_Home_Inline_Field extends acf_field {
        public function initialize() { $this->name='dp_home_inline'; $this->label='DP — Trang chủ trực tiếp'; $this->category='layout'; }
        public function render_field($field) {
            $id=absint(acf_get_form_data('post_id'));
            if(!$id)$id=absint(get_the_ID());
            echo '<div class="dp-home-editor" data-nonce="'.esc_attr(wp_create_nonce('dp_home_editor')).'"><p>Sửa mọi khối ngay bên dưới. Bấm tên khối để mở, kéo ☰ hoặc dùng ↑ ↓ để đổi thứ tự. Bấm <strong>Cập nhật</strong> trang để lưu tất cả.</p>';
            echo '<input type="hidden" name="acf[field_dp_home_inline][present]" value="1"><div class="dp-inline-sections">';
            foreach((array)dp_hv($id,'dp_home_sections',[]) as $section){$section=absint($section);if(get_post_type($section)==='dp_home_section')dp_inline_row('section','s'.$section,$section);}
            echo '</div><button type="button" class="button button-primary dp-add-section">+ Thêm khối trang chủ</button><p class="dp-editor-status" role="status"></p><input type="hidden" name="acf[field_dp_home_inline][complete]" value="1"></div>';
        }
        public function validate_value($valid,$value,$field,$input) {
            if(!is_array($value)||empty($value['present'])||empty($value['complete']))return 'Dữ liệu trang chủ chưa đầy đủ. Không lưu để tránh mất nội dung; hãy tải lại trang hoặc kiểm tra giới hạn max_input_vars của PHP.';
            return $valid;
        }
        public function update_value($value,$post_id,$field) {
            if(!is_array($value)||empty($value['present'])||empty($value['complete'])||!is_numeric($post_id)||!current_user_can('edit_post',(int)$post_id)||wp_is_post_revision($post_id))return null;
            $allowed_sections=array_map('absint',(array)dp_hv($post_id,'dp_home_sections',[]));
            $result=[];
            foreach((array)($value['sections']??[]) as $section){
                if(!is_array($section))continue;
                $old=absint($section['id']??0);
                $id=in_array($old,$allowed_sections,true)&&get_post_type($old)==='dp_home_section'?$old:0;
                $allowed_items=$id?array_map('absint',(array)dp_hv($id,'dp_hs_items',[])):[];
                $id=dp_inline_save_record('dp_home_section',$id,(array)($section['fields']??[]));
                if(!$id)continue;
                $item_ids=[];
                foreach((array)($section['items']??[]) as $item){
                    if(!is_array($item))continue;
                    $old=absint($item['id']??0);
                    $item_id=in_array($old,$allowed_items,true)&&get_post_type($old)==='dp_home_item'?$old:0;
                    $item_id=dp_inline_save_record('dp_home_item',$item_id,(array)($item['fields']??[]));
                    if($item_id)$item_ids[]=$item_id;
                }
                update_field('field_dp_hs_items',$item_ids,$id);$result[]=$id;
            }
            update_field('field_dp_home_sections',$result,$post_id);
            return null;
        }
    }
    acf_register_field_type('DP_Home_Inline_Field');
}
add_action('acf/include_field_types','dp_register_inline_acf');
if(did_action('acf/include_field_types'))dp_register_inline_acf();

function dp_inline_save_record($type,$id,$values) {
    $group=$type==='dp_home_section'?'group_dp_home_section':'group_dp_home_item';
    $title_key=$type==='dp_home_section'?'field_dp_hs_heading':'field_dp_hi_heading';
    $title=sanitize_text_field($values[$title_key]??'');
    $args=['post_type'=>$type,'post_status'=>'publish','post_title'=>$title?:'Nội dung trang chủ'];
    if($id)$args['ID']=$id;
    $id=wp_insert_post($args,true);if(is_wp_error($id))return 0;
    foreach(acf_get_fields($group)?:[] as $f){
        if($f['name']==='dp_hs_items')continue;
        if(!array_key_exists($f['key'],$values))continue;
        $v=$values[$f['key']];
        switch($f['type']) {
            case 'wysiwyg': $v=wp_kses_post(is_string($v)?$v:'');break;
            case 'textarea': $v=sanitize_textarea_field(is_string($v)?$v:'');break;
            case 'link': $v=is_array($v)?['url'=>esc_url_raw($v['url']??''),'title'=>sanitize_text_field($v['title']??''),'target'=>($v['target']??'')==='_blank'?'_blank':'']:[];break;
            case 'relationship': $v=array_values(array_filter(array_map('absint',(array)$v)));break;
            case 'image': case 'taxonomy': case 'number': $v=absint($v);break;
            case 'true_false': $v=empty($v)?0:1;break;
            case 'select': $v=is_scalar($v)&&isset($f['choices'][$v])?$v:($f['default_value']??'');break;
            default: $v=sanitize_text_field(is_string($v)?$v:'');
        }
        update_field($f['key'],$v,$id);
    }
    return $id;
}

add_action('wp_ajax_dp_home_editor_row',function(){
    check_ajax_referer('dp_home_editor','nonce');
    if(!current_user_can('edit_pages'))wp_send_json_error('Bạn không có quyền sửa trang.',403);
    $kind=($_POST['kind']??'')==='item'?'item':'section';
    $key=sanitize_key(wp_unslash($_POST['key']??''));$parent=sanitize_key(wp_unslash($_POST['parent']??''));
    if(!$key||($kind==='item'&&!$parent))wp_send_json_error('Thiếu thông tin mục.',400);
    ob_start();dp_inline_row($kind,$key,0,$parent);wp_send_json_success(ob_get_clean());
});
add_action('admin_enqueue_scripts',function(){
    $screen=get_current_screen();if(!$screen||$screen->base!=='post'||$screen->post_type!=='page')return;
    wp_enqueue_media();
    wp_enqueue_script('dp-home-editor',get_theme_file_uri('assets/home-editor.js'),['jquery','jquery-ui-sortable','acf-input'],(string)filemtime(get_theme_file_path('assets/home-editor.js')),true);
    wp_enqueue_style('dp-home-editor',get_theme_file_uri('assets/home-editor.css'),[],(string)filemtime(get_theme_file_path('assets/home-editor.css')));
});
add_action('add_meta_boxes',function(){remove_meta_box('dp_home_quick_edit','page','normal');},99);
add_action('admin_menu',function(){remove_menu_page('edit.php?post_type=dp_home_section');},99);
