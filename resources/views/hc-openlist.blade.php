@extends('layouts.app')
@section('content')
<style>
  .custom-table tbody tr:hover {
    background-color: #d1ecf1 !important;  /* 淡藍色 */
  }
</style>
@php
  $patient_type = config('public.hc_patient_type');
  $gender = config('public.gender');
  $close_reason = config('public.hc_close_reason');
@endphp
<div class="row align-items-center mb-4">
  <div class="col-3">
    <h1 class="h3 text-gray-800 mb-0">收案列表</h1>
  </div>
  <div class="col-9">
    <form method="GET" action="{{ route('hc-openlist') }}" class="d-flex align-items-center justify-content-end" id="regionForm">
      <label for="region" class="visually-hidden">區域：</label>
      <select name="region" id="region" class="form-control me-2" style="width:160px" onchange="document.getElementById('regionForm').submit();" autocomplete="off">
        <option value="">全部</option>
        @foreach ($areaNames as $area)
          <option value="{{ $area }}" {{ request('region') == $area ? 'selected' : '' }}>
              {{ $area }}
          </option>
        @endforeach
      </select> 
      <div style="padding-left:10px">
        <a href="{{ route('hc-create') }}" class="btn text-white" style="background-color: orange;" data-bs-toggle="modal" data-bs-target="#newcaseModal">新增個案</a>
      </div>
    </form>
  </div>
</div>
<!-- Bootstrap Tabs for Case Types -->
<ul class="nav nav-tabs mt-3" id="caseTypeTabs" role="tablist">
  <!-- 🔹 Tabs 選項 -->
  <li class="nav-item">
    <a class="nav-link active fw-bold" id="tab-all" data-bs-toggle="tab" href="#content-all" role="tab">全部</a>
  </li>
  @foreach ($patient_type as $key => $value)
    <li class="nav-item">
      <a class="nav-link fw-bold" id="tab-{{ $key }}" data-bs-toggle="tab" href="#content-{{ $key }}" role="tab">
       {{ $value }}
      </a>
    </li>
  @endforeach
</ul>
<div class="tab-content mt-3">
  <div class="tab-pane fade show active" id="content-all" role="tabpanel">
    @php
      // 🔹 合併所有 caseType 的個案
      $all_cases = collect($open_cases)->collapse()->collapse(); 
    @endphp

    @if ($all_cases->isEmpty()) 
      <div class="alert alert-warning text-center mt-3">
        🚨 目前沒有任何個案
      </div>
    @else
    <div class="row align-items-center mb-4">
      <div class="col-3">
        <h1 class="h3 text-gray-800 mb-0"></h1>
      </div>
      <div class="col-9 d-flex justify-content-end">
        <input type="text" class="form-control tableSearch" placeholder="🔍 搜尋..." style="width: 150px;">
      </div>
    </div>
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
          <table class="table table-striped table-hover custom-table searchable-table">
            <thead class="sticky-top table-dark">
              <tr>
                <th width="100x" class="text-center">案號</th>
                <th width="120px" class="text-center">姓名</th>
                <th width="60px" class="text-center">性別</th>
                <th width="110px" class="text-center">類型</th>
                <th width="120px" class="text-center">收案日</th>
                <th class="text-center">功能</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($open_cases as $caseType => $areaGroups)
                @foreach ($areaGroups as $area => $cases)
                  <tr class="table-primary">
                    <td colspan="6" class="fw-bold text-left">{{ $area }}</td>
                  </tr>
                  @foreach ($cases as $case)
                    <tr>
                      <td class="text-center">{{ $case->caseNoDisplay }}</td>
                      <td class="text-center">{{ $case->name }}</td>
                      <td class="text-center">{!! $gender[$case->gender] ?? '' !!}</td>
                      <td class="text-center">{{ $patient_type[$caseType] ?? '未知類型' }}</td>
                      <td class="text-center">{{ $case->open_date }}</td>
                      <td>
                        <button class="btn btn-sm btn-success edit-type-btn" style="font-size: 1.1rem !important;" data-id="{{ $case->caseID }}" data-caseno="{{ (string)$case->caseNoDisplay }}" data-type="{{ $case->case_type }}" data-casename="{{ $case->name }}" data-bs-toggle="modal" data-bs-target="#editcaseModal">
                          <i class="bi bi-pencil-square"></i>&nbsp;修改案號、類型
                        </button>
                        <button class="btn btn-sm btn-primary edit-area-btn" style="font-size: 1.1rem !important;" data-id="{{ $case->caseID }}" data-caseno="{{ (string)$case->caseNoDisplay }}" data-type="{{ $case->case_type }}" data-casename="{{ $case->name }}" data-area="{{ $case->areaID }}" data-bed="{{ $case->bedID }}" data-bs-toggle="modal" data-bs-target="#editareaModal">
                          <i class="bi bi-pencil-square"></i>&nbsp;變更區域
                        </button>
                        <button class="btn btn-sm close-btn" style="background-color:#e83e8c;color: #ffffff;font-size: 1.1rem !important;" data-caseno="{{ (string)$case->caseNoDisplay }}" data-id="{{ $case->caseID }}" data-opendate="{{ $case->open_date }}" data-type="{{ $case->case_type }}" data-area="{{ $case->areaID }}" data-casename="{{ $case->name }}" data-bs-toggle="modal" data-bs-target="#closecaseModal">
                        <i class="bi bi-person-x"></i>&nbsp;結案
                      </button>
                      </td>
                    </tr>
                  @endforeach
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
      </div>
    @endif
  </div>
  @foreach ($patient_type as $key => $value)
    <div class="tab-pane fade" id="content-{{ $key }}" role="tabpanel">
    @if (!isset($open_cases[$key]) || $open_cases[$key]->isEmpty()) 
      <div class="alert alert-warning text-center mt-3">
        🚨 目前沒有「{{ $value }}」類型的個案
      </div>
    @else
    <div class="row align-items-center mb-4">
      <div class="col-3">
        <h1 class="h3 text-gray-800 mb-0"></h1>
      </div>
      <div class="col-9 d-flex justify-content-end">
        <input type="text" class="form-control tableSearch" placeholder="🔍 搜尋..." style="width: 150px;">
      </div>
    </div>
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
          <table class="table table-striped table-hover custom-table searchable-table">
            <thead class="sticky-top table-dark">
              <tr>
                <th width="100x" class="text-center">案號</th>
                <th width="120px" class="text-center">姓名</th>
                <th width="60px" class="text-center">性別</th>
                <th width="120px" class="text-center">收案日</th>
                <th class="text-center">功能</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($open_cases[$key] as $area => $cases)
                <tr class="table-primary">
                  <td colspan="5" class="fw-bold text-left">{{ $area }}</td>
                </tr>
                @foreach ($cases as $case)
                  <tr>
                    <td class="text-center">{{ $case->caseNoDisplay }}</td>
                    <td class="text-center">{{ $case->name }}</td>
                    <td class="text-center">{!! $gender[$case->gender] ?? '' !!}</td>
                    <td class="text-center">{{ $case->open_date }}</td>
                    <td>
                      <button class="btn btn-sm btn-success edit-type-btn" style="font-size: 1.1rem !important;" data-id="{{ $case->caseID }}" data-caseno="{{ (string)$case->caseNoDisplay }}" data-type="{{ $case->case_type }}" data-casename="{{ $case->name }}" data-bs-toggle="modal" data-bs-target="#editcaseModal">
                        <i class="bi bi-pencil-square"></i>&nbsp;修改案號、類型
                      </button>
                      <button class="btn btn-sm btn-primary edit-area-btn" style="font-size: 1.1rem !important;" data-id="{{ $case->caseID }}" data-caseno="{{ (string)$case->caseNoDisplay }}" data-type="{{ $case->case_type }}" data-casename="{{ $case->name }}" data-area="{{ $case->areaID }}" data-bed="{{ $case->bedID }}" data-bs-toggle="modal" data-bs-target="#editareaModal">
                        <i class="bi bi-pencil-square"></i>&nbsp;變更區域
                      </button>
                      <button class="btn btn-sm close-btn" style="background-color:#e83e8c;color: #ffffff;font-size: 1.1rem !important;" data-caseno="{{ (string)$case->caseNoDisplay }}" data-id="{{ $case->caseID }}" data-opendate="{{ $case->open_date }}" data-type="{{ $case->case_type }}" data-area="{{ $case->areaID }}" data-casename="{{ $case->name }}" data-bs-toggle="modal" data-bs-target="#closecaseModal">
                        <i class="bi bi-person-x"></i>&nbsp;結案
                      </button>
                    </td>
                  </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
      </div>
    @endif
    </div>
  @endforeach
</div>
<!-- 編輯 Modal -->
<div class="modal fade" id="editcaseModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">編輯個案資料 <span id="editCaseName"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editForm">
          {{--  在 POST 請求中自動附加 CSRF Token，並在伺服器端驗證，以防止攻擊者偽造請求 --}}
          @csrf
          @method('PUT')
          <input type="hidden" id="editCaseId">

          <div class="mb-3">
            <label class="form-label">案號</label>
            <input type="text" class="form-control" id="editCaseNo" required>
          </div>

          <div class="mb-3">
            <label class="form-label">個案類型</label>
            <select class="form-control" id="editCaseType">
              @foreach ($patient_type as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-success w-100">儲存修改</button>
        </form>
      </div>
    </div>
  </div>
</div>
@include('includes.newcase')
@include('includes.closecase')
@include('includes.edit-case-area')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  document.querySelectorAll('.tableSearch').forEach(function(input) {
  input.addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let tables = document.querySelectorAll(".searchable-table");

    tables.forEach(table => {
      let rows = table.querySelectorAll("tbody tr");
      rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    });
  });
});

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

$(document).ready(function() {
  // 點擊「編輯」按鈕時，填入對應資料
  $(".edit-type-btn").click(function() {
    $("#editCaseId").val($(this).data("id"));
    $("#editCaseName").html($(this).data("casename"));
    $("#editCaseNo").val($(this).data("caseno"));
    $("#editCaseType").val($(this).data("type"));
  });

  // 提交表單並更新資料
  $("#editForm").submit(function(e) {
    e.preventDefault();

    let caseId = $("#editCaseId").val();
    let caseNo = $("#editCaseNo").val();
    let caseType = $("#editCaseType").val();
    let token = $("input[name=_token]").val();  //雖然有設置全域meta和app.js但沒有作用，還是要在ajax時增加送出token

    $.ajax({
      url: "/update-case/" + caseId,
      method: "PUT",
      data: {
        _token: token,
        caseNo: caseNo,
        caseType: caseType
      },
      success: function(response) {
        if (response.success) {
          alert("修改成功！");
          location.reload();
        } else {
          alert("修改失敗！");
        }
      },
      error: function() {
        alert("修改失敗！");
      }
    });
  });

  $(".edit-area-btn").click(function() {
    $("#editareaCaseId").val($(this).data("id"));
    $("#editareaCaseInfo").html("【"+$(this).data("casename")+" "+$(this).data("caseno")+"】");
    $("#editCaseName").html($(this).data("casename"));
    $("#editarea").val($(this).data("area"));
    $("#editarea_original").val($(this).data("area"));
    $("#editareaBed").val($(this).data("bed"));
  });

  // 提交表單並更新資料
  $("#editareaForm").submit(function(e) {
    e.preventDefault();

    let formData = {
      _token: $("input[name=_token]").val(),
      caseId: $("#editareaCaseId").val(),
      caseBed: $("#editareaBed").val(),
      caseArea: $("#editarea").val(),
      caseArea_original: $("#editarea_original").val()
    };

    $.ajax({
      url: "/update-area/" + $("#editareaCaseId").val(),
      method: "PUT",
      data: formData,
      dataType: "json",
      success: function(response) {
        console.log(response);
        if (response.success) {
          alert("修改成功！");
          // location.reload();
        } else {
          alert("修改失敗！"+response.message);
        }
      },
      error: function() {
        alert("修改失敗！"+response.message);
      }
    });
  });

  let today = new Date();
  let thisadYear = today.getFullYear(); // 取得民國年
  let maxadYear = thisadYear + 3; // 最大年份 = 今年 + 3 年
  $("#newCaseBD").datepicker({
    dateFormat: "yy/mm/dd", // yy 會解釋為 2 位數年份，但我們會手動轉換
    yearRange: "1911:"+maxadYear,
    changeMonth: true,
    changeYear: true,
    defaultDate: new Date(), // 預設為今天
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

  $("#newCaseDate").datepicker({
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
  
  $("#newcaseForm").submit(function(e) {
    e.preventDefault(); // 阻止表單送出

    let idNumber = $("#newCaseID").val();
    if (!isValidTaiwanID(idNumber)) {
      alert("身分證格式錯誤！");
      return;
    }
    // 🔍 先 AJAX 查詢是否已存在
    $.ajax({
      url: "/check-id-number",
      type: "POST",
      data: {
        _token: $("input[name=_token]").val(),
        id_number: idNumber
      },
      dataType: "json",
      success: function(response) {
        console.log("檢查結果：", response);
        if (response.exists) {
          alert("此身分證字號已存在，請勿重複新增！");
          return;
        } else {
          // 身分證沒有重複，繼續送出表單
          submitNewCase(); // 把表單送出的邏輯抽成一個函式
        }
      },
      error: function() {
        alert("檢查身分證時發生錯誤，請稍後再試！");
      }
    });
  });

  $(".close-btn").click(function() {
    $("#closeCaseId").val($(this).data("id"));
    $("#closeCaseInfo").html("【"+$(this).data("casename")+" "+$(this).data("caseno")+"】");
    $("#opendate").val($(this).data("opendate"));
    $("#caseType").val($(this).data("type"));
    $("#caseArea").val($(this).data("area"));
    $("#closeCaseNo").val($(this).data("caseno"));
  });
  
  $("#closeDate").datepicker({
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

  $('#closeReason').select2({
    placeholder: "請選擇結案原因",
    dropdownParent: $('#closecaseModal'), // 指定容器，防止被 Bootstrap Modal 蓋住
    width: '100%'  // 確保寬度正常
  });

  $('#closeReason').on('change', function () {
    const selectedText = $('#closeReason option:selected').text();
    if (selectedText === '其他') {
      $('#closeReasonOther').show();
    } else {
      $('#closeReasonOther').hide().val('');
    }
  });

  $("#closeForm").submit(function(e) {
    e.preventDefault();

    let formData = {
      _token: $("input[name=_token]").val(),
      caseId: $("#closeCaseId").val(),
      closeDate: $("#closeDate").val(),
      closeReason: $("#closeReason").val(),
      closeReasonOther: $("#closeReasonOther").val(),
      closefiller: $("#closefiller").val(),
      opendate: $("#opendate").val(),
      caseType: $("#caseType").val(),
      caseArea: $("#caseArea").val()
    };
   
    $.ajax({
      url: "/close-case/" + $("#closeCaseId").val(),
      method: "POST",
      data: formData,
      dataType: "json",
      success: function(response) {
        if (response.success) {
          alert("結案成功！");
          location.reload();
        } else {
          alert("結案失敗！");
        }
      },
      error: function() {
        alert("結案失敗！");
      }
    });
  });
});
// 表單送出邏輯抽成函式
function submitNewCase() {
  let formData = {
    _token: $("input[name=_token]").val(),
    name: $("#newCaseName").val(),
    gender: $("#newCaseGender").val(),
    id_number: $("#newCaseID").val(),
    birthday: $("#newCaseBD").val(),
    case_type: $("#newCaseType").val(),
    case_no: $("#newCaseNo").val(),
    area: $("#newCaseArea").val(),
    open_date: $("#newCaseDate").val()
  };

  $.ajax({
    url: "/new-case",
    type: "POST",
    data: formData,
    dataType: "json",
    success: function (response) {
      if (response.success) {
        alert("個案新增成功！");
        location.reload(); // 重新整理頁面
      } else {
        alert("錯誤：" + response.message);
      }
    },
    error: function (xhr) {
      alert("提交失敗，請檢查輸入資料！");
    }
  });
}


</script>
@endsection
