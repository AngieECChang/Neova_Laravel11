function RemoveRow(id) {
  $('#removed' + id).val("1");
  $('#row' + id).remove();
  if ((parseInt($('#form01_fileCount').val()) <= 1) && ($('#form01_oldCount').val() == 0)) {
    $("#submit").hide();
  }
}

$(function () {
  let count = 0;

  // 建立主 table 結構（含標題列）
  $('#form01_Title').appendDom([{
    tagName: 'table', id: 'teamTable', className: 'table table-bordered', childNodes: [{
      tagName: 'thead', childNodes: [{
        tagName: 'tr', childNodes: [
          { tagName: 'th', className:'table-info', style: 'width:110px;', innerHTML: '成員姓名' },
          { tagName: 'th', className:'table-info', style: 'width:120px;', innerHTML: '加入照護團隊日期' },
          { tagName: 'th', className:'table-info', style: 'width:125px;', innerHTML: '身分證字號' },
          { tagName: 'th', className:'table-info', style: 'width:100px;', innerHTML: '職稱' },
          { tagName: 'th', className:'table-info', style: 'width:110px;', innerHTML: '電話' },
          { tagName: 'th', className:'table-info', style: 'width:120px;', innerHTML: '備註' },
          { tagName: 'th', className:'table-info', style: 'width:60px;', innerHTML: '功能' }
        ]
      }],
    }, {
      tagName: 'tbody', id: 'form01Body'
    }]
  }]);
  $('#form01_addShow').hide();

  $('#form01_addFile').click(function () {
    count++;
    let selectJobHTML = $('#medical-template').html();
    let row = [{
      tagName: 'tr', id: 'row' + count, childNodes: [
        { tagName: 'td', childNodes: [{ tagName: 'input', name: 'form01Name[]', className: 'form-control', style: 'width:110px;' }] },
        { tagName: 'td', childNodes: [{ tagName: 'input', name: 'form01CareDate[]', id: 'form01CareDate_'+count, className: 'form-control', style: 'width:120px;' }] },
        { tagName: 'td', childNodes: [{ tagName: 'input', name: 'form01IdNo[]', className: 'form-control', style: 'width:130px;' }] },
        { tagName: 'td', childNodes: [
          {
            tagName: 'select', name: 'form01JobTitle[]', style: 'width:100px;', className: 'form-select form01JobTitle', innerHTML: selectJobHTML
          },
          { tagName: 'input', type: 'text', className: 'form-control mt-1 OtherJobTitleInput', name: 'form01JobTitleOther[]', placeholder: '其他職稱', style: 'display:none;' }
        ] },
        { tagName: 'td', childNodes: [{ tagName: 'input', name: 'form01Tel[]', className: 'form-control', style: 'width:130px;' }] },
        { tagName: 'td', childNodes: [{ tagName: 'textarea', name: 'form01CareRemark[]', className: 'form-control' }] },
        { tagName: 'td', childNodes: [
          { tagName: 'input', type: 'hidden', id: 'created_by' + count },
          { tagName: 'button', type: 'button', className: 'btn btn-danger btn-sm', onclick: "RemoveRow('" + count + "')", innerHTML: '移除', style: 'width:60px;' }
        ] }
      ]
    }, {
      tagName: 'input', type: 'hidden', id: 'removed' + count, value: '0'
    }];

    $('#form01_addShow').show();
    $('#form01_Title').show();
    $('#form01Body').appendDom(row);
    $('#form01_fileCount').val(count);
    $('#submit').show();

    $('select.form01JobTitle').off('change').on('change', function () {
      let $this = $(this);
      let otherInput = $this.closest('td').find('.OtherJobTitleInput');
      if ($this.val() === '其他') {
        otherInput.show();
      } else {
        otherInput.hide().val('');
      }
    });

    $("input[id^=form01CareDate_]").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true,
      defaultDate: new Date(),
      // showButtonPanel: true,
      monthNames: ["一月", "二月", "三月", "四月", "五月", "六月",
                  "七月", "八月", "九月", "十月", "十一月", "十二月"], // 國字月份
      monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月",
                        "7月", "8月", "9月", "10月", "11月", "12月"],
      dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"], // 國字星期
    });
  });
});