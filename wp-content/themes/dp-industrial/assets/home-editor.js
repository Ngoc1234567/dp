(function ($) {
  'use strict';
  const labels = {hero:'Banner', categories:'Danh mục', brands:'Thương hiệu', split:'Ảnh & nội dung', accordion:'Dịch vụ', cards:'Giải pháp', cta:'Liên hệ', news:'Tin tức', content:'Nội dung tự do'};
  const visibility = {
    dp_hs_body:['categories','split','accordion','cards','cta','news','content'],
    dp_hs_image:['split','content'], dp_hs_side:['split'],
    dp_hs_link:['categories','split','accordion','cards','cta','news','content'],
    dp_hs_columns:['categories','cards','news'], dp_hs_height:['hero'],
    dp_hs_count:['news'], dp_hs_posts:['news']
  };
  function refresh(section) {
    const fields = section.children('.dp-inline-fields');
    const layout = fields.find('[data-name="dp_hs_layout"] select').val() || 'split';
    Object.entries(visibility).forEach(([name, choices]) => fields.children('[data-name="'+name+'"]').toggle(choices.includes(layout)));
    section.children('.dp-inline-children').toggle(['hero','categories','brands','split','accordion','cards'].includes(layout));
    const title = fields.find('[data-name="dp_hs_heading"] input').val();
    section.children('summary').find('.dp-row-title').text((labels[layout] || 'Khối') + (title ? ' — '+title : ' mới'));
  }
  function init(root) {
    root.find('.dp-inline-sections,.dp-inline-items').each(function () {
      if (!$(this).hasClass('ui-sortable')) $(this).sortable({items:'> .dp-inline-row',handle:'> summary .dp-drag',placeholder:'dp-sort-placeholder',tolerance:'pointer',stop:()=>root.trigger('change')});
    });
    root.find('.dp-inline-section').each(function () {refresh($(this));});
  }
  $(function () {
    $('.dp-home-editor').each(function () {
      const root = $(this);init(root);
      root.on('change input', '.dp-inline-section > .dp-inline-fields input,.dp-inline-section > .dp-inline-fields select', function(){refresh($(this).closest('.dp-inline-section'));});
      root.on('input', '[data-name="dp_hi_heading"] input',function(){ $(this).closest('.dp-inline-item').children('summary').find('.dp-row-title').text(this.value || 'Mục mới'); });
      root.on('click','.dp-row-remove,.dp-row-up,.dp-row-down',function(event){
        event.preventDefault();event.stopPropagation();const row=$(this).closest('.dp-inline-row');
        if($(this).hasClass('dp-row-remove')) {
          if(!window.confirm('Xóa mục này khỏi trang chủ? Bấm Cập nhật để lưu thay đổi.'))return;
          if(window.acf)acf.doAction('remove',row);row.remove();
        } else if($(this).hasClass('dp-row-up')) {const prev=row.prev('.dp-inline-row');if(prev.length)row.insertBefore(prev);}
        else {const next=row.next('.dp-inline-row');if(next.length)row.insertAfter(next);}
        root.trigger('change');
      });
      root.on('click','.dp-add-section,.dp-add-item',function(){
        const button=$(this),isItem=button.hasClass('dp-add-item'),section=button.closest('.dp-inline-section');
        const key='n'+Date.now()+Math.random().toString(36).slice(2,8);
        const status=root.find('.dp-editor-status');button.prop('disabled',true);status.text('Đang thêm…');
        $.post(window.ajaxurl,{action:'dp_home_editor_row',nonce:root.attr('data-nonce'),kind:isItem?'item':'section',key,parent:isItem?section.attr('data-key'):''})
          .done(function(response){
            if(!response.success){status.text('Không thêm được. Hãy tải lại trang và thử lại.');return;}
            const row=$(response.data);const list=isItem?section.children('.dp-inline-children').children('.dp-inline-items'):root.children('.dp-inline-sections');
            list.append(row);if(window.acf)acf.doAction('append',row);init(root);status.text('Đã thêm. Bấm Cập nhật để lưu.');root.trigger('change');
          }).fail(()=>status.text('Không kết nối được. Hãy thử lại.')).always(()=>button.prop('disabled',false));
      });
    });
  });
})(jQuery);
