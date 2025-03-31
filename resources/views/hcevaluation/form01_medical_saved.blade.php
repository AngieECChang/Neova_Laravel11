@if(optional($medical_result)->isNotEmpty())
  <table class="table table-bordered">
    <thead>
      <tr>
        <th width="90px" class="table-info">成員姓名</th>
        <th width="115px" class="table-info">加入照護團隊日期</th>
        <th width="100px" class="table-info">身分證字號</th>
        <th width="120px" class="table-info">職稱</th>
        <th width="100px" class="table-info">電話</th>
        <th width="150px" class="table-info">備註</th>
        <th width="90px" class="table-info">填寫人員</th>
        <th width="60" class="table-info">功能</th>
      </tr>
    </thead>
    <tbody>
    @php 
      $tmp2 = ''; 
      $form01_record_count = 0;
    @endphp
    @foreach($medical_result as $medical)
      @php 
      $tmp2 .= $medical->id . ', ';
      $form01_record_count++;
      @endphp
      <tr id="DelTR{{ $medical->id }}">
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="form01_oldName[{{$medical->id}}]" value="{{ $medical->Name }}">
        </td>
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="form01_oldCareDate[{{$medical->id}}]" id="form01_oldCareDate_{{ $medical->id }}" value="{{ $medical->CareDate }}">
        </td>
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="form01_oldIdNo[{{$medical->id}}]" value="{{ $medical->IdNo}}">
        </td>
        <td align="center">
          <select class="form-select form-select-sm" name="form01_oldJobTitle[{{$medical->id}}]" id="form01_oldJobTitle_{{ $medical->id }}" style="width: 120px;">
            <option value=""></option>
            @foreach(getOptionList('JobTitle') as $key => $value)
              <option value="{{ $value }}" {{ $medical->JobTitle === $value ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
          </select>
          @if($medical->JobTitleOther || $medical->JobTitle==="其他")
            <input type="text" class="form-control form-control-sm mt-1" name="form01_oldJobTitleOther[{{$medical->id}}]" id="form01_oldJobTitleOther_{{ $medical->id }}" value="{{ $medical->JobTitleOther }}">
          @else
            <div id="form01_oldOtherJobTitle_{{ $medical->id }}" class="mt-1" style="display: none;">
              <input type="text" class="form-control form-control-sm" name="form01_oldJobTitleOther[{{$medical->id}}]" id="form01_oldJobTitleOther_{{ $medical->id }}">
            </div>
          @endif
        </td>
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="form01_oldTel[{{$medical->id}}]" value="{{ $medical->Tel}}">
        </td>
        <td>
          <textarea class="form-control form-control-sm" name="form01_oldCareRemark[{{$medical->id}}]" rows="2" maxlength="1000">{{ $medical->CareRemark }}</textarea>
        </td>
        <td align="center">{{ $users_arrayinfo[$medical->created_by]}}</td>
        <td align="center">
          <button type="button" class="btn btn-danger btn-sm" onclick="isDEL({{ $medical->id }})">移除</button>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <input type="hidden" name="form01_oldCount" value="{{ $form01_record_count }}">
  <input type="hidden" name="form01_infoNo" value="{{ $tmp2 }}">
  <input type="hidden" name="form01_deleted_ids" id="form01_deleted_ids">
@endif
<script>
  // 初始化：紀錄被刪除的 ID 陣列
  let deletedIdList = [];
  function isDEL(id) {
    document.getElementById('DelTR' + id).remove();
    // 加入被刪除的 ID，避免重複
    if (!deletedIdList.includes(id)) {
        deletedIdList.push(id);
    }
    // 同步更新隱藏欄位的值
    $('#form01_deleted_ids').val(deletedIdList.join(','));
  }

  $('select[id^=form01_oldJobTitle_]').off('change').on('change', function () {
    var id= this.id.split("_");
    if($("#"+this.id).val()=="其他"){
        $("#form01_oldOtherJobTitle_"+id[2]).show();
    }else{
        $("#form01_oldOtherJobTitle_"+id[2]).hide();
        $("#form01_oldJobTitleOther_"+id[2]).val("");
    }
  });

  $("input[id^=form01_oldCareDate_]").datepicker({
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
</script>
