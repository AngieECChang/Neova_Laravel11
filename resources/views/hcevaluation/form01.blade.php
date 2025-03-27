@extends('layouts.app')
@section('content')
@include('includes.case_selector')
@php
  $gender = $the_case->gender ?? '-1';
@endphp
<style>
  .radio {
    width: 20px !important;
    height: 20px !important;
    vertical-align: middle; /* 保持與文字垂直對齊 */
  }

  table, th, tr, td {
    border: 1.5px solid #4A4A4A !important; /* 改為你想要的顏色與寬度 */
    border-collapse: collapse !important; /* 建議加上讓框線不重疊 */
    color: #4A4A4A !important;
    vertical-align: middle !important;
  }
  th {
    text-align: center !important;
  }
  .input-150 {
    width: 150px;
  }

</style>
<div class="row align-items-center mt-4">
  <div class="col-6">
    <h1 class="h3 text-gray-800 mb-4">全人周全性評估_基本資料</h1>
  </div>
</div>
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="POST" action="{{ route('hcevaluation.save') }}">
      @csrf
      <input type="hidden" name="caseID" value="{{ $the_case->caseID ?? '0' }}">
      <table class="table table-bordered align-middle">
        <tbody>
          <tr>
            <th width="120" class="table-success">姓名</th>
            <td width="320">
                <input type="text" class="form-control input-150" value="{{ $the_case->name ?? '空白個案' }}">
            </td>
            <th width="120" class="table-success">性別</th>
            <td width="320">
              <div class="form-check form-check-inline">
                <input class="form-check-input radio" type="radio" name="gender" id="gender1" value="1" {{ $gender == "1" ? "checked" : "" }}>
                <label class="form-check-label radio" for="gender1"> 男 </label>
              </div>
              <div class="form-check form-check-inline ">
                <input class="form-check-input radio" type="radio" name="gender" id="gender0" value="0" {{ $gender == "0" ? "checked" : "" }}>
                <label class="form-check-label" for="gender0"> 女 </label>
              </div>
              <div class="form-check form-check-inline">              
                <input class="form-check-input radio" type="radio" name="gender" id="gender2" value="2" {{ $gender == "2" ? "checked" : "" }}> 
                <label class="form-check-label" for="gender2">其他 </label>
              </div>
            </td>
            <td colspan="2" rowspan="4" width="*" class="text-center">
              {{-- 大頭照區塊 --}}
              <div class="mb-2">
                <img src="{{ $photoUrl ?? asset('images/noImage.png') }}" alt="大頭照"
                    class="img-thumbnail" style="max-width: 180px; max-height: 180px;">
              </div>
              <div class="mb-2">
                <input class="form-control form-control-sm" type="file" name="photo" accept="image/*">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">生日</th>
            <td>
              <div class="d-flex align-items-center">
                民國 <input type="text" class="form-control" name="birthdate" id="birthdate" value="{{ optional($the_case)->birthdate ? dateTo_c($the_case->birthdate) : '' }}" style="width: 150px;margin-left: 8px;">
              </div>
              <input type="hidden" id="birthdate_AD" value="{{ optional($the_case)->birthdate }}">
            </td>
            <th class="table-success">身分證字號</th>
            <td colspan="3">
              <input type="text" class="form-control input-150" name="IdNo" value="{{ optional($the_case)->IdNo }}" required>
            </td>
          </tr>
          <tr>
            <th class="table-success">聯絡電話</th>
            <td>            
              <input type="text" class="form-control input-150" name="PhoneNumber" value="{{ optional($result)->PhoneNumber ?? '' }}" style="margin-left: 8px;">            
            </td>
            <th class="table-success">個案類型</th>
            <td colspan="3">
              @foreach (getOptionList('CaseType') as $key => $value)
                <div class="form-check form-check-inline">
                  <input class="form-check-input radio" type="radio" name="CaseType" id="CaseType{{$key}}" value="{{$key}}" {{ optional($result)->CaseType == $key ? "checked" : "" }}>
                  <label class="form-check-label" for="CaseType{{$key}}"> {{$value}} </label>
                </div>
              @endforeach
            </td>
          </tr>
          <tr>
            <th class="table-success">收案來源</th>
            <td colspan="5">
              @foreach (getOptionList('CaseSource') as $key => $value)
                <div class="form-check form-check-inline">
                  <input class="form-check-input radio" type="radio" name="CaseSource" id="CaseSource{{$key}}" value="{{$key}}" {{ optional($result)->CaseSource == $key ? "checked" : "" }}>
                  <label class="form-check-label" for="CaseSource{{$key}}"> {{$value}} </label>
                </div>
              @endforeach
              <input type="text" class="form-control input-150" name="CaseSource_other" value="{{ optional($result)->CaseSource_other }}" required>
            </td>
          </tr>
          <tr>
            <th class="table-success">地址</th>
            <td colspan="5">
              <div class="row align-items-end g-2">
                <div class="col-auto">
                  <label for="city" class="form-label" style="font-size: 10pt;">縣市</label>
                  <select name="city" id="city" class="form-control">
                    <option value="">請選擇</option>
                    @foreach($cities as $city)
                      <option value="{{ $city }}" {{ optional($result)->city === $city ? 'selected' : '' }}>
                        {{ $city }}
                      </option>
                    @endforeach  
                  </select>
                </div>
                <div class="col-auto">
                    <label for="town" class="form-label" style="font-size: 10pt;">市區鄉鎮</label>
                    <select name="town" id="town" class="form-control">
                      <option value="">請先選擇縣市</option>
                    </select>
                </div>
                <div class="col">
                    <label for="address_detail" class="form-label" style="font-size: 10pt;">地址</label>
                    <input type="text" name="lane" id="lane" class="form-control" value="{{ optional($result)->lane ?? '' }}" style="width:300px;">
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">婚姻狀況</th>
            <td colspan="3">
                <select class="form-select" name="Q7">
                    <option value="1">未婚</option>
                    <option value="2">已婚</option>
                    <option value="3">分居</option>
                    <option value="4">喪偶</option>
                    <option value="5">離異</option>
                </select>
            </td>
          </tr>
          <tr>
            <th class="table-success">教育程度</th>
            <td>
                <select class="form-select" name="Q4">
                    <option value="1">不識字</option>
                    <option value="2">識字未就學</option>
                    <option value="3">小學</option>
                    <option value="4">初中(職)</option>
                    <option value="5">高中(職)</option>
                    <option value="6">大學(專技)以上</option>
                </select>
            </td>
            <th class="table-success">婚姻狀況</th>
            <td colspan="3">
                <select class="form-select" name="Q7">
                    <option value="1">未婚</option>
                    <option value="2">已婚</option>
                    <option value="3">分居</option>
                    <option value="4">喪偶</option>
                    <option value="5">離異</option>
                </select>
            </td>
          </tr>
          <tr>
            <th class="table-success">宗教信仰</th>
            <td>
                <select class="form-select" name="Q13">
                    <option value="1">無</option>
                    <option value="2">佛教</option>
                    <option value="3">道教</option>
                    <option value="4">基督教</option>
                    <option value="5">天主教</option>
                    <option value="6">回教</option>
                    <option value="7">其他</option>
                </select>
            </td>
            <th class="table-success">緊急聯絡人</th>
            <td colspan="3"><input type="text" class="form-control" name="Q29b" required></td>
          </tr>
          <tr>
            <th class="table-success">電話</th>
            <td colspan="5"><input type="text" class="form-control" name="Q29c" required></td>
          </tr>
          <tr>
            <th width="120" class="table-success">評估日期</th>
            <td colspan="6">
              <input type="text" class="form-control datepicker" name="date" value="{{ now()->format('Y-m-d') }}" required style="width: 150px;">
            </td>
          </tr>
        </tbody>
      </table>
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary">儲存</button>
      </div>
    </form>
  </div>
</div>

<script>
  function convertYearDropdownToROC(inst) {
  // 找出 select 元件
    setTimeout(function () {
      const yearSelect = $(inst.dpDiv).find(".ui-datepicker-year");

      yearSelect.children("option").each(function () {
        const adYear = parseInt($(this).val());
        const rocYear = adYear - 1911;
        $(this).text(rocYear); // 顯示民國年
      });
    }, 0);
  }

  function isValidTaiwanID(id) {
    const regex = /^[A-Z]{1}[A-Z0-9]{1}\d{8}$/;
    return regex.test(id.toUpperCase());
  }

  document.addEventListener("DOMContentLoaded", function() {
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    let today = new Date();
    let thisadYear = today.getFullYear(); // 取得民國年
    let maxadYear = thisadYear + 3; // 最大年份 = 今年 + 3 年
    $("#birthdate").datepicker({
      dateFormat: "yy/mm/dd", // yy 會解釋為 2 位數年份，但我們會手動轉換
      yearRange: "1911:"+maxadYear,
      changeMonth: true,
      changeYear: true,
      defaultDate: new Date($("#birthdate_AD").val()), // 預設為今天
      monthNames: ["一月", "二月", "三月", "四月", "五月", "六月",
                  "七月", "八月", "九月", "十月", "十一月", "十二月"], // 國字月份
      monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月",
                        "7月", "8月", "9月", "10月", "11月", "12月"],
      dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"], // 國字星期
      beforeShow: function (input, inst) {
        convertYearDropdownToROC(inst);
      },
      onSelect: function (dateText, inst) {
        // console.log("dateText="+dateText);
        if (dateText.match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/)) {
          // console.log("4");
          let parts = dateText.split('/');
          let year = parseInt(parts[0]) - 1911; // 轉換回民國年
          $(this).val(year + '/' + parts[1] + '/' + parts[2]);
        }
      },
      onClose: function (dateText, inst) {
        if (dateText.match(/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/)) {
          let parts = dateText.split('/');
          let year = parseInt(parts[0]) - 1911; // 轉換回民國年
          $(this).val(year + '/' + parts[1] + '/' + parts[2]);
        }
      },
      onChangeMonthYear: function (year, month, inst) {
        convertYearDropdownToROC(inst);
      }
    });

    $('#city').on('change', function () {
      let city = $(this).val();
      $('#town').html('<option value="">載入中...</option>');

      $.get('/api/towns', { city: city }, function (data) {
        let options = '<option value="">請選擇</option>';
        data.forEach(function (town) {
            options += `<option value="${town}">${town}</option>`;
        });
        $('#town').html(options);
      });
    });
  });
</script>
@endsection