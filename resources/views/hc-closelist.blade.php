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
    <h1 class="h3 text-gray-800 mb-0">結案列表</h1>
  </div>
</div>
<div class="tab-content mt-3">
  <div class="tab-pane fade show active" id="content-all" role="tabpanel">
    @if ($close_cases->isEmpty()) 
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
                <th width="120px" class="text-center">結案日</th>
                <th width="300px" class="text-center">結案原因</th>
                <th class="text-center">功能</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($close_cases as $cases => $case)
              <tr>
                <td class="text-center">{{ $case->caseNoDisplay }}</td>
                <td class="text-center">{{ $case->name }}</td>
                <td class="text-center">{!! $gender[$case->gender] ?? '' !!}</td>
                <td class="text-center">{{ $patient_type[$case->close_type] ?? '未知類型' }}</td>
                <td class="text-center">{{ $case->open_date }}</td>
                <td class="text-center">{{ $case->close_date }}</td>
                <td class="text-left">{{ $close_reason[$case->reason] ?? '未知原因' }}{{ $case->memo ? '：' . $case->memo : '' }}</td>
                <td>
                {{-- 如果 caseID 不在 case_open 內，顯示 "重新收案" 按鈕 --}}
                  @if (!in_array($case->caseID, $open_case_ids))
                    <button class="btn btn-sm btn-info reopen-btn" style="font-size: 1rem !important;" data-id="{{ $case->caseID }}" data-opendate="{{ $case->open_date }}" data-closedate="{{ $case->close_date }}" data-type="{{ $case->close_type }}" data-area="{{ $case->areaID }}" data-bed="{{ $case->bedID }}" data-casename="{{ $case->name }}" data-caseno="{{ $case->caseNoDisplay }}" data-bs-toggle="modal" data-bs-target="#reopenModal">
                      <i class="bi bi-box-arrow-in-left"></i>&nbsp;重新收案
                    </button>
                  @else
                    <!-- <button class="btn btn-sm btn-danger delete-close-btn" style="font-size: 1rem !important;" data-id="{{ $case->caseID }}" data-opendate="{{ $case->open_date }}" data-closedate="{{ $case->close_date }}" data-caseno="{{ $case->caseNoDisplay }}" data-casename="{{ $case->name }}" data-bs-toggle="modal" data-bs-target="#delete-closeModal">
                      <i class="bi bi-trash3"></i>&nbsp;刪除
                    </button> -->
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
      </div>
    @endif
  </div>
</div>
@include('includes.reopen-case')
@include('includes.delete-close')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $("#reopenDate").datepicker({
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

    $(".reopen-btn").click(function() {
      $("#reopenCaseId").val($(this).data("id"));
      $("#reopenInfo").html("【"+$(this).data("casename")+" "+$(this).data("caseno")+"】");
      $("#closeArea").val($(this).data("area"));
      $("#reopenArea").val($(this).data("area"));
      $("#caseType").val($(this).data("type"));
      $("#reopenType").val($(this).data("type"));
      $("#opendate").val($(this).data("opendate"));
      $("#closedate").val($(this).data("closedate"));
      $("#closeBed").val($(this).data("bed"));

      let selectedArea = $(this).data("area");
      $("#reopenArea").val(selectedArea).change(); // .change() 讓選單 UI 立即更新
      let selectedType = $(this).data("type");
      $("#reopenType").val(selectedType).change();
    });

    $("#reopenForm").submit(function(e) {
      e.preventDefault();

      let formData = {
        _token: $("input[name=_token]").val(),
        caseId: $("#reopenCaseId").val(),
        reopenDate: $("#reopenDate").val(),
        reopenArea: $("#reopenArea").val(),
        reopenType: $("#reopenType").val(),
        opendate: $("#opendate").val(),
        closedate: $("#closedate").val(),
        caseType: $("#caseType").val(),
        closeArea: $("#closeArea").val(),
        closeBed: $("#closeBed").val()
      };
    
      $.ajax({
        url: "/reopen-case/" + $("#reopenCaseId").val(),
        method: "POST",
        data: formData,
        dataType: "json",
        success: function(response) {
          if (response.success) {
            alert("重新收案成功！");
            location.reload();
          } else {
            alert("錯誤："+response.message);
          }
        },
        error: function() {
          alert("錯誤：");
        }
      });
    });

    $(".delete-close-btn").click(function() {
      $("#deleteCaseId").val($(this).data("id"));
      $("#deleteInfo").html("【"+$(this).data("casename")+" "+$(this).data("caseno")+"】");
      $("#deleteopendate").val($(this).data("opendate"));
      $("#deleteclosedate").val($(this).data("closedate"));
    });

    $("#deleteForm").submit(function(e) {
      e.preventDefault();

      let formData = {
        _token: $("input[name=_token]").val(),
        caseId: $("#deleteCaseId").val(),
        opendate: $("#deleteopendate").val(),
        closedate: $("#deleteclosedate").val()
      };
    
      $.ajax({
        url: "/delete-close/" + $("#deleteCaseId").val(),
        method: "POST",
        data: formData,
        dataType: "json",
        success: function(response) {
          console.log(response);
          if (response.success) {
            alert("刪除成功！");
            location.reload();
          } else {
            alert("錯誤："+response.message);
          }
        },
        error: function() {
          alert("錯誤!");
        }
      });
    });
  });
</script>
@endsection
