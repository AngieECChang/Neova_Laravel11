function RemoveRow2(id) {
  $('#removedtwo' + id).val("1");
  $('#rowtwo' + id).remove();
}

$(function () {
  let count_relative = 0;
  // 建立主 table 結構（含標題列）
  $('#form01_1_Title').appendDom([{
    tagName: 'table', id: 'relativeTable', className: 'table table-bordered', childNodes: [{
      tagName: 'thead', childNodes: [{
        tagName: 'tr', childNodes: [
          { tagName: 'th', className:'table-warning', style: 'width:110px;', innerHTML: '成員姓名' },
          { tagName: 'th', className:'table-warning', style: 'width:120px;', innerHTML: '關係' },
          { tagName: 'th', className:'table-warning', style: 'width:120px;', innerHTML: '主要照顧時間' },
          { tagName: 'th', className:'table-warning', style: 'width:120px;', innerHTML: '電話1' },
          { tagName: 'th', className:'table-warning', style: 'width:120px;', innerHTML: '電話2' },
          { tagName: 'th', className:'table-warning', style: 'width:120px;', innerHTML: '電話3' },
          { tagName: 'th', className:'table-warning', style: 'width:200px;', innerHTML: '備註' },
          { tagName: 'th', className:'table-warning', style: 'width:60px;', innerHTML: '功能' }
        ]
      }]
    }, {
      tagName: 'tbody', id: 'relativeBody'
    }]
  }]);
  $('#form01_1_addShow').hide();
  $('#form01_1_addFile').click(function () {
    count_relative++;
// 抓取 Blade 預產生 option html
    let selectHTML = $('#relative-template').html();
    let row = [{
      tagName: 'tr', id: 'rowtwo' + count_relative, childNodes: [
        { tagName: 'td', style: 'text-align:center;', childNodes: [
          { tagName: 'input', name: 'form01_1RelativesName[]', className: 'form-control mx-auto d-block', style: 'width:110px;text-align:center;' }
        ] },
        { tagName: 'td', style: 'text-align:center;', childNodes: [
          {
             tagName: 'select', name: 'form01_1Relationship[]', className: 'form-select mx-auto d-block form01_1Relationship', style: 'width:120px;', innerHTML: selectHTML
          },
          { tagName: 'input', type: 'text', className: 'form-control mt-1 OtherRelationshipInput', name: 'form01_1RelationshipOther[]', placeholder: '其他關係', style: 'display:none;' }
        ] },
        { tagName: 'td', style: 'text-align:center;', childNodes: [
          {
            tagName: 'select', name: 'form01_1CareTime[]', className: 'form-select mx-auto d-block form01_1CareTime', style: 'width:80px;', childNodes: [
              { tagName: 'option', value: '', innerHTML: '請選擇' },
              { tagName: 'option', value: '白天', innerHTML: '白天' },
              { tagName: 'option', value: '晚上', innerHTML: '晚上' },
              { tagName: 'option', value: '其他', innerHTML: '其他' }
            ]
          },
          { tagName: 'input', type: 'text', className: 'form-control mt-1 OtherCareTimeInput', name: 'form01_1CareTimeOther[]', placeholder: '其他時間', style: 'display:none;' }
        ] },
        { tagName: 'td', style: 'text-align:center;', childNodes: [
          { tagName: 'input', name: 'form01_1Tel1[]', className: 'form-control mx-auto d-block', placeholder: '電話1', required: true },
          { tagName: 'input', name: 'form01_1Tel1Remark[]', className: 'form-control mt-1', placeholder: '說明/聯絡時間' }
        ] },
        { tagName: 'td', style: 'text-align:center;', childNodes: [
          { tagName: 'input', name: 'form01_1Tel2[]', className: 'form-control mx-auto d-block', placeholder: '電話2' },
          { tagName: 'input', name: 'form01_1Tel2Remark[]', className: 'form-control mt-1', placeholder: '說明/聯絡時間' }
        ] },
        { tagName: 'td', style: 'text-align:center;', childNodes: [
          { tagName: 'input', name: 'form01_1Tel3[]', className: 'form-control', placeholder: '電話3' },
          { tagName: 'input', name: 'form01_1Tel3Remark[]', className: 'form-control mt-1', placeholder: '說明/聯絡時間' }
        ] },
        { tagName: 'td', style: 'text-align:center;', childNodes: [
          { tagName: 'textarea', name: 'form01_1RelativesRemark[]', className: 'form-control mx-auto d-block', placeholder: '最多不超過1000字', maxlength: '1000', style: 'height:60px;' }
        ] },
        { tagName: 'td', style: 'text-align:center;', childNodes: [
          { tagName: 'button', type: 'button', className: 'btn btn-danger btn-sm', onclick: "RemoveRow2('" + count_relative + "')", innerHTML: '移除' }
        ] }
      ]
    }, {
      tagName: 'input', type: 'hidden', id: 'removedtwo' + count_relative, value: '0'
    }];

    $('#relativeBody').appendDom(row);
    $('#form01_1_Title').show();
    $('#form01_1_addShow').show();
    $('#submit').show();

    $('select.form01_1Relationship').off('change').on('change', function () {
      let $this = $(this);
      let otherInput = $this.closest('td').find('.OtherRelationshipInput');
      if ($this.val() === '其他') {
        otherInput.show();
      } else {
        otherInput.hide().val('');
      }
    });

    $('select.form01_1CareTime').off('change').on('change', function () {
      let $this = $(this);
      let otherInput = $this.closest('td').find('.OtherCareTimeInput');
      if ($this.val() === '其他') {
        otherInput.show();
      } else {
        otherInput.hide().val('');
      }
    });
  });
});
