<?php
// Run from CLI: php tools/seed-demo.php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require dirname(__DIR__).'/wp-load.php';
$rows=[
 ['Máy đào bánh xích DX-220','DX-220','Thiết bị công trình','Giải pháp đào xúc cho các dự án hạ tầng và san lấp. Thiết bị mẫu để minh họa cấu trúc catalogue.','Khối lượng vận hành | 22.000 kg\nCông suất động cơ | 115 kW\nDung tích gầu | 1,0 m³\nKiểu di chuyển | Bánh xích'],
 ['Xe nâng điện FL-30','FL-30','Nâng hạ & kho vận','Giải pháp nâng hạ cho kho hàng và nhà máy. Nội dung sản phẩm minh họa, chưa phải cấu hình thương mại.','Tải trọng nâng | 3.000 kg\nChiều cao nâng | 3.000 mm\nNguồn năng lượng | Điện\nỨng dụng | Kho hàng'],
 ['Máy phát điện GP-250','GP-250','Năng lượng','Nguồn điện dự phòng cho hoạt động sản xuất. Sản phẩm mẫu để duyệt bố cục và luồng tư vấn.','Công suất dự phòng | 250 kVA\nTần số | 50 Hz\nĐiện áp | 400 V\nKiểu vỏ | Cách âm'],
];
foreach($rows as $row){
 $slug=sanitize_title($row[1]); if(get_page_by_path($slug,OBJECT,'dp_product')) continue;
 $term=term_exists($row[2],'dp_category'); if(!$term)$term=wp_insert_term($row[2],'dp_category');
 $id=wp_insert_post(['post_type'=>'dp_product','post_status'=>'publish','post_title'=>$row[0],'post_name'=>$slug,'post_excerpt'=>$row[3],'post_content'=>'<p>'.$row[3].'</p>'],true);
 if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 wp_set_object_terms($id,[(int)$term['term_id']],'dp_category');
 update_post_meta($id,'_dp_model',$row[1]);update_post_meta($id,'_dp_specs',str_replace('\\n',"\n",$row[4]));
 update_post_meta($id,'_dp_seo_title',$row[0].' | DP Equipment');update_post_meta($id,'_dp_seo_description',$row[3]);
}
flush_rewrite_rules();
echo "Demo catalogue ready.\n";
