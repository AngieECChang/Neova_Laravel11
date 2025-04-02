function RemoveRow(id) {
  $('#removed' + id).val("1");
  $('#row' + id).remove();
  updateSortOrder(); // 自動重新排序
}

$(function () {
  let count = 0;
  let saved_count = 0;
  $('#treatmentset_Title').appendDom([{
    tagName: 'table', id: 'treatmentTable', className: 'table table-bordered', childNodes: [{
      tagName: 'thead', childNodes: [{
        tagName: 'tr', childNodes: [
          { tagName: 'th', className:'table-info text-center', style: 'width:100px;', innerHTML: '排序' },
          { tagName: 'th', className:'table-info text-center', style: 'width:150px;', innerHTML: '簡易代碼' },
          { tagName: 'th', className:'table-info text-center', style: 'width:150px;', innerHTML: '健保代碼' },
          { tagName: 'th', className:'table-info text-center', style: 'width:400px;', innerHTML: '處置項目' },
          { tagName: 'th', className:'table-info text-center', style: 'width:60px;', innerHTML: '功能' }
        ]
      }],
    }, {
      tagName: 'tbody', id: 'treatmentsetBody'
    }]
  }]);
  $('#treatmentset_addShow').hide();

  $('#treatmentset_addFile').click(function () {
    count++;
    saved_count = $('#saved_count').val();
    let currentRowCount = $('#treatmentsetBody tr').length;
    let sortValue = Number(currentRowCount) + Number(saved_count) + 1;
    let selectJobHTML = $('#treatment-template').html();
    let row = [{
      tagName: 'tr', id: 'row' + count, childNodes: [
        { tagName: 'td', className:'text-center', childNodes: [{ tagName: 'input', name: 'treatment_sort[]', id: 'treatment_sort_' + count, className: 'form-control', style: 'width:80px;' }] },
        { tagName: 'td', className:'text-center', childNodes: [
          {
            tagName: 'select', name: 'treatment_item[]', id: 'treatment_item_' + count, style: 'width:300px;', className: 'form-control treatment_item_select', innerHTML: selectJobHTML
          },
          { tagName: 'input', type: 'hidden', name: 'treatment_id[]', id: 'treatment_id_' + count }
        ] },
        { tagName: 'td', className:'text-center', childNodes: [{ tagName: 'input', name: 'treatment_code[]', id: 'treatment_code_' + count, className: 'form-control form-control-plaintext', style: 'width:200px;', readonly:'readonly' }] },
        { tagName: 'td', className:'text-center', childNodes: [{ tagName: 'input', name: 'treatment_name[]', id: 'treatment_name_' + count, className: 'form-control', style: 'width:400px;', readonly:'readonly' }] },      
        { tagName: 'td', className:'text-center', childNodes: [
          { tagName: 'button', type: 'button', className: 'btn btn-danger btn-sm', onclick: "RemoveRow('" + count + "')", innerHTML: '移除', style: 'width:60px;' }
        ] }
      ]
    }, {
      tagName: 'input', type: 'hidden', id: 'removed' + count, value: '0'
    }];

    $('#treatmentset_addShow').show();
    $('#treatmentset_Title').show();
    $('#treatmentsetBody').appendDom(row);
    $('#submit').show();

    $('#treatment_sort_' + count).val(sortValue);
    let $newSelect = $('#treatment_item_' + count);
    $newSelect.select2();
    $newSelect.on('change',function (){
      var id= this.id.split("_");
      var selected = $(this).find('option:selected');
      var code = selected.data('code');
      var name = selected.data('name');
      var id_no = selected.data('id');
      $("#treatment_code_"+id[2]).val(code);
      $("#treatment_name_"+id[2]).val(name);
      $("#treatment_id_"+id[2]).val(id_no);
    });
    $newSelect.trigger('change');
  });
});

function updateSortOrder() {
  $('#treatmentsetBody tr').each(function (index) {
    $(this).find('input[name="treatment_sort[]"]').val(index + 1);
  });
}