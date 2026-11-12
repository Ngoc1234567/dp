<?php
defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', function () {
    if (is_front_page() || is_page_template('template-home.php')) wp_enqueue_style('dp-home-acf', get_theme_file_uri('assets/home-acf.css'), ['dp-pages'], (string) filemtime(get_theme_file_path('assets/home-acf.css')));
});

add_action('init', function () {
    foreach (['dp_home_section' => ['Khối trang chủ', 'Khối'], 'dp_home_item' => ['Nội dung trong khối', 'Nội dung']] as $type => $labels) {
        register_post_type($type, [
            'labels' => ['name' => $labels[0], 'singular_name' => $labels[1], 'add_new_item' => 'Thêm '.$labels[1], 'edit_item' => 'Sửa '.$labels[1]],
            'public' => false, 'show_ui' => true, 'show_in_menu' => $type === 'dp_home_section' ? true : 'edit.php?post_type=dp_home_section',
            'menu_icon' => 'dashicons-layout', 'supports' => ['title', 'revisions'], 'map_meta_cap' => true,
            'capability_type' => 'page', 'exclude_from_search' => true, 'rewrite' => false,
        ]);
    }
});

function dp_home_field($name, $label, $type = 'text', $extra = []) {
    $layouts = [
        'dp_hs_body' => ['categories','split','accordion','cards','cta','news','content'],
        'dp_hs_image' => ['split','content'], 'dp_hs_side' => ['split'],
        'dp_hs_link' => ['categories','split','accordion','cards','cta','news','content'],
        'dp_hs_items' => ['hero','categories','brands','split','accordion','cards'],
        'dp_hs_columns' => ['categories','cards','news'],
    ];
    if (isset($layouts[$name])) {
        $extra['conditional_logic'] = array_map(function ($layout) {
            return [['field'=>'field_dp_hs_layout','operator'=>'==','value'=>$layout]];
        }, $layouts[$name]);
    }
    return array_merge(['key' => 'field_'.$name, 'name' => $name, 'label' => $label, 'type' => $type], $extra);
}

add_action('acf/init', function () {
    $f = 'dp_home_field';
    acf_add_local_field_group([
        'key' => 'group_dp_home_page', 'title' => 'DP — Thiết kế trang chủ', 'position' => 'acf_after_title',
        'fields' => [
            dp_home_field('dp_home_inline', 'Nội dung trang chủ', 'dp_home_inline'),
            $f('dp_home_phone', 'Số điện thoại'), $f('dp_home_email', 'Email', 'email'), $f('dp_home_address', 'Địa chỉ', 'textarea', ['rows' => 2]),
            $f('dp_home_contact_heading', 'Tiêu đề phần thông tin liên hệ'),
            $f('dp_home_dock', 'Hiện nút liên hệ nổi', 'true_false', ['ui' => 1, 'default_value' => 1]),
            $f('dp_home_phone_label', 'Nhãn nút điện thoại', 'text', ['default_value' => 'Hotline']),
            $f('dp_home_contact_link', 'Nút liên hệ nổi', 'link', ['return_format' => 'array']),
        ],
        'location' => [[['param' => 'page_type', 'operator' => '==', 'value' => 'front_page']], [['param' => 'page_template', 'operator' => '==', 'value' => 'template-home.php']]],
        'hide_on_screen' => ['the_content'],
    ]);
    acf_add_local_field_group([
        'key' => 'group_dp_home_section', 'title' => 'Thiết lập khối',
        'fields' => [
            $f('dp_hs_enabled', 'Hiển thị khối', 'true_false', ['ui' => 1, 'default_value' => 1]),
            $f('dp_hs_layout', 'Kiểu bố cục', 'select', ['choices' => ['hero' => 'Banner nhiều slide', 'categories' => 'Danh mục / thẻ hình ảnh', 'brands' => 'Dải thương hiệu', 'split' => 'Ảnh và nội dung', 'accordion' => 'Dịch vụ đóng/mở', 'cards' => 'Thẻ giải pháp', 'cta' => 'Kêu gọi liên hệ', 'news' => 'Tin tức', 'content' => 'Nội dung tự do / shortcode'], 'default_value' => 'split']),
            $f('dp_hs_anchor', 'Mã liên kết trong trang', 'text', ['instructions' => 'Ví dụ: dich-vu, phu-tung, giai-phap. Không nhập dấu #. Mỗi khối dùng một mã riêng.']),
            $f('dp_hs_heading', 'Tiêu đề hiển thị'), $f('dp_hs_eyebrow', 'Dòng chữ nhỏ phía trên tiêu đề'),
            $f('dp_hs_body', 'Nội dung', 'wysiwyg', ['tabs' => 'all', 'media_upload' => 1, 'delay' => 1]),
            $f('dp_hs_image', 'Hình ảnh', 'image', ['return_format' => 'id', 'preview_size' => 'medium']),
            $f('dp_hs_side', 'Vị trí ảnh', 'select', ['choices' => ['right' => 'Bên phải', 'left' => 'Bên trái'], 'default_value' => 'right']),
            $f('dp_hs_background', 'Nền khối', 'select', ['choices' => ['white' => 'Trắng', 'muted' => 'Xám nhạt', 'dark' => 'Tối'], 'default_value' => 'white']),
            $f('dp_hs_link', 'Nút hành động', 'link', ['return_format' => 'array']),
            $f('dp_hs_items', 'Slide / thẻ / mục trong khối', 'relationship', ['post_type' => ['dp_home_item'], 'filters' => ['search'], 'return_format' => 'id', 'instructions' => 'Thêm mục tại Khối trang chủ → Nội dung trong khối. Chọn và kéo để sắp xếp; không giới hạn số mục.']),
            $f('dp_hs_columns', 'Số cột trên máy tính', 'select', ['choices' => [2 => '2 cột', 3 => '3 cột', 4 => '4 cột'], 'default_value' => 3]),
            $f('dp_hs_height', 'Chiều cao banner (px)', 'number', ['min' => 360, 'max' => 900, 'default_value' => 540, 'conditional_logic' => [[['field' => 'field_dp_hs_layout', 'operator' => '==', 'value' => 'hero']]]]),
            $f('dp_hs_count', 'Số bài tin tức', 'number', ['min' => 1, 'max' => 12, 'default_value' => 3, 'conditional_logic' => [[['field' => 'field_dp_hs_layout', 'operator' => '==', 'value' => 'news']]]]),
            $f('dp_hs_posts', 'Chọn bài tin tức cụ thể', 'relationship', ['post_type' => ['post'], 'filters' => ['search'], 'return_format' => 'id', 'instructions' => 'Để trống để lấy bài mới nhất. Kéo để thay đổi thứ tự.', 'conditional_logic' => [[['field' => 'field_dp_hs_layout', 'operator' => '==', 'value' => 'news']]]]),
            $f('dp_hs_contact', 'Hiện thông tin liên hệ dưới khối', 'true_false', ['ui' => 1]),
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'dp_home_section']]],
    ]);
    acf_add_local_field_group([
        'key' => 'group_dp_home_item', 'title' => 'Nội dung slide / thẻ',
        'fields' => [
            $f('dp_hi_enabled', 'Hiển thị mục', 'true_false', ['ui' => 1, 'default_value' => 1]),
            $f('dp_hi_heading', 'Tiêu đề hiển thị', 'text', ['instructions' => 'Để trống để dùng tên mục phía trên.']),
            $f('dp_hi_eyebrow', 'Nhãn nhỏ'), $f('dp_hi_body', 'Mô tả', 'textarea', ['rows' => 4]),
            $f('dp_hi_image', 'Ảnh / logo', 'image', ['return_format' => 'id', 'preview_size' => 'medium']),
            $f('dp_hi_fit', 'Cách hiển thị ảnh', 'select', ['choices' => ['cover' => 'Phủ kín', 'contain' => 'Hiện toàn bộ ảnh'], 'default_value' => 'contain']),
            $f('dp_hi_link', 'Liên kết / nút hành động', 'link', ['return_format' => 'array']),
            $f('dp_hi_category', 'Nhóm sản phẩm liên kết', 'taxonomy', ['taxonomy' => 'dp_category', 'field_type' => 'select', 'allow_null' => 1, 'return_format' => 'id', 'save_terms' => 0, 'load_terms' => 0, 'instructions' => 'Dùng cho thẻ danh mục. Liên kết tự cập nhật theo nhóm nếu không nhập liên kết riêng.']),
        ],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'dp_home_item']]],
    ]);
});

function dp_hv($id, $name, $default = '') {
    return metadata_exists('post', $id, $name) ? get_post_meta($id, $name, true) : $default;
}
function dp_home_ids($id, $field, $type) {
    return array_values(array_filter(array_unique(array_map('absint', (array) dp_hv($id, $field, []))), function ($item) use ($type) {
        return get_post_type($item) === $type && get_post_status($item) === 'publish' && dp_hv($item, $type === 'dp_home_section' ? 'dp_hs_enabled' : 'dp_hi_enabled', 1);
    }));
}
function dp_home_link($id, $field, $class = 'btn') {
    $link = dp_hv($id, $field);
    if (!is_array($link) || empty($link['url'])) return;
    $blank = ($link['target'] ?? '') === '_blank';
    printf('<a class="%s" href="%s"%s>%s</a>', esc_attr($class), esc_url($link['url']), $blank ? ' target="_blank" rel="noopener noreferrer"' : '', esc_html($link['title'] ?: 'Xem thêm'));
}
function dp_home_image($id, $field, $class = '', $eager = false) {
    $image = absint(dp_hv($id, $field));
    if ($field === 'dp_hi_image') $class .= dp_hv($id,'dp_hi_fit','contain') === 'cover' ? ' dp-fit-cover' : ' dp-fit-contain';
    if ($image) echo wp_get_attachment_image($image, 'full', false, ['class' => trim($class), 'loading' => $eager ? 'eager' : 'lazy']);
}
function dp_home_rich($html) {
    echo do_shortcode(wpautop(wp_kses_post($html)));
}
add_action('admin_notices', function () {
    if (!function_exists('acf_add_local_field_group') && current_user_can('edit_pages')) echo '<div class="notice notice-warning"><p>Bật Advanced Custom Fields để chỉnh các trường trang chủ. Nội dung đã lưu vẫn được hiển thị.</p></div>';
});

require_once __DIR__.'/home-editor.php';
