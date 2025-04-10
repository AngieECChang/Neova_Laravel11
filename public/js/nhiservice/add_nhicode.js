function RemoveRow(id) {
  $('#removed' + id).val("1");
  $('#row' + id).remove();
  // amountValue();
	if(($('#fileCount').val() == 0) && ($('#oldCount').val() ==0)){		
		$("#submit").hide();		
	}	
}

$(function () {

  let count = 0;

  $('#nhi_Title').appendDom([{
    tagName: 'table', id: 'teamTable', className: 'table table-bordered', childNodes: [{
      tagName: 'thead', childNodes: [{
        tagName: 'tr', childNodes: [
          { tagName: 'th', className:'table-info text-center', style: 'width:175px', innerHTML: '處置代碼' },
          { tagName: 'th', className:'table-info text-center', style: 'width:*', innerHTML: '名稱' },
          { tagName: 'th', className:'table-info text-center', style: 'width:80px', innerHTML: '天數' },
          { tagName: 'th', className:'table-info text-center', style: 'width:90px', innerHTML: '點數' },
          { tagName: 'th', className:'table-info text-center', style: 'width:90px', innerHTML: '執行人員' },
          { tagName: 'th', className:'table-info text-center', style: 'width:160px', innerHTML: '執行時間起' },
          { tagName: 'th', className:'table-info text-center', style: 'width:160px;', innerHTML: '執行時間迄' },
          { tagName: 'th', className:'table-info text-center', style: 'width:40px;', innerHTML: '功能' }
        ]
      }],
    }, {
      tagName: 'tbody', id: 'nhiBody'
    }]
  }]);
  $('#nhi_addShow').hide();

  $('#nhi_addFile').click(function () {
    var count = $("#nhi_fileCount").val();
    // var chkType = '<?php echo $type;?>'; 
    count++;
    let selectJobHTML = $('#emp-template').html();
    let row = [{
      tagName: 'tr', id: 'row' + count, childNodes: [
        { tagName: 'td', childNodes: [{ tagName: 'input', name: 'nhiA73[]', id: 'nhiA73_'+count, className: 'form-control', size: '25' }] },
        { tagName: 'td', childNodes: [{ tagName: 'input', name: 'nhiName[]', id: 'nhiName_'+count, className: 'form-control'}] },
        { tagName: 'td', childNodes: [{ tagName: 'input', name: 'nhiA76[]', id: 'nhiA76_'+count, className: 'form-control', size: '6' }] },
        { tagName: 'td', childNodes: [{ tagName: 'input', name: 'nhiA9[]', id: 'nhiA9_'+count, className: 'form-control', size: '10' }] },
        { tagName: 'td', align:'center' , childNodes: [
          {
            tagName: 'select', name: 'nhip16[]', id: 'nhip16_'+count, style: 'width:110px;', className: 'form-control', innerHTML: selectJobHTML
          }
        ] },
        { tagName: 'td', align:'center', childNodes: [{ tagName: 'input', name: 'nhiA71_1[]', id: 'nhiA71_1_'+count, className: 'form-control', size: 'width:150px !important;' }] },
        { tagName: 'td', align:'center', childNodes: [{ tagName: 'input', name: 'nhiA71_2[]', id: 'nhiA71_2_'+count, className: 'form-control', style: 'width:150px !important;' }] },
        { tagName: 'td', childNodes: [
          { tagName: 'input', type: 'hidden', name: 'nhip3[]' + count, id: 'nhip3_' + count },
          { tagName: 'button', type: 'button', className: 'btn btn-danger btn-sm', onclick: "RemoveRow('" + count + "')", innerHTML: '移除', style: 'width:45px;' }
        ] }
      ]
    }, {
      tagName: 'input', type: 'hidden', id: 'removed' + count, value: '0'
    }];

    $('#nhi_addShow').show();
    $('#nhi_Title').show();
    $('#nhiBody').appendDom(row);
    $('#nhi_fileCount').val(count);
    $('#submit').show();
    if($('#nhi_fileCount').val()> 0 ){
      $("#submit").show();
    }
    $('input[id^=nhiA73_]').autocomplete({
      source: function (request, response) {
        $.ajax({
          url: '/nhi-code-search',
          method: 'GET',
          dataType: 'json',
          data: {
            term: request.term
          },
          success: function (data) {
            response(data); // 必須是陣列 [{ label: xxx, value: xxx, name: xxx, point: xxx }]
          }
        });
      },
      minLength: 3, // 輸入三個字元後啟動
      select: function (event, ui) {
        const id = event.target.id; // e.g., nhiA73_1
        applyPREData(ui.item, id);
      }
    });

    $('#nhiA71_1_'+count).change(function () {
      var startdate = $('#nhiA71_1_' + count).val();
      var start = new Date(startdate);
      var enddate = '';
      var Y = start.getFullYear() + '-';
      var M = (start.getMonth() + 1 < 10 ? '0' + (start.getMonth() + 1).toString() : (start.getMonth() + 1).toString()) + '-';
      var D = (start.getDate() < 10 ? '0' + start.getDate() : start.getDate()) + ' ';
      var h = (start.getHours() + 1 < 10 ? '0' + (start.getHours() + 1).toString() : (start.getHours() + 1).toString()) + ':';
      var m = (start.getMinutes() < 10 ? '0' + start.getMinutes() : start.getMinutes());
      enddate = Y + M + D + h + m;
      // amountValue();
    });

    $('#nhiA71_2_' + count).change(function () {
      if ($('#nhiA71_2_' + count).val() != '' && $('#nhiA71_2_' + count).val() < $('#nhiA71_1_' + count).val()) {
        var startdate = $('#nhiA71_1_' + count).val();
        var start = new Date(startdate);
        var Y = start.getFullYear() + '-';
        var M = (start.getMonth() + 1 < 10 ? '0' + (start.getMonth() + 1).toString() : (start.getMonth() + 1).toString()) + '-';
        var D = (start.getDate() < 10 ? '0' + start.getDate() : start.getDate()) + ' ';
        alert("執行時間迄的時間不可以小於執行時間起的時間");
        $('#nhiA71_2_' + count).val(Y+M+D+'00:00');
      }
    });

    $("input[id^=nhiA71_1_]").datetimepicker({
      format: 'Y-m-d H:i:s',
      mask: false
    });
    $("input[id^=nhiA71_2_]").datetimepicker({
      format: 'Y-m-d H:i:s',
      mask: false
    });
  });
});