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
</style>
@php
  $patient_type = config('public.hc_patient_type');
@endphp
<div class="row align-items-center mb-4">
  <div class="col-4 col-md-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <h1 class="h4 text-gray-800 mb-0">掛號/看診列表</h1>
      </div>
    </div>
  </div>

  <div class="col-6 col-md-6 d-flex justify-content-end">
    <form method="GET" class="d-flex align-items-center gap-2">
      <div class="card mb-2" style="width: auto;margin-right:25px !important;">
        <div class="card-header text-white" style="background-color:#4e73df !important;">
          日期區間
        </div>
        <div class="card-body d-flex align-items-center gap-3 p-3">
          <input type="text" name="startdate" value="{{ request('startdate') }}" class="form-control datepick" style="width: 120px;" autocomplete="off">
          <span class="mx-1">~</span>
          <input type="text" name="enddate" value="{{ request('enddate') }}" class="form-control datepick" style="width: 120px;margin-right:15px !important;" autocomplete="off">
          <button type="submit" class="btn btn-outline-primary btn-sm">查詢</button>
        </div>
      </div>
    </form>
    <form>
      <div class="card mb-2" style="width: auto;">
        <div class="card-header bg-success text-white">
            掛號作業
        </div>
        <div class="card-body d-flex align-items-center gap-3 p-3">
          <a href="{{ route('reginfo') }}" class="btn text-white" style="background-color: deeppink; border-color: deeppink;margin-right:25px !important;" target="_blank">無卡掛號</a>
          <button type="button" class="btn btn-primary" style="background-color: orange; border-color: orange;" onclick="window.location.href='UARK://newreg//0117//{{ session('nOrgID') }}//{{ request('pid') }}////';">讀卡掛號</button>
        </div>
      </div>
    </form>
  </div>
  <div class="col-2 col-md-2">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
      <div class="d-flex align-items-center gap-2 flex-wrap">
       
      </div>
    </div>
  </div>
</div>

<div class="tab-content mt-3">
<div class="tab-pane fade show active" id="content-all" role="tabpanel">
    @if ($registration_list->isEmpty()) 
      <div class="alert alert-warning text-center mt-3">
        🚨 目前沒有任何個案
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
                @endphp
              <tr>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <th class="text-center">{{ $value->A17.($value->A19==2?"<br><font color='#ff69b4'>(補 ".$oldDate ." 看診)":"") }}</th>
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
<!-- 新增處置 -->
<div class="modal fade" id="newtreatmentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">新增處置代碼</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="newtreatmentForm">
          {{-- CSRF Token 防止跨站請求攻擊 --}}
          @csrf
          <div id="formErrors" class="alert alert-danger d-none"></div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newcategory" class="form-label m-0" style="line-height:20px !important;">類別</label>
                </div>
                <div class="col-10">
                  <div style="height: 38px; display: flex; align-items: center;">
                    @foreach ([2 => '醫療服務費', 3 => '特殊材料費', 4 => '不得計價的診療費用或材料'] as $value => $label)
                    <div class="form-check form-check-inline">
                      <input class="form-check-input radio" type="radio" name="newcategory" id="newcategory{{ $value }}" value="{{ $value }}">
                      <label class="form-check-label" for="newcategory{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newgroup" class="form-label m-0" style="line-height:20px !important;">組別</label>
                </div>
                <div class="col-10">
                  <div style="height: 38px; display: flex; align-items: center;">
                  @foreach ([1 => '醫師', 2 => '護理師', 3 => '呼吸治療師'] as $value => $label)
                    <div class="form-check form-check-inline">
                      <input class="form-check-input radio" type="checkbox" name="newgroup[]" id="newgroup{{ $value }}" value="{{ $value }}">
                      <label class="form-check-label" for="newgroup{{ $value }}">{{ $label }}</label>
                    </div>
                  @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="row">
                <div class="col-4 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newshort_code" class="form-label m-0">簡易代碼</label>
                </div>
                <div class="col-8">
                  <input type="text" class="form-control" name="newshort_code" id="newshort_code" placeholder="請輸入簡易代碼">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-4 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newtreatment_code" class="form-label m-0">健保代碼</label>
                </div>
                <div class="col-8">
                  <input type="text" class="form-control" name="newtreatment_code" id="newtreatment_code" placeholder="請輸入健保代碼">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-3 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newtreatment_name_zh" class="form-label m-0">處置項目中文名稱</label>
                </div>
                <div class="col-9">
                  <input type="text" class="form-control" name="newtreatment_name_zh" id="newtreatment_name_zh" placeholder="請輸入處置項目中文名稱">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-3 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newtreatment_name_en" class="form-label m-0">處置項目英文名稱</label>
                </div>
                <div class="col-9">
                  <input type="text" class="form-control" name="newtreatment_name_en" id="newtreatment_name_en" placeholder="請輸入處置項目英文名稱">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newmodel_no" class="form-label m-0">型號</label>
                </div>
                <div class="col-10">
                  <input type="text" class="form-control" name="newmodel_no" id="newmodel_no" placeholder="請輸入型號">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="row">
                <div class="col-4 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newunit" class="form-label m-0">單位</label>
                </div>
                <div class="col-8">
                  <input type="text" class="form-control" name="newunit" id="newunit" placeholder="請輸入單位">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-4 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newpoints" class="form-label m-0">價格/點數</label>
                </div>
                <div class="col-8">
                  <input type="text" class="form-control" name="newpoints" id="newpoints" placeholder="請輸入價格/點數">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label class="form-label m-0">生效日期</label>
                </div>
                <div class="col-10">
                  <div class="d-flex gap-2 align-items-center">
                    <input type="text" class="form-control datepick" name="newstart_date" id="newstart_date" placeholder="生效起日" style="width: 150px;">
                    <span>&nbsp;~ &nbsp;</span>
                    <input type="text" class="form-control datepick" name="newend_date" id="newend_date" placeholder="生效迄日" style="width: 150px;" value="2910-12-31">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="newcomments" class="form-label m-0">備註</label>
                </div>
                <div class="col-10">
                  <textarea class="form-control w-100" name="newcomments" id="newcomments" rows="4" maxlength="1000" style="resize: vertical;"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex gap-2 mt-5">
            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-warning w-50">新增處置代碼</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- 編輯處置 -->
<div class="modal fade" id="edittreatmentModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">編輯處置代碼</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="edit-treatment-form">
          {{-- CSRF Token 防止跨站請求攻擊 --}}
          @csrf
          <div id="formupdateErrors" class="alert alert-danger d-none"></div>
          <input type="hidden" id="edit-id">
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="editcategory" class="form-label m-0" style="line-height:20px !important;">類別</label>
                </div>
                <div class="col-10">
                  <div style="height: 38px; display: flex; align-items: center;">
                    @foreach ([2 => '醫療服務費', 3 => '特殊材料費', 4 => '不得計價的診療費用或材料'] as $value => $label)
                    <div class="form-check form-check-inline">
                      <input class="form-check-input radio" type="radio" name="editcategory" id="editcategory{{ $value }}" value="{{ $value }}">
                      <label class="form-check-label" for="editcategory{{ $value }}">{{ $label }}</label>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="editgroup" class="form-label m-0" style="line-height:20px !important;">組別</label>
                </div>
                <div class="col-10">
                  <div style="height: 38px; display: flex; align-items: center;">
                  @foreach ([1 => '醫師', 2 => '護理師', 3 => '呼吸治療師'] as $value => $label)
                    <div class="form-check form-check-inline">
                      <input class="form-check-input radio" type="checkbox" name="editgroup[]" id="editgroup{{ $value }}" value="{{ $value }}">
                      <label class="form-check-label" for="editgroup{{ $value }}">{{ $label }}</label>
                    </div>
                  @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="row">
                <div class="col-4 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="editshort_code" class="form-label m-0">簡易代碼</label>
                </div>
                <div class="col-8">
                  <input type="text" class="form-control" name="editshort_code" id="editshort_code" placeholder="請輸入簡易代碼">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-4 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="edittreatment_code" class="form-label m-0">健保代碼</label>
                </div>
                <div class="col-8">
                  <input type="text" class="form-control" name="edittreatment_code" id="edittreatment_code" placeholder="請輸入健保代碼">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-3 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="edittreatment_name_zh" class="form-label m-0">處置項目中文名稱</label>
                </div>
                <div class="col-9">
                  <input type="text" class="form-control" name="edittreatment_name_zh" id="edittreatment_name_zh" placeholder="請輸入處置項目中文名稱">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-3 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="edittreatment_name_en" class="form-label m-0">處置項目英文名稱</label>
                </div>
                <div class="col-9">
                  <input type="text" class="form-control" name="edittreatment_name_en" id="edittreatment_name_en" placeholder="請輸入處置項目英文名稱">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="editmodel_no" class="form-label m-0">型號</label>
                </div>
                <div class="col-10">
                  <input type="text" class="form-control" name="editmodel_no" id="editmodel_no" placeholder="請輸入型號">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <div class="row">
                <div class="col-4 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="editunit" class="form-label m-0">單位</label>
                </div>
                <div class="col-8">
                  <input type="text" class="form-control" name="editunit" id="editunit" placeholder="請輸入單位">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-4 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="editpoints" class="form-label m-0">價格/點數</label>
                </div>
                <div class="col-8">
                  <input type="text" class="form-control" name="editpoints" id="editpoints" placeholder="請輸入價格/點數">
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label class="form-label m-0">生效日期</label>
                </div>
                <div class="col-10">
                  <div class="d-flex gap-2 align-items-center">
                    <input type="text" class="form-control datepick" name="editstart_date" id="editstart_date" placeholder="生效起日" style="width: 150px;">
                    <span>&nbsp;~ &nbsp;</span>
                    <input type="text" class="form-control datepick" name="editend_date" id="editend_date" placeholder="生效迄日" style="width: 150px;" value="2910-12-31">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-12">
              <div class="row">
                <div class="col-2 bg-success text-white d-flex align-items-center justify-content-center rounded-start">
                  <label for="editcomments" class="form-label m-0">備註</label>
                </div>
                <div class="col-10">
                  <textarea class="form-control w-100" name="editcomments" id="editcomments" rows="4" maxlength="1000" style="resize: vertical;"></textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex gap-2 mt-5">
            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-warning w-50">編輯處置代碼</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- 刪除處置 -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">處置代碼 </h5>
        <button type="button" class="btn-delete" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="delete-treatment-form">
          {{-- CSRF Token 防止跨站請求攻擊 --}}
          @csrf
          <input type="hidden" id="delete-id">
          <span id="treatmentInfo"></span>
          <div class="d-flex gap-2 mt-5">
            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-danger w-50">確認刪除</button>
          </div>
        </form>
      </div>
    </div>
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

    const fieldNames = {
      'short_code': '簡易代碼',
      'treatment_code': '健保代碼',
      'treatment_name_zh': '處置中文名稱',
      'start_date': '生效起日',
      'end_date': '生效迄日',
      'points': '價格/點數',
      'category': '類別',
      'group': '組別'
    };

    $("#newtreatmentForm").submit(function(e) {
      e.preventDefault(); // 阻止表單送出
      let formData = {
        _token: $("input[name=_token]").val(),
        short_code: $("#newshort_code").val(),
        treatment_code: $("#newtreatment_code").val(),
        treatment_name_zh: $("#newtreatment_name_zh").val(),
        treatment_name_en: $("#newtreatment_name_en").val(),
        model_no: $("#newmodel_no").val(),
        unit: $("#newunit").val(),
        points : $("#newpoints ").val(),
        start_date: $("#newstart_date").val(),
        end_date: $("#newend_date").val(),
        comments: $("#newcomments").val(),
        category: $("input[name='newcategory']:checked").val(),
        group: $("input[name='newgroup[]']:checked").map(function () {
          return $(this).val();
        }).get()
      };
      $('#formErrors').addClass('d-none').empty();
      $.ajax({
        url: "/newtreatment",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function(response) {
          console.log("檢查結果：", response);
          if (response.success) {
            alert("新增成功！");
            location.reload();
          } else {
            alert("錯誤：" + response.messages);
          }
        },
        error: function(xhr) {
          if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors || {};
            // 顯示錯誤
            let allMessages = '';
            Object.entries(errors).forEach(([field, messages]) => {
              const label = fieldNames[field] || field;
              const cleanMessage = messages[0].replace(new RegExp(field, 'gi'), '').trim();
              allMessages += `${label}：${cleanMessage}\n`;
            }); 
            $('#formErrors').removeClass('d-none').html(allMessages.replace(/\n/g, '<br>'));          
          } else {
            alert("系統錯誤，請稍後再試！");
          }
        }
      });
    });

    $('.edit-type-btn').on('click', function () {
      const id = $(this).data('id');
      $("#edit-id").val(id);
      $.ajax({
        url: `/get_treatment/${id}`, 
        method: 'GET',
        success: function (response) {
          $('#editshort_code').val(response.short_code);
          $('#edittreatment_code').val(response.treatment_code);
          $('#edittreatment_name_zh').val(response.treatment_name_zh);
          $('#edittreatment_name_en').val(response.treatment_name_en);
          $('#editmodel_no').val(response.model_no);
          $('#editunit').val(response.unit);
          $('#editpoints').val(response.points);
          $('#editstart_date').val(response.start_date);
          $('#editend_date').val(response.end_date);
          $('#editcomments').val(response.comments);
          $('input[name="editcategory"]').prop('checked', false); // 清除所有選取
          $('input[name="editgroup[]"]').prop('checked', false);
          $('#editcategory' + response.category).prop('checked', true); // 勾選對應的
          if (Array.isArray(response.group)) {
            console.log('array');
            response.group.forEach(function (val) {
              $('#editgroup' + val).prop('checked', true);
            });
          }
        },
        error: function () {
          alert('無法載入資料');
        }
      });
    });

    $('#edit-treatment-form').on('submit', function (e) {
      e.preventDefault();
      const id = $('#edit-id').val();
      let formData = {
        _token: $("input[name=_token]").val(),
        short_code: $("#editshort_code").val(),
        treatment_code: $("#edittreatment_code").val(),
        treatment_name_zh: $("#edittreatment_name_zh").val(),
        treatment_name_en: $("#edittreatment_name_en").val(),
        model_no: $("#editmodel_no").val(),
        unit: $("#editunit").val(),
        points : $("#editpoints ").val(),
        start_date: $("#editstart_date").val(),
        end_date: $("#editend_date").val(),
        comments: $("#editcomments").val(),
        category: $("input[name='editcategory']:checked").val(),
        group: $("input[name='editgroup[]']:checked").map(function () {
          return $(this).val();
        }).get()
      };
      $('#formupdateErrors').addClass('d-none').empty();
      $.ajax({
        url: `/update_treatment/${id}`,
        method: 'PUT',
        data: formData,
        dataType: "json",
        success: function () {
          alert('更新成功');
          location.reload();
        },
        error: function(xhr) {
          if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors || {};
            // 顯示錯誤
            let allMessages = '';
            Object.entries(errors).forEach(([field, messages]) => {
              const label = fieldNames[field] || field;
              const cleanMessage = messages[0].replace(new RegExp(field, 'gi'), '').trim();
              allMessages += `${label}：${cleanMessage}\n`;
            }); 
            $('#formupdateErrors').removeClass('d-none').html(allMessages.replace(/\n/g, '<br>'));          
          } else {
            alert("系統錯誤，請稍後再試！");
          }
        }
      });
    });

    $(".close-btn").click(function() {
      $("#delete-id").val($(this).data("id"));
      console.log($(this).data("name"));
      const zh = $(this).data("name") || "";
      $("#treatmentInfo").html(
        $(this).data("short_code") + "<br>" +
        $(this).data("treatment_code") + "<br>" +
        zh );
      });

    $("#delete-treatment-form").submit(function(e) {
      e.preventDefault();

      let formData = {
        _token: $("input[name=_token]").val(),
        id: $("#delete-id").val()
      };
    
      $.ajax({
        url: "/delete_treatment/" + $("#delete-id").val(),
        method: "POST",
        data: formData,
        dataType: "json",
        success: function(response) {
          if (response.success) {
            alert("刪除成功！");
            location.reload();
          } else {
            alert("錯誤："+response.message);
          }
        },
        error: function() {
          alert("錯誤！");
        }
      });
    });
    
  }); 
</script>
@endsection
