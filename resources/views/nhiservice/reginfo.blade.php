@extends('layouts.app')
@section('content')
<style>
	.radio {
    width: 20px !important;
    height: 20px !important;
    vertical-align: middle; /* 保持與文字垂直對齊 */
  }
	.table th,
	.table td {
		border: 2px solid #BEBEBE;
		vertical-align: middle !important;
	}
</style>
@php
  $patient_type = config('public.hc_patient_type');
	$citizen_status_array = config('public.citizen_status');
	$startdate = request('startdate');
	$enddate = request('enddate');
@endphp
<div class="container" style="max-width: 1400px;">
  <form method="POST" id="reg_form" action="{{ route('reginfo.save') }}">
		@csrf
		<div class="card mb-4">
			<div class="card-header" style="font-size:14pt !important;">掛號資訊</div>
				<div class="card-body">
					<table class="table align-middle">
						<tbody>
							<tr>
								<th class="table-success align-middle" style="width: 10%">申報補件</th>
								<td style="width: 40%">
									<div class="form-check form-check-inline">
										<input class="form-check-input radio" type="radio" name="shift" id="shift0" value="0" {{ optional($result)->shift == '0' ? 'checked' : '' }}>
										<label class="form-check-label" for="shift0">否</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input radio" type="radio" name="shift" id="shift1" value="1" {{ optional($result)->shift == '1' ? 'checked' : '' }}>
										<label class="form-check-label" for="shift1">是</label>
									</div>
									<span class="form-text text-danger">*選擇 "是"，此筆資料不會列入IC卡每日上傳，僅作月申報使用</span>
								</td>
								<th class="table-success align-middle" style="width: 10%">案件分類</th>
								<td style="width: 40%">
									<select name="D1" id="D1" class="form-control">
										<option value="" {{ optional($result)->D1 == '' ? 'selected' : '' }}></option>
										<option value="A1" {{ optional($result)->D1 == 'A1' ? 'selected' : '' }}>A1. 居家照護</option>
										<option value="A2" {{ optional($result)->D1 == 'A2' ? 'selected' : '' }}>A2. 精神疾病社區復健</option>
										<option value="A5" {{ optional($result)->D1 == 'A5' ? 'selected' : '' }}>A5. 安寧居家療護</option>
										<option value="A6" {{ optional($result)->D1 == 'A6' ? 'selected' : '' }}>A6. 護理之家居家照護</option>
										<option value="A7" {{ optional($result)->D1 == 'A7' ? 'selected' : '' }}>A7. 安養、養護機構院民之居家照護</option>al
										<option value="E1" {{ optional($result)->D1 == 'E1' ? 'selected' : '' }}>E1. 醫療給付改善方案及試辦計畫</option>
									</select>
								</td>
							</tr>
							<tr>
								<th class="table-success align-middle">就診日期</th>
								<td>
									<input type="text" class="form-control" name="A17" id="A17" value="{{ optional($result)->A17 ?? now() }}">
								</td>
								<th class="table-success align-middle">個案</th>
								<td>
									<select name="firmID" id="firmID" class="form-control" {{ (optional($result)->caseID!=""? 'disabled':'') }}>
										<option value="">請選擇</option>
										@foreach ($open_cases as $case)
											@php
												$case_status = (!empty($cases_latest_baseform[$case->caseID])?$cases_latest_baseform[$case->caseID]['citizen_status']:'');
												$case_status_other = (!empty($cases_latest_baseform[$case->caseID])?$cases_latest_baseform[$case->caseID]['citizen_status_other']:'');
												if($case_status!=""){
													$the_case_status = $citizen_status_array[$case_status].($case_status_other!=""?' '.$case_status_other:'');
												}else{
													$the_case_status = '尚未指定身分別';
												}
											@endphp
										<option value="{{ $case->caseID }}" data-birth="{{ $case->birthdate }}" data-idno="{{ $case->IdNo }}" data-type="{{ $case->case_type }}" data-case_status="{{ $the_case_status }}" {{ optional($result)->caseID == $case->caseID ? 'selected' : '' }}>
											{{ "【".(string)$case->caseNoDisplay."】".$case->name." (".($patient_type[$case->case_type]??'').")" }}
										</option>
										@endforeach
									</select>
									<input type="hidden" name="caseID" id="caseID" value="{{ optional($result)->caseID }}">
								</td>
							</tr>
							<tr>
								<th class="table-success align-middle">身份證號</th>
								<td>
									<input type="text" readonly class="form-control" name="A12" id="A12" value="{{ optional($result)->A12 }}">
								</td>
								<th class="table-success align-middle">生日</th>
								<td>
									<input type="text" readonly class="form-control" name="A13" id="A13" value="{{ optional($result)->A13 }}">
								</td>
							</tr>
							<tr>
								<th class="table-success align-middle">身份別</th>
								<td><span id="citizen_status"></span></td>
								<th class="table-success align-middle">就醫科別</th>
								<td>
									<select name="D8" id="D8" class="form-control">
										<option value="" {{ optional($result)->D8 == '' ? 'selected' : '' }}>請選擇</option>
										<option value="EA" {{ optional($result)->D8 == 'EA' ? 'selected' : '' }}>EA 居家護理</option>
										<option value="AC" {{ optional($result)->D8 == 'AC' ? 'selected' : '' }}>AC 胸腔內科</option>
									</select>
								</td>
							</tr>
							<tr>
								<th class="table-success align-middle">就診類別</th>
								<td>
									<div class="form-check form-check-inline">
										<input class="form-check-input radio" type="radio" name="A23" id="A231" value="01" {{ optional($result)->A23 == '01' ? 'checked' : '' }}>
										<label class="form-check-label" for="A231">訪視(1)</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input radio" type="radio" name="A23" id="A232" value="AH" {{ optional($result)->A23 == 'AH' ? 'checked' : '' }}>
										<label class="form-check-label" for="A232">訪視(2含以上)</label>
									</div>
								</td>
								<th class="table-success align-middle">狀態</th>
								<td>
									
								</td>
							</tr>
							<tr>
								<th class="table-success align-middle">夜間加成</th>
								<td colspan="3">
									<div class="form-check form-check-inline">
										<input class="form-check-input radio" type="radio" name="night_plus" id="night_plus1" value="0" {{ optional($result)->night_plus == '0' ? 'checked' : '' }}>
										<label class="form-check-label" for="night_plus1">否</label>
									</div>
									<div class="form-check form-check-inline">
										<input class="form-check-input radio" type="radio" name="night_plus" id="night_plus2" value="1" {{ optional($result)->night_plus == '1' ? 'checked' : '' }}>
										<label class="form-check-label" for="night_plus2">是</label>
									</div>
                	<form>
										<button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#infoModal">說明</button>
									</form>
								</td>
							</tr>
							<tr>
								<th class="table-success align-middle">上次看診資訊</th>
								<td colspan="3">
									<span id="last_reginfo"></span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="text-center">
				<input type="hidden" name="HospID" value="{{ optional($result)->A14 ?? $clientinfo_arrayinfo[session('client_id')]['client_hospID']}}">
				<input type="hidden" name="D4" id="D4" value="{{ optional($result)->D4 }}"/>
				<input type="hidden" name="type" id="type" value="{{ optional($result)->case_type }}">
				<input type="hidden" name="action" value="{{ request('action') }}">
				<a href="/nhiservice/registration?startdate={{ $startdate }}&enddate={{ $enddate }}" class="btn" style="background-color: #BEBEBE;margin-right:25px">返回列表</a>
				@if(request('action') == 'delete')
					<button type="submit" class="btn btn-danger">取消看診!</button>
				@elseif(request('action') == 'after')
					<button type="submit" class="btn btn-primary">補卡</button>
				@else
					<button type="submit" class="btn btn-warning">儲存</button>
				@endif
			</div>
    </form>
	</div>
	<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="infoModalLabel">說明</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
      </div>
      <div class="modal-body">
        這是詳細的說明內容。
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
      </div>
    </div>
  </div>
</div>
<script>
	document.addEventListener("DOMContentLoaded", function() {   
		$('#firmID').select2({
      placeholder: "請選擇或輸入個案名稱",
      width: '100%', // 讓選單寬度適應
    });

		$('#firmID').on('change', function () {
			let selectedOption = $(this).find('option:selected');
      let birthdate = selectedOption.data('birth');
      let IdNo = selectedOption.data('idno');
			let type = selectedOption.data('type');
			let case_status = selectedOption.data('case_status');
			let firmID = $(this).val();

			$("#caseID").val(firmID);
			$("#A13").val(birthdate);
			$("#A12").val(IdNo);
			$("#type").val(type);
			$("#citizen_status").html(case_status);
			// 呼叫後端 API 查詢上次紀錄
			if (firmID) {
				$.ajax({
					url: "/get-case-reginfo",
					method: "POST",
					data: {
						_token: $("input[name=_token]").val(),
						caseID: firmID
					},
					dataType: "json",
					success: function (res) {
						if (res && res.last_date) {
							$('#last_reginfo').text(res.last_date); // 填入畫面上
							$('#D8').val(res.last_D8);
							$('#D4').val(res.this_D4);
						} else {
							$('#last_reginfo').text('未有看診資訊');
						}
					},
					error: function () {
						$('#last_reginfo').text('查詢失敗');
					}
				});
			}
    });

		$("#A17").datetimepicker({ 
			format: 'Y-m-d H:i:s',
			mask: false
		});

		$('form').on('submit', function(e) {
			e.preventDefault();
			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: $(this).serialize(),
				success: function(response) {
					if (window.opener && !window.opener.closed) {
					const openerUrl = new URL(window.opener.location.href);
					const startdate = openerUrl.searchParams.get("startdate");
					const enddate = openerUrl.searchParams.get("enddate");
					//重導主視窗，保有參數
					window.opener.location.href = `/nhiservice/registration?startdate=${startdate}&enddate=${enddate}`;
				}
				window.close();
				}
			});
		});
	});   
</script>
<br>
@endsection
