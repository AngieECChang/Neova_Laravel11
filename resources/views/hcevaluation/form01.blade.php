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

  /* 員工 */
  option.employee {
    color: #071B37;
  }

  /* 被選取的項目 — 無論在職或離職 */
  select option:checked {
    color: #071B37;
    font-weight: bold;
    background-color: #fff3cd; /* 淡黃色背景 */
  }

  input,
  select,
  textarea {
    color: #071B37 !important; /* 深藍色 */
  }

</style>
<div class="row align-items-center mt-4">
  <div class="col-6">
    <h1 class="h3 text-gray-800 mb-2">基本資料</h1>
  </div>
  <div class="col-6">
    <form method="GET" class="d-flex align-items-center justify-content-end" id="regionForm">
      <div class="text-end mb-3">
        <button type="button" class="btn btn-secondary" onclick="window.print()">
          列印本頁
        </button>
        <a href="{{ route('hcevaluation.print', ['formID' => request()->segment(2) ,'caseID' => request()->segment(3),'date' => request()->segment(4)??'']) }}" target="_blank" class="btn btn-outline-primary">
          前往列印頁
        </a>
      </div>
    </form>
  </div>
</div>
<div class="card shadow-sm mb-2">
  <div class="card-body" style="max-height: 570px; overflow-y: auto;">
    <form id="form1" method="POST" action="{{ route('hcevaluation.save') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="caseID" value="{{ $the_case->caseID ?? '0' }}">
      <table class="table table-bordered align-middle">
        <tbody>
          <tr>
            <th width="120" class="table-success">姓名</th>
            <td width="280">
                <input type="text" class="form-control input-150" name="name" id="name" value="{{ optional($the_case)->name ?? '' }}" placeholder="{{ optional($the_case)->name ?? '請選擇個案' }}">
            </td>
            <th width="120" class="table-success">性別</th>
            <td width="*" colspan="2">
              <div class="form-check form-check-inline">
                <input class="form-check-input radio" type="radio" name="gender" id="gender1" value="1" {{ optional($the_case)->gender == "1" ? "checked" : "" }}>
                <label class="form-check-label radio" for="gender1"> 男 </label>
              </div>
              <div class="form-check form-check-inline ">
                <input class="form-check-input radio" type="radio" name="gender" id="gender0" value="0" {{ optional($the_case)->gender == "0" ? "checked" : "" }}>
                <label class="form-check-label" for="gender0"> 女 </label>
              </div>
              <div class="form-check form-check-inline">              
                <input class="form-check-input radio" type="radio" name="gender" id="gender2" value="2" {{ optional($the_case)->gender == "2" ? "checked" : "" }}> 
                <label class="form-check-label" for="gender2">其他 </label>
              </div>
            </td>
            <td rowspan="4" width="180" class="text-center">
              <div class="mb-2">
                <img src="{{ optional($the_case)->photo_url ?? asset('images/noImage.png') }}" alt="大頭照"
                    class="img-thumbnail" style="max-width: 180px; max-height: 180px;">
              </div>
              <div class="mb-2">
                <input class="form-control form-control-sm" type="file" name="photo_url" accept="image/*">
                <input type="hidden" name="old_photo_url" value="{{  optional($the_case)->photo_url ?? '' }}">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">生日</th>
            <td>
              <div class="d-flex align-items-center">
                民國 <input type="text" class="form-control" name="birthdate" id="birthdate" value="{{ optional($the_case)->birthdate && optional($the_case)->birthdate!='0000-00-00' ? dateTo_c($the_case->birthdate) : '' }}" style="width: 150px;margin-left: 8px;">
              </div>
              <input type="hidden" id="birthdate_AD" value="{{ optional($the_case)->birthdate }}">
            </td>
            <th class="table-success">身分證字號</th>
            <td colspan="4">
              <input type="text" class="form-control input-150" name="IdNo" value="{{ optional($the_case)->IdNo }}" required>
            </td>
          </tr>
          <tr>
            <th class="table-success">聯絡電話</th>
            <td>            
              <input type="text" class="form-control input-150" name="PhoneNumber" value="{{ optional($result)->PhoneNumber ?? '' }}" style="margin-left: 8px;">            
            </td>
            <th class="table-success">個案類型</th>
            <td colspan="4">
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
            <td colspan="6">
              <div class="form-check form-check-inline">
                @foreach (getOptionList('CaseSource') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="CaseSource" id="CaseSource{{$key}}" value="{{$key}}" {{ optional($result)->CaseSource == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="CaseSource{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="CaseSource_other" value="{{ optional($result)->CaseSource_other }}">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">地址</th>
            <td colspan="6">
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
            <th class="table-success">診斷碼</th>
            <td colspan="6">
              <div class="row g-2">
                @for ($i = 1; $i <= 6; $i++)
                  <div class="col-md-6">
                    <div class="input-group">
                      <input type="text" name="diag_{{ $i }}_ICD10" id="diag_{{ $i }}_ICD10" class="form-control icd-autocomplete mt-2" placeholder="診斷ICD10" data-index="{{ $i }}" autocomplete="off" value="{{ optional($result)->{'diag_'.$i.'_ICD10'} ?? '' }}" style="max-width: 120px;">
                      <input type="text" id="diag_{{ $i }}_ICD10name" name="diag_{{ $i }}_ICD10name" class="form-control mt-2" placeholder="診斷名稱" value="{{ optional($result)->{'diag_'.$i.'_ICD10name'} ?? '' }}">
                      <button type="button" class="btn btn-outline-secondary mt-2" onclick="clearDiag({{ $i }})">清除</button>
                    </div>
                  </div>
                  <div class="col-md-6">
                  </div>
                @endfor
              </div>
            </td>
          </tr>
          <tr>
            @php
              $citizen_status = array("1"=>"一般","2"=>"榮民","3"=>"中低收入戶","4"=>"低收入戶","5"=>"榮民/眷","6"=>"重大傷病","7"=>"福保","8"=>"農保","9"=>"原住民","10"=>"其他");
            @endphp
            <th class="table-success">身分別</th>
            <td colspan="6">
              <div class="form-check form-check-inline">
                @foreach($citizen_status as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="citizen_status" id="citizen_status{{$key}}" value="{{$key}}" {{ optional($result)->citizen_status == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="citizen_status{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="citizen_status_other" value="{{ optional($result)->citizen_status_other }}">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">教育程度</th>
            <td colspan="6">
              <div class="form-check form-check-inline">
                @foreach (getOptionList('Education') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="Education" id="Education{{$key}}" value="{{$key}}" {{ optional($result)->Education == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="Education{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="Education_other" value="{{ optional($result)->Education_other }}">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">婚姻狀況</th>
            <td colspan="6">
              <div class="form-check form-check-inline">
                @foreach (getOptionList('Marriage') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="Marriage" id="Marriage{{$key}}" value="{{$key}}" {{ optional($result)->Marriage == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="Marriage{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="Marriage_other" value="{{ optional($result)->Marriage_other }}">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">宗教信仰</th>
            <td colspan="6">
              <div class="form-check form-check-inline">
                @foreach (getOptionList('Religion') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="Religion" id="Religion{{$key}}" value="{{$key}}" {{ optional($result)->Religion == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="Religion{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="Religion_other" value="{{ optional($result)->Religion_other }}">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">主要職業</th>
            <td colspan="6">
              <div class="form-check form-check-inline">
                @foreach (getOptionList('ExJob') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="ExJob" id="ExJob{{$key}}" value="{{$key}}" {{ optional($result)->ExJob == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="ExJob{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="ExJob_other" value="{{ optional($result)->ExJob_other }}">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">家庭經濟<br>狀況</th>
            <td colspan="3">
              <div class="form-check form-check-inline">
                @foreach (getOptionList('Economic') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="Economic" id="Economic{{$key}}" value="{{$key}}" {{ optional($result)->Economic == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="Economic{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="Economic_other" value="{{ optional($result)->Economic_other }}">
              </div>
            </td>
            <th class="table-success" width="120">是否有福利</th>
            <td colspan="2">
              <div class="form-check form-check-inline">
                <input class="form-check-input radio" type="radio" name="HasWelfare" id="HasWelfare0" value="0" {{ optional($result)->HasWelfare == "0" ? "checked" : "" }}>
                <label class="form-check-label radio" for="HasWelfare0"> 無 </label>
              </div>
              <div class="form-check form-check-inline ">
                <input class="form-check-input radio" type="radio" name="HasWelfare" id="HasWelfare1" value="1" {{ optional($result)->HasWelfare == "1" ? "checked" : "" }}>
                <label class="form-check-label" for="HasWelfare1"> 有 </label>
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">福利種類</th>
            <td colspan="6">
              <div class="form-check form-check-inline">
                @foreach (getOptionList('Welfare') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="Welfare" id="Welfare{{$key}}" value="{{$key}}" {{ optional($result)->Welfare == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="Welfare{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="Welfare_other" value="{{ optional($result)->Welfare_other }}">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">身障類別</th>
            <td colspan="6">
              @php
                $chunks = chunkWithBootstrapCol(getOptionList('DisabilityType'), 2);
                $selectedTypes = json_decode(optional($result)->DisabilityType ?? '[]', true);
              @endphp
              @foreach ($chunks as $row)
                <div class="row mb-2">
                  @foreach ($row as $item)
                    <div class="{{ $item['colClass'] }}">
                      <div class="form-check">
                      <input class="form-check-input radio" type="checkbox" name="DisabilityType[]" id="DisabilityType{{ $item['key'] }}" value="{{ $item['key'] }}" {{ in_array($item['key'], $selectedTypes ?? []) ? 'checked' : '' }}>
                        <label class="form-check-label" for="DisabilityType{{ $item['key'] }}">&nbsp;
                          {{ $item['label'] }}
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endforeach
            </td>
          </tr>
          <tr>
            <th class="table-success">重大傷病</th>
            <td colspan="6">
              <div class="row g-2">
                @for ($i = 1; $i <= 6; $i++)
                  <div class="col-md-6">
                    <div class="input-group">
                      <input type="text" name="catastrophic_code_{{ $i }}" id="catastrophic_code	_{{ $i }}" class="form-control mt-2" placeholder="重大傷病代碼" data-index="{{ $i }}" autocomplete="off" value="{{ optional($result)->{'catastrophic_code_'.$i} ?? '' }}" style="max-width: 120px;">
                      <input type="text" name="catastrophic_name_{{ $i }}" id="catastrophic_name	_{{ $i }}" class="form-control mt-2" placeholder="重大傷病名稱" autocomplete="off" value="{{ optional($result)->{'catastrophic_name_'.$i} ?? '' }}" style="max-width:200px;">
                      <input type="text" name="catastrophic_startdate_{{ $i }}" id="catastrophic_startdate_{{ $i }}" class="form-control mt-2" placeholder="起始日期" autocomplete="off" value="{{ optional($result)->{'catastrophic_startdate_'.$i} ?? '' }}" style="max-width: 120px;">
                      <input type="text" name="catastrophic_enddate_{{ $i }}" id="catastrophic_enddate_{{ $i }}" class="form-control mt-2" placeholder="結束日期" autocomplete="off" value="{{ optional($result)->{'catastrophic_enddate_'.$i} ?? '' }}" style="max-width: 120px;">
                    </div>
                  </div>
                  <div class="col-md-6">
                  </div>
                @endfor
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">主要照顧者</th>
            <td colspan="6">
              <div class="form-check form-check-inline">
                關係：                
                @foreach (getOptionList('CaregiverID') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="CaregiverID" id="CaregiverID{{$key}}" value="{{$key}}" {{ optional($result)->CaregiverID == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="CaregiverID{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="CaregiverID_other" value="{{ optional($result)->CaregiverID_other }}">
              </div>
              <div class="form-check form-check-inline">
                姓名：<input type="text" class="form-control input-150" name="CaregiverName" value="{{ optional($result)->CaregiverName }}">
                ，聯絡電話：<input type="text" class="form-control input-150" name="CaregiverTel" value="{{ optional($result)->CaregiverTel }}">
              </div>
              <div class="row align-items-end g-2">
                <div class="col-auto">
                  <label for="CaregiverAddress" class="form-label" style="font-size: 10pt;">縣市</label>
                  <select name="CaregiverAddress_city" id="CaregiverAddress_city" class="form-control">
                    <option value="">請選擇</option>
                    @foreach($cities as $city)
                      <option value="{{ $city }}" {{ optional($result)->CaregiverAddress_city === $city ? 'selected' : '' }}>
                        {{ $city }}
                      </option>
                    @endforeach  
                  </select>
                </div>
                <div class="col-auto">
                  <label for="CaregiverAddress_town" class="form-label" style="font-size: 10pt;">市區鄉鎮</label>
                  <select name="CaregiverAddress_town" id="CaregiverAddress_town" class="form-control">
                    <option value="">請先選擇縣市</option>
                  </select>
                </div>
                <div class="col">
                  <label for="address_detail" class="form-label" style="font-size: 10pt;">地址</label>
                  <input type="text" name="CaregiverAddress_lane" id="CaregiverAddress_lane" class="form-control" value="{{ optional($result)->CaregiverAddress_lane ?? '' }}" style="width:300px;">
                </div>
              </div>                    
            </td>
          </tr>
          <tr>
            <th class="table-success" width="120">緊急聯絡人</th>
            <td colspan="6">
              <div class="form-check form-check-inline">
                姓名：<input type="text" class="form-control input-150" name="EgyContactName" value="{{ optional($result)->EgyContactName }}">
                ，聯絡電話1：<input type="text" class="form-control input-150" name="EgyContactTel1" value="{{ optional($result)->EgyContactTel1 }}">
                ，聯絡電話2：<input type="text" class="form-control input-150" name="EgyContactTel2" value="{{ optional($result)->EgyContactTel2 }}">
              </div> 
              <br><br>
              <div class="form-check form-check-inline">
                @foreach (getOptionList('EgyContactRelation') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="EgyContactRelation" id="EgyContactRelation{{$key}}" value="{{$key}}" {{ optional($result)->EgyContactRelation == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="EgyContactRelation{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="EgyContactRelation_other" value="{{ optional($result)->EgyContactRelation_other }}">
              </div>    
              
            </td>
          </tr>
          <tr>
            <th class="table-success">主要醫療決定者</th>
            <td colspan="6">
              <div class="form-check form-check-inline">
                @foreach (getOptionList('DecisionMakerRelation') as $key => $value)
                  <div class="form-check form-check-inline">
                    <input class="form-check-input radio" type="radio" name="DecisionMakerRelation" id="DecisionMakerRelation{{$key}}" value="{{$key}}" {{ optional($result)->DecisionMakerRelation == $key ? "checked" : "" }}>
                    <label class="form-check-label" for="DecisionMakerRelation{{$key}}"> {{$value}} </label>
                  </div>
                @endforeach
                <input type="text" class="form-control input-150" name="DecisionMakerRelation_other" value="{{ optional($result)->DecisionMakerRelation_other }}">
              </div>    
            </td>
          </tr>
          <tr>
            <th class="table-success">一年內有無重大事件發生</th>
            <td colspan="6">
              @php
                $options = getOptionList('MEventItem');
                $selectedTypes = json_decode(optional($result)->MEventItem ?? '[]', true);
              @endphp

              <div class="d-flex align-items-center flex-wrap overflow-auto" style="white-space: nowrap;">
                <div class="form-check form-check-inline me-3">
                  <input class="form-check-input radio" type="radio" name="MEvent" id="MEvent0" value="0" {{ optional($result)->MEvent == "0" ? "checked" : "" }}>
                  <label class="form-check-label" for="MEvent0">無</label>
                </div>
                <div class="form-check form-check-inline me-3">
                  <input class="form-check-input radio" type="radio" name="MEvent" id="MEvent1" value="1" {{ optional($result)->MEvent == "1" ? "checked" : "" }}>
                  <label class="form-check-label" for="MEvent1">有</label>
                </div>
                <span class="me-3">，項目：</span>
                @foreach ($options as $key => $label)
                  <div class="form-check form-check-inline me-3">
                    <input class="form-check-input radio" type="checkbox" name="MEventItem[]" id="MEventItem{{ $key }}" value="{{ $key }}" {{ in_array($key, $selectedTypes ?? []) ? 'checked' : '' }}>
                    <label class="form-check-label" for="MEventItem{{ $key }}">
                      {{ $label }}
                    </label>
                  </div>
                @endforeach
                <input type="text" class="form-control" name="MEventItem_other" value="{{ optional($result)->MEventItem_other }}" style="width:300px;">
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">個案描述</th>
            <td colspan="6">
              <div class="form-check form-check-inline w-100">
                <textarea class="form-control w-100" name="CaseDesc" rows="4" maxlength="1000" style="resize: vertical;">{{ optional($result)->CaseDesc }}</textarea>
              </div>
            </td>
          </tr>
          <tr>
            <th class="table-success">評估日期</th>
            <td>
              <input type="text" class="form-control" name="date" id="date" value="{{ optional($result)->date??now()->format('Y-m-d') }}" required style="width: 150px;">
            </td>
            <th class="table-success">評估人員</th>
            <td colspan="3">
              @php
                $employeeExists = $open_staffs_withIdNo->contains('employeeID', optional($result)->employeeID);
              @endphp
              <select name="employeeID" id="employeeID" class="form-control" style="width:250px;">
                <option value=""></option>
                @foreach ($open_staffs_withIdNo as $emp)
                  <option value="{{ $emp->employeeID }}" class="employee" {{ optional($result)->employeeID == $emp->employeeID ?'selected':''}} data-id="{{ $emp->IdNo }}">
                    {{ maskIdNo($emp->IdNo) }} - {{ $emp->name }}
                  </option>
                @endforeach
                @if (!$employeeExists && optional($result)->employeeID)
                  <option value="{{ $result->employeeID }}" class="employee" selected>
                    {{ maskIdNo($all_staffs_array[$result->employeeID]->IdNo) }} - {{ $all_staffs_array[$result->employeeID]->name }}（離職）
                  </option>
                @endif
              </select>
              <input type="hidden" name="NurseID" id="NurseID" value="{{ optional($result)->NurseID }}">
            </td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" name="formID" value="hcevaluation01">
      <div>
        @include('hcevaluation.form01_medical_saved')
      </div>
      <div>
        @include('hcevaluation.form01_medical')
      </div>
      <br>
      <div>
        @include('hcevaluation.form01_relative_saved')
      </div>
      <div>
        @include('hcevaluation.form01_relative')
      </div>
      @if(request()->segment(3)!="0")
        <div class="text-center mt-4">
          <button type="submit" class="btn btn-primary">儲存</button>
        </div>
      @endif
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

    $("#date").datepicker({
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

    $("input[id^=catastrophic_startdate_]").datepicker({
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

    $("input[id^=catastrophic_enddate_]").datepicker({
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

    $('#city').on('change', function () {
      let city = $(this).val();
      $('#town').html('<option value="">載入中...</option>');
      let selectedTown = "{{ optional($result)->town }}"; // 加在 Blade 裡，回填用

      $.get('/api/towns', { city: city }, function (data) {
        let options = '<option value="">請選擇</option>';
        data.forEach(function (town) {
          let selected = (town === selectedTown) ? 'selected' : '';
          options += `<option value="${town}" ${selected}>${town}</option>`;
        });
        $('#town').html(options);
      });
    });
    
    $('#CaregiverAddress_city').on('change', function () {
      let city = $(this).val();
      $('#CaregiverAddress_town').html('<option value="">載入中...</option>');
      let Caregiver_selectedTown = "{{ optional($result)->CaregiverAddress_town }}"; // 加在 Blade 裡，回填用

      $.get('/api/towns', { city: city }, function (data) {
        let options = '<option value="">請選擇</option>';
        data.forEach(function (town) {
          let selected = (town === Caregiver_selectedTown) ? 'selected' : '';
          options += `<option value="${town}" ${selected}>${town}</option>`;
        });
        $('#CaregiverAddress_town').html(options);
      });
    });

    $('#employeeID').on('change', function () {
      let selectedOption = $(this).find('option:selected');
      let dataId = selectedOption.data('id');
      $('#NurseID').val(dataId);
    });

    $('.icd-autocomplete').each(function () {
      let $input = $(this);
      let index = $input.data('index');

      $input.autocomplete({
        source: function (request, response) {
          $.ajax({
            url: '{{ route("icd.lookup") }}',
            dataType: 'json',
            data: { q: request.term },
            success: function (data) {
              response($.map(data, function (item) {
                return {
                  label: item.icd10_new + ' - ' + item.icd10_cname,
                  value: item.icd10_new,
                  icd_name: item.icd10_cname
                };
              }));
            }
          });
        },
        minLength: 3,
        delay: 300,
        select: function (event, ui) {
          // $(`#diag_${index}_ICD10`).val(ui.item.icd10);
          $(`#diag_${index}_ICD10name`).val(ui.item.icd_name);
        }
      });
    });
  });

  function clearDiag(index) {
    $(`#diag_${index}_ICD10`).val('');
    $(`#diag_${index}_ICD10name`).val('');
  }
</script>
@endsection