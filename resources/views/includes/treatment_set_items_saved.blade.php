@if(!empty((array)$treatment_set_lists) && optional($treatment_set_lists)->isNotEmpty())
  <table class="table table-bordered">
    <thead>
      <tr>
        <th width="90px" class="table-info text-center">排序</th>
        <th width="150px" class="table-info text-center">簡易代碼</th>
        <th width="150px" class="table-info text-center">健保代碼</th>
        <th width="400px" class="table-info text-center">處置項目</th>
        <th width="150px" class="table-info text-center">建立人員</th>
        <th width="80px" class="table-info text-center">功能</th>
      </tr>
    </thead>
    <tbody>
    @php 
      $save_count = 0;
      $tmp = ''; 
    @endphp
    @foreach($treatment_set_lists as $item)
      @php 
        $tmp .= $item->id . ', ';
        $save_count = $item->sort;
      @endphp
      <tr id="DelTR{{ $item->id }}">
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="oldtreatment_sort[{{$item->id}}]" id="oldtreatment_sort_{{ $item->id }}" value="{{ $item->sort }}">
        </td>
        <td align="center">
          <select class="form-control" name="oldtreatment_shortcode[{{$item->id}}]" id="oldtreatment_shortcode_{{ $item->id }}" style="width: 250px;">
            <option value=""></option>
            @foreach($treatment_items as $item_key=>$item_value)
              <option value="{{ $item_value->short_code }}" data-id="{{ $item_value->id }}" data-code="{{ $item_value->treatment_code }}" data-name="{{ $item_value->treatment_name_zh }}" {{ $item_value->short_code===$item->short_code?'selected':'' }}>
                {{ $item_value->short_code."【".$item_value->treatment_code."】" }}
              </option>
            @endforeach
          </select>
          <input type="hidden" name="oldtreatment_id[{{$item->id}}]" id="oldtreatment_id_{{$item->id}}" value="{{ $item->nhiservice_treatment_id }}">
        </td>
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="oldtreatment_code[{{$item->id}}]" id="oldtreatment_code_{{$item->id}}" value="{{ $treatment_item_arrayinfo[$item->nhiservice_treatment_id]['treatment_code'] ?? '' }}" readonly>
        </td>
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="oldtreatment_name[{{$item->id}}]" id="oldtreatment_name_{{$item->id}}" value="{{ $treatment_item_arrayinfo[$item->nhiservice_treatment_id]['treatment_name_zh'] ?? '' }}" readonly>
        </td>
        <td align="center">{{ $users_arrayinfo[$item->created_by] ?? '' }}</td>
        <td align="center">
          <button type="button" class="btn btn-danger btn-sm" onclick="is1DEL({{ $item->id }})">移除</button>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <input type="hidden" name="saved_count" id="saved_count" value="{{ $save_count }}">
  <input type="hidden" name="oldtreatment_infoNo" value="{{ $tmp }}">
  <input type="hidden" name="oldtreatment_deleted_ids" id="oldtreatment_deleted_ids">
@endif
<script>
  // 初始化：紀錄被刪除的 ID 陣列
  let deletedIdList = [];
  function is1DEL(id) {
    document.getElementById('DelTR' + id).remove();
    // 加入被刪除的 ID，避免重複
    if (!deletedIdList.includes(id)) {
        deletedIdList.push(id);
    }
    // 同步更新隱藏欄位的值
    $('#oldtreatment_deleted_ids').val(deletedIdList.join(','));
  }

  $('select[id^=oldtreatment_shortcode_]').off('change').on('change', function () {
    var id= this.id.split("_");
    var selected = $(this).find('option:selected');
    var code = selected.data('code');
    var name = selected.data('name');
    var id_no = selected.data('id');
    $("#oldtreatment_code_"+id[2]).val(code);
    $("#oldtreatment_name_"+id[2]).val(name);
    $("#oldtreatment_id_"+id[2]).val(id_no);
  });
</script>
