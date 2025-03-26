@extends('layouts.app')
@section('content')
@include('includes.case_selector')
@php
  $gender = $the_case->gender ?? '-1';
@endphp
<div class="row align-items-center mt-4">
  <div class="col-6">
    <h1 class="h3 text-gray-800 mb-4">全人周全性評估_基本資料</h1>
  </div>
</div>
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="POST">
      @csrf
      <input type="hidden" name="caseID" value="{{ $the_case->caseID ?? '0' }}">
      <table class="table table-bordered align-middle">
        <tbody>
          <tr>
            <th width="20%">姓名</th>
            <td width="30%">
                <input type="text" class="form-control" value="{{ $the_case->name ?? '空白個案' }}">
            </td>
            <th width="20%">性別</th>
            <td width="30%">
              <select class="form-control" name="gender" style="width: 100px;">
                  <option value="1" {{ $gender == "1" ? "selected" : "" }}>男</option>
                  <option value="0" {{ $gender == "0" ? "selected" : "" }}>女</option>
                  <option value="2" {{ $gender == "2" ? "selected" : "" }}>其他</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>生日</th>
            <td class="d-flex align-items-center">民國 <input type="text" class="form-control" name="birthdate" id="birthdate" value="{{ dateTo_c($the_case->birthdate) ?? '' }}" style="width: 150px;margin-left: 8px;">
              <input type="hidden" id="birthdate_AD" value="{{ $the_case->birthdate }}">
            </td>
            <th>評估日期</th>
            <td><input type="text" class="form-control datepicker" name="date" value="{{ now()->format('Y-m-d') }}" required></td>
          </tr>
          <tr>
            <th>教育程度</th>
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
            <th>婚姻狀況</th>
            <td>
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
            <th>宗教信仰</th>
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
            <th>緊急聯絡人</th>
            <td><input type="text" class="form-control" name="Q29b" required></td>
          </tr>
          <tr>
            <th>電話</th>
            <td colspan="3"><input type="text" class="form-control" name="Q29c" required></td>
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
  });
</script>
@endsection