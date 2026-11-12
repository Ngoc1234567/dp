<?php
// CLI only. Content transcribed from the company-supplied Profile DP.pdf.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require dirname(__DIR__).'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/media.php';
require_once ABSPATH.'wp-admin/includes/image.php';
update_option('blogname','Công ty TNHH Giải Pháp Kỹ Thuật DP');
update_option('blogdescription','Giải pháp kỹ thuật – Giá trị bền vững');
foreach(['company'=>'DP TECHNICAL SOLUTION','headline'=>'Giải pháp kỹ thuật. Giá trị bền vững.','intro'=>'Cung cấp thiết bị, vật tư công nghiệp và giải pháp kỹ thuật cho dầu khí, năng lượng, cơ khí và công nghiệp nặng. Đồng hành từ tư vấn, thiết kế đến lắp đặt và bảo trì.','phone'=>'+84 254 3554 123','email'=>'info@dpsolution.com.vn','address'=>'132/39 Nguyễn Tri Phương, Phường Tam Thắng, Thành phố Hồ Chí Minh, Việt Nam'] as $k=>$v) set_theme_mod('dp_'.$k,$v);
$groups=[
 'piping-instrumentation'=>'Bơm, van, đường ống & đo lường',
 'ups-batteries'=>'UPS & ắc quy',
 'motors-compressors'=>'Động cơ, máy phát & máy nén khí',
 'fire-safety'=>'Thiết bị PCCC',
];
$terms=[];foreach($groups as $slug=>$name){$term=get_term_by('slug',$slug,'dp_category');$result=$term?:wp_insert_term($name,'dp_category',['slug'=>$slug]);if(is_wp_error($result))throw new RuntimeException($result->get_error_message());$terms[$slug]=$term?$term->term_id:$result['term_id'];}
$rows=[
 ['bom-va-phu-tung','Bơm công nghiệp & phụ tùng','piping-instrumentation','pumps',8,'Bơm màng, bơm ly tâm, bơm bánh răng và bơm piston khí nén cùng phụ tùng phục vụ hệ thống công nghệ.','Chủng loại | Bơm màng, ly tâm, bánh răng, piston khí nén\nPhụ tùng | Chi tiết và phụ tùng cho từng dòng bơm\nCấu hình | Lựa chọn theo môi chất và yêu cầu vận hành'],
 ['van-cong-nghiep','Van công nghiệp','piping-instrumentation','valves',9,'Van vận hành bằng tay và van có bộ truyền động cho hệ thống đường ống công nghiệp.','Chủng loại | Van bi, van cổng, van bướm\nĐiều khiển | Vận hành tay hoặc bộ truyền động\nDòng thiết bị | Van đóng ngắt có bộ truyền động\nCấu hình | Lựa chọn theo yêu cầu hệ thống'],
 ['ong-va-phu-kien','Ống & phụ kiện đường ống','piping-instrumentation','piping',9,'Ống thép carbon, ống inox, mặt bích và phụ kiện kết nối cho các hệ thống đường ống công nghệ.','Ống | Thép carbon, inox\nPhụ kiện | Mặt bích, phụ kiện thép carbon, đầu nối ống inox\nKích thước & tiêu chuẩn | Tư vấn theo bản vẽ và yêu cầu kỹ thuật'],
 ['thiet-bi-do-luong','Thiết bị đo lường','piping-instrumentation','instrumentation',10,'Thiết bị đo lưu lượng, áp suất, nhiệt độ, cảm biến và thiết bị giao tiếp phục vụ đo lường công nghiệp.','Chức năng | Đo lưu lượng, áp suất, nhiệt độ\nThiết bị bổ trợ | Cảm biến, thiết bị giao tiếp\nThương hiệu trong hồ sơ | Siemens, ABB, Emerson, Yokogawa\nModel | Tư vấn theo yêu cầu ứng dụng'],
 ['he-thong-ups','Hệ thống UPS công nghiệp','ups-batteries','ups',11,'Giải pháp lưu điện công nghiệp với các dòng AEG PROTECT 8 S10 và Chloride CP70RC DC UPS được giới thiệu trong hồ sơ công ty.','Dòng sản phẩm trong hồ sơ | AEG PROTECT 8 S10; Chloride CP70RC\nPhân loại | UPS công nghiệp; DC UPS\nCấu hình | Tư vấn công suất, điện áp và thời gian lưu điện theo ứng dụng'],
 ['ac-quy-cong-nghiep','Ắc quy công nghiệp','ups-batteries','batteries',12,'Danh mục ắc quy cho UPS, trung tâm dữ liệu, hệ thống an ninh và chiếu sáng khẩn cấp.','Thương hiệu trong hồ sơ | FIAMM, Sebang, Saite\nỨng dụng | UPS, trung tâm dữ liệu, an ninh, chiếu sáng khẩn cấp\nCấu hình | Lựa chọn điện áp, dung lượng theo hệ thống'],
 ['dong-co-may-phat','Động cơ & máy phát điện','motors-compressors','generators',13,'Động cơ điện, động cơ diesel, máy phát điện và phụ tùng cho nhu cầu vận hành công nghiệp.','Danh mục | Động cơ điện, động cơ diesel, máy phát điện\nPhụ tùng | Tư vấn theo thiết bị đang sử dụng\nCông suất & model | Liên hệ để lựa chọn cấu hình'],
 ['may-nen-khi','Máy nén khí & phụ tùng','motors-compressors','compressors',14,'Cung cấp máy nén khí và phụ tùng cho hệ thống khí nén công nghiệp; hồ sơ giới thiệu Atlas Copco, Ingersoll Rand, Boge và GE.','Thương hiệu trong hồ sơ | Atlas Copco, Ingersoll Rand, Boge, GE\nDanh mục | Máy nén khí và phụ tùng\nCấu hình | Tư vấn theo lưu lượng, áp suất và điều kiện vận hành'],
 ['thiet-bi-pccc','Thiết bị phòng cháy chữa cháy','fire-safety','fire',15,'Thiết bị thở và thiết bị chữa cháy phục vụ nhu cầu an toàn trong môi trường công nghiệp.','Danh mục | Thiết bị thở, thiết bị chữa cháy\nThương hiệu trong hồ sơ | 3M Scott, Dräger, Kidde Fenwal, Fike\nLựa chọn thiết bị | Theo yêu cầu kỹ thuật của hệ thống'],
];
foreach($rows as [$slug,$title,$group,$asset,$page,$intro,$specs]){
 $existing=get_page_by_path($slug,OBJECT,'dp_product');
 $id=wp_insert_post(['ID'=>$existing?$existing->ID:0,'post_type'=>'dp_product','post_status'=>'publish','post_name'=>$slug,'post_title'=>$title,'post_excerpt'=>$intro,'post_content'=>'<p>'.esc_html($intro).'</p><p>DP hỗ trợ tư vấn lựa chọn thiết bị theo nhu cầu và điều kiện vận hành của từng dự án.</p>'],true);
 if(is_wp_error($id))throw new RuntimeException($id->get_error_message());
 wp_set_object_terms($id,[(int)$terms[$group]],'dp_category');
 foreach(['model'=>'','specs'=>str_replace('\\n',"\n",$specs),'pdf'=>'','seo_title'=>$title.' | DP Technical Solution','seo_description'=>$intro,'profile_page'=>$page] as $k=>$v)update_post_meta($id,'_dp_'.$k,$v);
 if(!has_post_thumbnail($id)){
  $source=get_theme_file_path('assets/profile/'.$asset.'.png');$temp=wp_tempnam($source);copy($source,$temp);
  $attachment=media_handle_sideload(['name'=>'dp-'.$asset.'.png','tmp_name'=>$temp],$id,$title);
  if(is_wp_error($attachment))throw new RuntimeException($attachment->get_error_message());
  set_post_thumbnail($id,$attachment);update_post_meta($attachment,'_wp_attachment_image_alt',$title.' — hình ảnh từ hồ sơ công ty DP');
 }
}
// Preserve earlier demo records as drafts rather than deleting them.
foreach(['dx-220','fl-30','gp-250'] as $slug){$p=get_page_by_path($slug,OBJECT,'dp_product');if($p)wp_update_post(['ID'=>$p->ID,'post_status'=>'draft']);}
flush_rewrite_rules();echo "Company identity updated; 9 catalogue entries imported; demo records retained as drafts.\n";
