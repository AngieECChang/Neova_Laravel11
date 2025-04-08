@extends('layouts.app')
@section('content')
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
    font-size:13pt !important;
  }
  th {
    text-align: center !important;
    background-color: #D2A2CC !important;
    font-size:14pt !important;
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
    <h1 class="h3 text-gray-800 mb-2">系統設定</h1>
  </div>
  <div class="col-6">
  </div>
</div>
<div class="card shadow-sm mb-2">
  <div class="card-body" style="max-height: 570px; overflow-y: auto;">
    <form id="form1" method="POST" action="{{ route('hcmanagement.save') }}">
      @csrf
      <table class="table table-bordered align-middle">
        <tbody>
          <tr>
            <th width="200">單位名稱</th>
            <td colspan="3">
                <input type="text" class="form-control" name="client_name" id="client_name" value="{{ optional($result)->client_name ?? '' }}">
            </td>
          </tr>
          <tr>
            <th>醫事服務機構代碼</th>
            <td width="400">
              <input type="text" class="form-control" name="client_hospID" value="{{ optional($result)->client_hospID }}">
            </td>
            <th width="150">政府立案字號</th>
            <td>
              <input type="text" class="form-control" name="client_govno" value="{{ optional($result)->client_govno }}">
            </td>
          </tr>
          <tr>
            <th>地址</th>
            <td colspan="3">
              <div class="row align-items-end g-2">
                <div class="col-auto">
                  <label for="city" class="form-label" style="font-size: 10pt;">縣市</label>
                  <select name="client_city" id="city" class="form-control">
                    <option value="">請選擇</option>
                    @foreach($cities as $city)
                      <option value="{{ $city }}" {{ optional($result)->client_city === $city ? 'selected' : '' }}>
                        {{ $city }}
                      </option>
                    @endforeach  
                  </select>
                </div>
                <div class="col-auto">
                    <label for="town" class="form-label" style="font-size: 10pt;">市區鄉鎮</label>
                    <select name="client_town" id="town" class="form-control">
                      <option value="">請先選擇縣市</option>
                    </select>
                </div>
                <div class="col">
                    <label for="address_detail" class="form-label" style="font-size: 10pt;">地址</label>
                    <input type="text" name="client_address" id="lane" class="form-control" value="{{ optional($result)->client_address ?? '' }}">
                </div>
              </div>
            </td>
          </tr>
          <tr>
            <th>負責人</th>
            <td width="400">
              <input type="text" class="form-control" name="client_CEO" value="{{ optional($result)->client_CEO }}">
            </td>
            <th width="150">電話</th>
            <td>
              <input type="text" class="form-control" name="client_tel" value="{{ optional($result)->client_tel }}">
            </td>
          </tr>
          <tr>
            <th colspan="4" style="background-color: #FFC78E !important;">「居家護理照護管理系統」介接資訊</th>
          </tr>
          <tr>
            <th>AGENCY_ID</th>
            <td>
              <input type="text" class="form-control" name="csd_agency_id" value="{{ optional($result)->csd_agency_id }}">
            </td>
            <th>SecretKey</th>
            <td>
              <input type="text" class="form-control" name="csd_secret_key" value="{{ optional($result)->csd_secret_key }}">
            </td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" name="formID" value="form01">
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary">儲存</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {   
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
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
  });
</script>
@endsection