@if(!empty((array)$relative_result) && optional($relative_result)->isNotEmpty())
  <table class="table table-bordered">
    <thead>
      <tr>
        <th width="90px" class="table-warning">成員姓名</th>
        <th width="110px" class="table-warning">關係</th>
        <th width="110px" class="table-warning">主要照顧時間</th>
        <th width="100px" class="table-warning">電話1</th>
        <th width="100px" class="table-warning">電話2</th>
        <th width="100px" class="table-warning">電話3</th>
        <th width="150px" class="table-warning">備註</th>
        <th width="90px" class="table-warning">填寫人員</th>
        <th width="60px" class="table-warning">功能</th>
      </tr>
    </thead>
    <tbody>
    @php 
      $tmp = ''; 
    @endphp
    @foreach($relative_result as $relative)
      @php 
      $tmp .= $relative->id . ', ';
      @endphp
      <tr id="Del1TR{{ $relative->id }}">
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="form01_1oldRelativesName[{{$relative->id}}]" value="{{ $relative->Name }}">
        </td>
        <td align="center">
          <select class="form-select form-select-sm" name="form01_1oldRelationship[{{$relative->id}}]" id="form01_1oldRelationship_{{ $relative->id }}" style="width: 120px;">
            <option value=""></option>
            @foreach(getOptionList('Relationship') as $key => $value)
              <option value="{{ $value }}" {{ $relative->Relationship === $value ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
          </select>
          @if($relative->RelationshipOther || $relative->Relationship==="其他")
            <input type="text" class="form-control form-control-sm mt-1" name="form01_1oldRelationshipOther[{{$relative->id}}]" id="form01_1oldRelationshipOther_{{ $relative->id }}" value="{{ $relative->RelationshipOther }}">
          @else
            <div id="form01_1oldOtherRelationship_{{ $relative->id }}" class="mt-1" style="display: none;">
              <input type="text" class="form-control form-control-sm" name="form01_1oldRelationshipOther[{{$relative->id}}]" id="form01_1oldRelationshipOther_{{ $relative->id }}">
            </div>
          @endif
        </td>
        <td align="center">
          <select class="form-select form-select-sm" name="form01_1oldCareTime[{{$relative->id}}]" id="form01_1oldCareTime_{{ $relative->id }}" style="width: 120px;">
            <option value=""></option>
            <option value="白天" {{ $relative->CareTime === '白天' ? 'selected' : '' }}>白天</option>
            <option value="晚上" {{ $relative->CareTime === '晚上' ? 'selected' : '' }}>晚上</option>
            <option value="其他" {{ $relative->CareTime === '其他' ? 'selected' : '' }}>其他</option>
          </select>
          @if($relative->CareTimeOther || $relative->CareTime==="其他")
            <input type="text" class="form-control form-control-sm mt-1" name="form01_1oldCareTimeOther[{{$relative->id}}]" id="form01_1oldCareTimeOther_{{ $relative->id }}" value="{{ $relative->CareTimeOther }}">
          @else
            <div id="form01_1oldOtherCareTime_{{ $relative->id }}" class="mt-1" style="display: none;">
              <input type="text" class="form-control form-control-sm" name="form01_1oldCareTimeOther[{{$relative->id}}]" id="form01_1oldCareTimeOther_{{ $relative->id }}">
            </div>
          @endif
        </td>
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="form01_1oldTel1[{{$relative->id}}]" value="{{ $relative->Tel1}}"><br>
          <input type="text" class="form-control form-control-sm" name="form01_1oldTel1Remark[{{$relative->id}}]" value="{{ $relative->Tel1Remark}}">
        </td>
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="form01_1oldTel2[{{$relative->id}}]" value="{{ $relative->Tel2}}"><br>
          <input type="text" class="form-control form-control-sm" name="form01_1oldTel2Remark[{{$relative->id}}]" value="{{ $relative->Tel2Remark}}">
        </td>
        <td align="center">
          <input type="text" class="form-control form-control-sm" name="form01_1oldTel3[{{$relative->id}}]" value="{{ $relative->Tel3}}"><br>
          <input type="text" class="form-control form-control-sm" name="form01_1oldTel3Remark[{{$relative->id}}]" value="{{ $relative->Tel3Remark}}">
        </td>
        <td>
          <textarea class="form-control form-control-sm" name="form01_1oldRemark[{{$relative->id}}]" rows="2" maxlength="1000">{{ $relative->Remark }}</textarea>
        </td>
        <td align="center">{{ $users_arrayinfo[$relative->created_by]}}</td>
        <td align="center">
          <button type="button" class="btn btn-danger btn-sm" onclick="is1DEL({{ $relative->id }})">移除</button>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  <input type="hidden" name="form01_1infoNo" value="{{ $tmp }}">
  <input type="hidden" name="form01_1deleted_ids" id="form01_1deleted_ids">
@endif
<script>
  // 初始化：紀錄被刪除的 ID 陣列
  let deletedIdList1 = [];
  function is1DEL(id) {
    document.getElementById('Del1TR' + id).remove();
    // 加入被刪除的 ID，避免重複
    if (!deletedIdList1.includes(id)) {
        deletedIdList1.push(id);
    }
    // 同步更新隱藏欄位的值
    $('#form01_1deleted_ids').val(deletedIdList1.join(','));
  }

  $('select[id^=form01_1oldRelationship_]').off('change').on('change', function () {
    var id= this.id.split("_");
    if($("#"+this.id).val()=="其他"){
        $("#form01_1oldOtherRelationship_"+id[2]).show();
    }else{
        $("#form01_1oldOtherRelationship_"+id[2]).hide();
        $("#form01_1oldRelationshipOther_"+id[2]).val("");
    }
  });
  $('select[id^=form01_1oldCareTime_]').off('change').on('change', function () {
    var id= this.id.split("_");
    if($("#"+this.id).val()=="其他"){
        $("#form01_1oldOtherCareTime_"+id[2]).show();
    }else{
        $("#form01_1oldOtherCareTime_"+id[2]).hide();
        $("#form01_1oldCareTimeOther_"+id[2]).val("");
    }
  });
</script>
