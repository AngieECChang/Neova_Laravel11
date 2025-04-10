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
  .font-control:focus{
    color: #3C3C3C	!important;
  }
  .card-header {
    background-color:rgb(37, 162, 102) !important;
    padding: 0.25rem 1rem !important;
    font-size:14pt !important;
  }
  .card-body {
    padding: 0.5rem 1rem !important;
    font-size:13pt !important;
  }
  .ui-datepicker {
    z-index: 1060 !important; /* bootstrap modal 的 z-index 是 1050 */
  }
  .table th,
	.table td {
		vertical-align: middle !important;
	}
  
</style>
@php
  $patient_type = config('public.hc_patient_type');
@endphp
<div class="row align-items-center mb-4">
  <div class="col-1 col-md-2">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <h1 class="h4 text-gray-800 mb-0">掛號/看診列表</h1>
      </div>
    </div>
  </div>
  <div class="col-8 col-md-8 d-flex justify-content-end">
    <form method="GET" class="d-flex align-items-center gap-2">
      <div class="card mb-2" style="width: auto;margin-right:25px !important;">
        <div class="card-header text-white" style="background-color:#4e73df !important;">
          日期區間
        </div>
        <div class="card-body d-flex align-items-center gap-3 p-3" style="background-color:	#D2E9FF">
          <input type="text" name="startdate" value="{{ request('startdate')??date('Y-m-d') }}" class="form-control datepick" style="width: 120px;" autocomplete="off">
          <span class="mx-1">~</span>
          <input type="text" name="enddate" value="{{ request('enddate')??date('Y-m-d') }}" class="form-control datepick" style="width: 120px;margin-right:15px !important;" autocomplete="off">
          <button type="submit" class="btn btn-outline-primary">查詢</button>
        </div>
      </div>
    </form>
    <form>
      <div class="card mb-2" style="width: auto;">
        <div class="card-header bg-success text-white">
            掛號作業
        </div>
        <div class="card-body d-flex align-items-center gap-3 p-3" style="background-color:#F0FFF0">
          <a href="{{ route('reginfo.edit', ['action' => 'new']) }}" class="btn text-black" style="background-color: #96FED1; border-color: #009100;margin-right:25px !important;" target="_blank">無卡掛號</a>
          <button type="button" class="btn text-black" style="background-color: #96FED1; border-color: #009100;" onclick="window.location.href='UARK://newreg//0117//{{ session('nOrgID') }}//{{ request('pid') }}////';">讀卡掛號</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-3 col-md-2">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <form>
          <div class="card mb-2" style="width: auto;">
            <div class="card-header text-white" style="background-color:#f6c23e !important;">
                看診作業
            </div>
            <div class="card-body d-flex align-items-center gap-3 p-3" style="background-color: #FFFCEC">
              <button type="button" class="btn text-black" style="background-color:rgb(247, 247, 184); border-color: #FFD306;" onclick="window.location.href='UARK://newreg//0117//{{ session('nOrgID') }}//{{ request('pid') }}////';">讀卡看診</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="tab-content mt-3">
<div class="tab-pane fade show active" id="content-all" role="tabpanel">
    @if ($registration_list->isEmpty()) 
      <div class="alert alert-warning text-center mt-3">
        🚨 今日尚無 掛號/看診 個案
      </div>
    @else
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
          <table id="registrationTable" class="table table-striped table-hover custom-table searchable-table">
            <thead class="sticky-top table-dark">
              <tr>
                <th width="140x" class="text-center">掛號</th>
                <th width="140x" class="text-center">看診</th>
                <th width="140px" class="text-center">就診日期</th>
                <th width="90px" class="text-center">就診序號</th>
                <th width="90px" class="text-center">就醫類別</th>
                <th width="120px" class="text-center">個案姓名</th>
                <th width="120px" class="text-center">個案類型</th>
                <th width="90px" class="text-center">看診狀態</th>
                <th width="90px" class="text-center">VPN上傳</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($registration_list as $key => $value)
                @php
                  $oldDate ='';
                  $IC_PT_TIME = (date('Y', strtotime($value->A17)) - 1911) . date('mdHis', strtotime($value->A17));
                  $PT_BD_IC = str_pad((date('Y', strtotime($value->A13)) - 1911), 3, "0", STR_PAD_LEFT) . date('md', strtotime($value->A13));
                  $oldDate = substr($value->A54, 0, 3) + 1911 . "-" . substr($value->A54, 3, 2) . "-" . substr($value->A54, 5, 2);

                  if($value->finished==1){
                    $show_status = '<i class="bi bi-check-lg fw-bold" style="color: green;font-size:1.5rem !important;"></i>';
                  }else{
                    $show_status = '<i class="bi bi-x-lg fw-bold" style="color: red;font-size:1.5rem !important;"></i>';
                  }
                  if($value->xmlupload==1){
                    $show_xmlupload = '<i class="bi bi-check-lg fw-bold" style="color: green;font-size:1.5rem !important;"></i>';
                  }else{
                    $show_xmlupload = '<i class="bi bi-x-lg fw-bold" style="color: red;font-size:1.5rem !important;"></i>';
                  }
                  $show_xmlupload .= ( $value->shift!=''?'<br>申報補件不需上傳':'');
                  $show_A18 = ($value->owed=="1" && $value->A18==""?"<font color='red'>欠卡</font>": $value->A18);

                  preg_match('/[0-9]{4}/', $value->A18, $Match_array);

                  $editUrl = route('reginfo.edit', ['REGID' => $value->REGID, 'action' => 'edit', 'startdate' => request('startdate'), 'enddate' => request('enddate')]);
                  $deleteUrl = route('reginfo.edit', ['REGID' => $value->REGID, 'action' => 'delete', 'startdate' => request('startdate'), 'enddate' => request('enddate')]);
                  $viewUrl = route('reginfo.edit', ['REGID' => $value->REGID, 'action' => 'view', 'startdate' => request('startdate'), 'enddate' => request('enddate')]);
                  $cardviewUrl = route('consultation.edit', ['REGID' => $value->REGID, 'caseID' => $value->caseID, 'action' => 'view', 'startdate' => request('startdate'), 'enddate' => request('enddate'), 'nocard' => '1']);
                  $cardUrl = route('consultation.edit', ['REGID' => $value->REGID, 'caseID' => $value->caseID, 'action' => 'edit', 'startdate' => request('startdate'), 'enddate' => request('enddate'), 'nocard' => '1']);

                  $show_reg = '<div class="d-flex flex-column align-items-center">';
                  $show_card = '<div class="d-flex flex-column align-items-center">';

                  if ($value->status == '9' || $value->A23 == 'ZB') {
                      $show_reg .= "<a href=\"{$viewUrl}\">已取消掛號</a>";
                      if ($value->A23 == 'ZB') {
                        $show_card .= "<a href=\"{$cardviewUrl}\" class=\"btn btn-warning mb-1\">查看</a>";
                      }
                  } else {
                      $show_reg .= "<a href=\"{$editUrl}\" class=\"btn btn-info mb-1\">編輯</a>";

                      if ($value->finished == '0') {
                          $show_reg .= "<a href=\"{$deleteUrl}\" class=\"btn btn-info\">取消掛號</a>";
                      } elseif ($value->finished == '1') {
                          preg_match('/[0-9]{4}/', $value->A18, $Match_array);
                          if ($value->status == '2') {
                              if ($value->A23 == 'AH') {
                                  // AH 退掛處理
                              } elseif ($value->A23 == '01') {
                                  // 01 退掛處理
                              }
                          } elseif (count($Match_array) == 0) {
                              $show_reg .= "<a href=\"{$deleteUrl}\" class=\"btn btn-danger\" style=\"background-color:#FF9797\">異常，取消</a>";
                          }
                      }

                      // 顯示讀卡按鈕
                      if ($value->A23 != 'ZB' && !(count($Match_array) == 0 && $value->finished == 1 && substr($value->A17, 0, 10) != date("Y-m-d"))) {
                          $show_card .= "<a href=\"#\" class=\"btn btn-warning mb-1\">讀卡</a>";
                      }

                      $show_card .= "<a href=\"{$cardUrl}\" class=\"btn btn-warning mb-1\">無卡</a>";
                  }

                  $show_reg .= '</div>';
                  $show_card .= '</div>';
                  
                  

                  if($value->A19==2){
                    $show_A19 = "<br><font color='#ff69b4'>(補 ".$oldDate ." 看診)</font>";
                  }else{
                    $show_A19 = "";
                  }
                @endphp
              <tr>
                <td class="text-center">
                  {!! $show_reg !!}
                </td>
                <td class="text-center">{!! $show_card !!}</td>
                <th class="text-center">{{ $value->A17}}{!! $show_A19 !!}</th>
                <th class="text-center">{!! $show_A18 !!}</th>
                <th class="text-center">{{ $value->A23 }}</th>
                <th class="text-center">{{ $value->name }}</th>
                <th class="text-center">{{ $patient_type[$value->case_type] ?? '未知類型' }}<br>{{ "(".$value->D1."/".$value->D8.")"}}</th>
                <th class="text-center">{!! $show_status !!}</th>
                <th class="text-center">{!! $show_xmlupload !!}</th>
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
  $(document).ready(function() {
    $('#registrationTable').DataTable({
      paging: false,
      order: [[2, 'desc']], // 預設就診日期欄降冪排序（第2欄 index 是 1）
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/zh-HANT.json"
      },
      columnDefs: [
        { orderable: false, targets: [0, 1] } // 禁用「看診按鈕」與「VPN上傳」欄排序
      ]
    });

    $(".datepick").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true,
      defaultDate: new Date(),
      monthNames: ["一月", "二月", "三月", "四月", "五月", "六月",
                  "七月", "八月", "九月", "十月", "十一月", "十二月"],
      monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月",
                        "7月", "8月", "9月", "10月", "11月", "12月"],
      dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
    });
  }); 
</script>
@endsection
