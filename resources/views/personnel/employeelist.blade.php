@extends('layouts.app')
@section('content')
<style>
  .custom-table tbody tr:hover {
    background-color: #d1ecf1 !important;  /* 淡藍色 */
    color: #750000 !important;
    font-weight: bold !important;
  }
  .custom-table {
    color: #272727 !important;
  }
</style>
@php
  $gender = config('public.gender');
@endphp
<div class="row align-items-center mb-4">
  <div class="col-3">
    <h1 class="h3 text-gray-800 mb-0">人事列表</h1>
  </div>
  <div class="col-9">
    <form method="GET" action="{{ route('hc-openlist') }}" class="d-flex align-items-center justify-content-end" id="regionForm">
      <div style="padding-left:10px">
        <a href="{{ route('hc-create') }}" class="btn text-white" style="background-color: orange;" data-bs-toggle="modal" data-bs-target="#newcaseModal">新增人員</a>
      </div>
    </form>
  </div>
</div>
<!-- Bootstrap Tabs for Case Types -->
<ul class="nav nav-tabs mt-3" id="caseTypeTabs" role="tablist">
  <!-- 🔹 Tabs 選項 -->
  <li class="nav-item">
    <a class="nav-link active fw-bold" id="tab-open" data-bs-toggle="tab" href="#content-open" role="tab">在職員工</a>
  </li>
  <li class="nav-item">
    <a class="nav-link fw-bold" id="tab-quit" data-bs-toggle="tab" href="#content-quit" role="tab">離職員工</a>
  </li>
</ul>
<div class="tab-content mt-3">
  <div class="tab-pane fade show active" id="content-open" role="tabpanel">
    @if ($open_staffs->isEmpty()) 
      <div class="alert alert-warning text-center mt-3">
        🚨 目前沒有任何員工
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
                <th width="100x" class="text-center">員工編號</th>
                <th width="120px" class="text-center">姓名</th>
                <th width="60px" class="text-center">性別</th>
                <th width="110px" class="text-center">職稱</th>
                <th width="200px" class="text-center">年資</th>
                <th class="text-center">功能</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($open_staffs as $cases => $case)
                <tr>
                  <td class="text-center">{{ $case->employeeNo }}</td>
                  <td class="text-center">{{ $case->name }}</td>
                  <td class="text-center">{!! $gender[$case->gender] ?? '' !!}</td>
                  <td class="text-center">{{ $case->official_title }}{{ $case->official_title_other ? "：". $case->official_title_other:"" }}</td>
                  <td class="text-center">{{ $case->start_workdate." ~ " }}</td>
                  <td>
                    <a href="{{ url('/personnel/form01/'.$case->employeeID.'') }}" class="btn btn-sm btn-info d-inline-block" style="font-size: 1rem !important;">
                      <i class="bi bi-journal-richtext"></i>&nbsp;基本資料
                    </a>
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
  <div class="tab-pane fade" id="content-quit" role="tabpanel">
    @if ($quit_staffs->isEmpty()) 
      <div class="alert alert-warning text-center mt-3">
        🚨 目前沒有任何員工
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
                <th width="100x" class="text-center">員工編號</th>
                <th width="120px" class="text-center">姓名</th>
                <th width="60px" class="text-center">性別</th>
                <th width="110px" class="text-center">職稱</th>
                <th width="300px" class="text-center">年資</th>
                <th class="text-center">功能</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($quit_staffs as $cases => $case)
                <tr>
                  <td class="text-center">{{ $case->employeeNo }}</td>
                  <td class="text-center">{{ $case->name }}</td>
                  <td class="text-center">{!! $gender[$case->gender] ?? '' !!}</td>
                  <td class="text-center">{{ $case->official_title }}{{ $case->official_title_other ? "：". $case->official_title_other:"" }}</td>
                  <td class="text-center">{{ $case->start_workdate." ~ ".$case->end_workdate." (".senioritywithyear($case->start_workdate, $case->end_workdate).")" }}</td>
                  <td>
                    
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
</script>
@endsection
