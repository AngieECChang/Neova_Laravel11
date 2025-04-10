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
  $patient_type_array = config('public.hc_patient_type');
	$gender_text = config('public.gender_text');
	$citizen_status_array = config('public.citizen_status');
	$startdate = request('startdate');
	$enddate = request('enddate');

	$case_type = $case_data->case_type;
	$case_status = (!empty($cases_latest_baseform[$case_data->caseID])?$cases_latest_baseform[$case_data->caseID]['citizen_status']:'');
	$case_status_other = (!empty($cases_latest_baseform[$case_data->caseID])?$cases_latest_baseform[$case_data->caseID]['citizen_status_other']:'');
	if($case_status!=""){
		$the_case_status = $citizen_status_array[$case_status].($case_status_other!=""?' '.$case_status_other:'');
	}else{
		$the_case_status = '尚未指定身分別';
	}

	if($result->finished=="1"){  //已看診完抓當下紀錄的
		$A25 = $result->A25;
		$A25_ICD = $result->A25_ICD;
		$A26 = $result->A26;
		$A26_ICD = $result->A26_ICD;
		$A27 = $result->A27;
		$A27_ICD = $result->A27_ICD;
		$A28 = $result->A28;
		$A28_ICD = $result->A28_ICD;
		$A29 = $result->A29;
		$A29_ICD = $result->A29_ICD;
		$A30 = $result->A30;
		$A30_ICD = $result->A30_ICD;
	}else{
		$A25 = $latest_baseform->diag_1_ICD10;
		$A26 = $latest_baseform->diag_2_ICD10;
		$A27 = $latest_baseform->diag_3_ICD10;
		$A28 = $latest_baseform->diag_4_ICD10;
		$A29 = $latest_baseform->diag_5_ICD10;
		$A30 = $latest_baseform->diag_6_ICD10;
		$A25_ICD = $latest_baseform->diag_1_ICD10name;
		$A26_ICD = $latest_baseform->diag_2_ICD10name;		
		$A27_ICD = $latest_baseform->diag_3_ICD10name;		
		$A28_ICD = $latest_baseform->diag_4_ICD10name;		
		$A29_ICD = $latest_baseform->diag_5_ICD10name;		
		$A30_ICD = $latest_baseform->diag_6_ICD10name;
	}

	$title_array = array("醫師" => "1", "護理師" => "2", "呼吸治療師" => "3");
	$staffMap = [];
	foreach ($open_staffs_withIdNo as $emp) {
		$emp_group = $emp->official_title;
		if($emp_group!=""){
			$staffMap[$title_array[$emp_group]][] = ['id' => $emp->employeeID, 'name' => $emp->name]; 
		}
	}
@endphp
<div class="card shadow-sm mb-2">
  <div class="card-body" style="max-height: 570px; overflow-y: auto;">
  	<form method="POST" id="reg_form" action="{{ route('consultation.save') }}">
		@csrf
		<div class="card mb-4">
			<div class="card-header" style="font-size:14pt !important;">掛號基本資訊</div>
				<div class="card-body" style="font-size:14pt !important;">
					姓名：{{ $case_data->name}}&emsp;&emsp;性別：{{ $gender_text[$case_data->gender] }}&emsp;&emsp;生日：{{ $case_data->birthdate}}&emsp;&emsp;個案類型：{{ optional($result)->case_type?$patient_type_array[$result->case_type]:'' }}&emsp;&emsp;身分別：{{ $the_case_status }}
				</div>
				<div class="card-body" style="font-size:14pt !important;">
					就診類別：
					<div class="form-check form-check-inline">
						<input class="form-check-input radio" type="radio" name="A23" id="A231" value="01" {{ optional($result)->A23 === '01' ? 'checked' : '' }}>
						<label class="form-check-label" for="A231">訪視(1)</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input radio" type="radio" name="A23" id="A232" value="AH" {{ optional($result)->A23 === 'AH' ? 'checked' : '' }}>
						<label class="form-check-label" for="A232">訪視(2含以上)</label>
					</div>
					&emsp;&emsp;夜間加成：
					<div class="form-check form-check-inline">
						<input class="form-check-input radio" type="radio" name="night_plus" id="night_plus1" value="0" {{ optional($result)->night_plus == '0' ? 'checked' : '' }}>
						<label class="form-check-label" for="night_plus1">否</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input radio" type="radio" name="night_plus" id="night_plus2" value="1" {{ optional($result)->night_plus == '1' ? 'checked' : '' }}>
						<label class="form-check-label" for="night_plus2">是</label>
					</div>
					&emsp;&emsp;
					<button class="btn btn-info major-illness-btn" style="font-size: 14pt !important;" data-id="{{ $case_data->caseID }}" data-bs-toggle="modal" data-bs-target="#major_illness_Modal">重大傷病紀錄
					</button>
					&emsp;&emsp;
					<button class="btn btn-info btn-history-order" style="font-size: 14pt !important;" data-id="{{ $case_data->caseID }}" data-bs-toggle="modal" data-bs-target="#history_Modal">歷史醫令紀錄</button>
				</div>
			</div>
		</div>
		<div class="card mb-4">
			<div class="card-header" style="font-size:14pt !important;">看診資訊 <font color="red">{{ (optional($result)->status=="9" || optional($result)->A23=="ZB"?"【已取消看診】":"") }}</font></div>
				<div class="card-body">
					<table class="table align-middle">
						<tbody>
							<tr>
								<th class="table-success align-middle" style="width: 10%">診斷碼</th>
								<td colspan="5">
									<div class="row g-2">
										@for ($i = 1; $i <= 6; $i++)
											@php
												$item_name = "A".($i+24);
												$item_name2 = "A".($i+24)."_ICD";
											@endphp
											<div class="col-md-6">
												<div class="input-group">
													<input type="text" name="{{ $item_name }}" id="diag_{{ $i }}_ICD10" class="form-control icd-autocomplete mt-2" placeholder="診斷ICD10" data-index="{{ $i }}" autocomplete="off" value="{{ $$item_name }}" style="max-width: 120px;">
													<input type="text" name="{{ $item_name }}_ICD" id="diag_{{ $i }}_ICD10name" class="form-control mt-2" placeholder="診斷名稱" value="{{ $$item_name2 }}">
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
								<th colspan="6" class="align-middle" style="background-color:#FFF4C1">處置醫令</th>
							</tr>
							<tr>
								<td colspan="6">
									@include('nhiservice.addnhicode')
								</td>
							</tr>
							<tr>
								<th class="table-success align-middle">部分負擔代碼</th>
								<td colspan="5">
									<select name="D15" id="D15" onchange="amountValue()">
                    <option value="">:::請選擇:::</option>
                    <option value="K00" {{ (optional($result)->D15=="K00"?'selected':'') }}>K00.自行部分負擔5%</option>
                    <option value="001" {{ (optional($result)->D15=="001"?'selected':'') }}>001.重大傷病</option>
                    <option value="003" {{ (optional($result)->D15=="003"?'selected':'') }}>003.合於社會救助法規定之低收入戶之保險對象(第五類之保險對象)</option>
                    <option value="004" {{ (optional($result)->D15=="004"?'selected':'') }}>004.榮民、榮民遺眷之家戶代表(第六類第一目之保險對象)</option>
                    <option value="006" {{ (optional($result)->D15=="006"?'selected':'') }}>006.勞工保險被保險人因職業傷害或職業病門診者</option>
                    <option value="007" {{ (optional($result)->D15=="007"?'selected':'') }}>007.山地離島地區之就醫</option>
                    <option value="008" {{ (optional($result)->D15=="008"?'selected':'') }}>008.經離島醫院診所轉診至台灣本島門診及急診就醫者</option>
                    <option value="009" {{ (optional($result)->D15=="009"?'selected':'') }}>009.本局其他規定免部分負擔者</option>
                    <option value="902" {{ (optional($result)->D15=="902"?'selected':'') }}>902.三歲以下兒童醫療補助計畫</option>
                	</select>{{ (optional($result)->D52 == "02" ?  " (個案為醫缺地區「自行部分負擔」再減20%)": "") }}
								</td>
							</tr>
							<tr>
								<th class="table-success align-middle">合計點值</th>
								<td width="18%">
									<input type="text" name="A31" id="A31" class="form-control mt-2" value="{{ optional($result)->A31 }}" size="10"  readonly>
								</td>
								<th class="table-success align-middle" width="15%">部分負擔</th>
								<td width="18%">
									<input type="text" name="A32" id="A32" class="form-control mt-2" value="{{ optional($result)->A32 }}" size="10"  readonly>
								</td>
								<th class="table-success align-middle" width="15%">就醫序號</th>
								<td width="19%">
									<div class="d-flex align-items-center ms-2">
										<input type="text" name="A18" id="A18" class="form-control" value="{{ optional($result)->A18 }}"  style="max-width: 100px;margin-right:15px;">
										<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nhiErrorModal">
												開啟異常代碼表
										</button>
									</div>
									@if (request('nocard') == '1')
										<div class="d-flex align-items-center mt-2">
											<div class="form-check" style="margin-right:15px;">
												<input class="form-check-input radio" type="checkbox" name="owed" id="owed" {{ (optional($result)->owed=="1"?"checked":"") }}>
												<label class="form-check-label" for="owed">欠卡</label>
											</div>
											<button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#owedModal">
												欠卡說明
											</button>
										</div>
									@endif
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
				@elseif(request('action') == 'view')
					
				@else
					<button type="submit" class="btn btn-warning">儲存</button>
				@endif
			</div>
		</div>
    </form>
	</div>
</div>
<!-- 欠卡說明 Modal -->
<div class="modal fade" id="owedModal" tabindex="-1" aria-labelledby="owedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="owedModalLabel">欠卡說明</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">
					此功能提供當天訪視沒有個案健保卡，就醫序號就不需要輸入，當天亦不進行上傳。待當月下次訪視時，才使用健保卡進行補卡動作。
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="nhiErrorModal" tabindex="-1" aria-labelledby="nhiErrorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="nhiErrorModalLabel">健保IC卡異常代碼對照表</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered text-center align-middle">
            <thead class="table-primary">
              <tr>
                <th>尚未取得就醫序號</th>
                <th>已取得就醫序號</th>
                <th>異常原因</th>
              </tr>
            </thead>
            <tbody>
              @php
                $errors = [
                  ['A000', 'A001', '讀卡設備故障'],
                  ['A010', 'A011', '讀卡機故障'],
                  ['A020', 'A021', '網路故障造成讀卡機無法使用'],
                  ['A030', 'A031', '安全模組故障,造成讀卡機無法使用'],
                  ['B000', 'B001', '卡片不良(表面正常, 晶片異常)'],
                  ['C000', null, '停電'],
                  ['C001', null, '例外就醫者（首次加保 1 個月內、換 補發卡 14 日內）'],
                  ['C002', null, '20歲以下兒少例外就醫'],
                  ['C003', null, '讓懷孕婦女例外就醫'],
                  ['D000', 'D001', '醫療資訊系統(HIS)當機'],
                  ['D010', 'D011', '醫療院所電腦故障'],
                  ['E000', null, '健保總局資訊系統當機'],
                  ['E001', null, '控卡名單已簽切結書'],
                  ['F000', null, '醫事機構赴偏遠地區,因無電話撥接上網設備、居家照護'],
                  ['F00B', null, '居家輕量藍牙方案之離線認卡'],
                  ['Z000', 'Z001', '其他'],
                  ['G000', null, '新特約'],
                  ['H000', null, '高齡醫師'],
                  ['IC89', null, '無力繳納健保費'],
                  ['IC98', null, '未加保之移植捐贈者'],
                  ['IC09', null, '無健保愛滋病患就醫'],
                ];
              @endphp

              @foreach ($errors as [$code1, $code2, $reason])
              <tr>
                <td>
                  @if ($code1)
                    <a href="#" class="text-decoration-none text-primary" onclick="selectErrorCode('{{ $code1 }}')">{{ $code1 }}</a>
                  @endif
                </td>
                <td>
                  @if ($code2)
                    <a href="#" class="text-decoration-none text-primary" onclick="selectErrorCode('{{ $code2 }}')">{{ $code2 }}</a>
                  @endif
                </td>
                <td class="text-start">{{ $reason }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="major_illness_Modal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">個案重大傷病資料</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
				<div id="catasContent">載入中...</div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="history_Modal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">醫令歷史紀錄</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>日期區間：</label>
          <input type="text" id="startdate" class="form-control d-inline-block w-auto" autocomplete="off" size="12">
          ~
          <input type="text" id="enddate" class="form-control d-inline-block w-auto" autocomplete="off" size="12">
          <button id="searchHistoryBtn" class="btn btn-sm btn-secondary">搜尋</button>
        </div>
        <div id="searchblock">請輸入條件查詢</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="confirmHistorySelect">確定</button>
      </div>
    </div>
  </div>
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
		var type = {{ $case_type }};
		const staffMap = @json($staffMap);  // @json等同於 json_encode()
		var A17 = {{ $result->A17 }};
		var D52 = {{ $result->D52 }};

		$("#startdate").datetimepicker({ 
			format: 'Y-m-d',
			timepicker:false,
			mask: false
		});
		$("#enddate").datetimepicker({ 
			format: 'Y-m-d',
			timepicker:false,
			mask: false
		});

		$(".major-illness-btn").click(function() {
			const caseID = $(this).data('id');
			$.ajax({
        url: '/get_consultation_major/' + caseID,
        method: 'GET',
        dataType: 'json',
        success: function (data) {
					if (data.length === 0) {
						$('#catasContent').html('<div class="alert alert-info">重大傷病紀錄：無</div>');
					} else {
						let table = `
							<table class="table table-bordered text-center">
								<thead class="table-light">
									<tr>
										<th>紀錄</th>
										<th>重大傷病代碼</th>
										<th>有效起日</th>
										<th>有效迄日</th>
									</tr>
								</thead>
								<tbody>`;
								data.forEach((item, index) => {
									table += `
									<tr>
										<td>第 ${index + 1} 組</td>
										<td>${item.code}</td>
										<td>${item.start}</td>
										<td>${item.end}</td>
									</tr>`;
								});
						table += '</tbody></table>';
						$('#catasContent').html(table);
					}
        },
        error: function () {
					$('#catasContent').html('<div class="alert alert-danger">讀取失敗</div>');
        }
    	});
		});

		$('.btn-history-order').on('click', function () {
        caseID = $(this).data('id');
        $('#searchblock').html('請輸入條件查詢');
        $('#startdate').val('');
        $('#enddate').val('');
        $('#history_Modal').modal('show');
    });

		$('#searchHistoryBtn').on('click', function () {
			const start = $('#startdate').val();
			const end = $('#enddate').val();

			if (!start || !end) {
				alert('請選擇起訖日期');
				return;
			}
			$('#searchblock').html('查詢中...');
			$.ajax({
				url: '/get_history_orders',
				method: 'GET',
				data: { caseID: caseID, startdate: start, enddate: end },
				success: function (data) {
					if (data.length === 0) {
						$('#searchblock').html('<div class="alert alert-warning">無歷史紀錄</div>');
					} else {
						let html = '<table class="table table-bordered"><thead><tr style="background-color:#F0F0F0;"><th>選擇</th><th>日期</th><th>就診類別</th><th>醫令內容</th></tr></thead><tbody>';
						data.forEach(item => {
							html += `
								<tr>
										<td align="center"><input type="radio" class="radio" name="selectRID" value="${item.REGID}"></td>
										<td align="center">${item.A17}</td>
										<td align="center">${item.A23}</td>
										<td>${item.summary}</td>
								</tr>`;
						});
						html += '</tbody></table>';
						$('#searchblock').html(html);
					}
				},
				error: function () {
					$('#searchblock').html('<div class="alert alert-danger">查詢失敗</div>');
				}
			});
    });

		// 選取醫令
    $(document).on('change', 'input[name="selectRID"]', function () {
        selectedRID = $(this).val();
    });

    $('#confirmHistorySelect').on('click', function () {
			if (!selectedRID) {
				alert('請先選擇一筆紀錄');
				return;
			}

			$.post('/select-history-order', {
				rID: selectedRID,
				_token: '{{ csrf_token() }}'
			}, function (data) {
				$('#LabSpan2').html(data.html);
				$('#fileCount').val(data.fileCount);
				$('#history_Modal').modal('hide');
			});
    });

		$("#A17").datetimepicker({ 
			format: 'Y-m-d H:i:s',
			mask: false
		});

		$("#D15").select2();

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

	function selectErrorCode(code) {
		$('#A18').val(code);
		$('#nhiErrorModal').modal('hide');
	}

	function amountValue()
	{
		var nightplus = $('input[name*=night_plus]:checked').val();
    var double_flag = (nightplus === "1") ? 1 : 0;

    var numdouble = ['05301C', '05342C', '05302C','05343C', '05328C', '05344C','05329C','05345C', '05303C', '05346C', '05304C', '05347C', '05330C', '05348C', '05331C', '05349C', '05305C', '05350C', '05306C', '05351C', '05332C', '05352C', '05333C', '05353C', '05321C', '05354C', '05322C', '05355C', '05334C', '05356C', '05335C', '05357C','05307C','05358C', '05308C', '05359C', '05309C', '05360C', '05310C', '05361C','05312C','05362C', '05323C', '05363C','05336C', '05364C', '05337C', '05365C', '05313C', '05366C', '05324C', '05367C', '05338C', '05368C', '05339C', '05369C', '05314C', '05370C', '05325C', '05371C', '05340C', '05372C', '05341C', '05373C', '05315C', '05374C', '05316C'];   //需加成醫令
		var pre_partfee = ['P8416C', 'P8417C', 'P8418C','P8419C', 'P8420C'];   //HAH要計算部分負擔的醫令

    var amt = 0, amt_part = 0;

    $('input[id^=nhiPRICE_]').each(function () {
        var id = this.id.split('_')[1];
        var code = $('#nhiA73_' + id).val();
        var p3 = $('#nhiA73_' + id + 'p3').val();
        var price = parseFloat($('#nhiPRICE_' + id).val()) || 0;
        var pointdouble = 1;

        if (double_flag && numdouble.includes(code)) {
					pointdouble = checkpointdouble($('#nhiA71_1_' + id).val(), $('#nhiA71_2_' + id).val(), A17);
        }

        if (type == 2 && (p3 == "2" || p3 == "4")) {  //IDS
            amt += Math.round(price * pointdouble);
            amt_part += Math.round(price * pointdouble);
            $('#nhiA75_' + id).val(pointdouble.toFixed(2));
        } else if (type == 2 && p3 == "3") {
            amt += Math.round(price * 1.05);
            amt_part += Math.round(price * 1.05);
            $('#nhiA75_' + id).val(1.05);
        } else if (type == 7 && p3 == "2" && pre_partfee.includes(code)) {
            var res = checkpointdoubleHAH($('#nhiA71_1_' + id).val(), $('#nhiA71_2_' + id).val(), A17, price).split('@@');
            amt_part = Number(res[0]);
            pointdouble = Number(res[1]);
            amt += amt_part;
            $('#nhiA75_' + id).val(pointdouble.toFixed(2));
        } else if (p3 == "2") {
            amt += Math.round(price * pointdouble);
            amt_part += Math.round(price * pointdouble);
            $('#nhiA75_' + id).val(pointdouble.toFixed(2));
        } else if (p3 == "3") {
            amt += Math.round(price * 1.05);
            amt_part += Math.round(price * 1.05);
            $('#nhiA75_' + id).val(1.05);
        }
    });

    $('#nhiA31').val(amt);

    if ($('#D15').val() === 'K00') {
        var rate = (D52 === '02') ? 0.04 : 0.05;
        $('#nhiA32').val(Math.round(amt_part * rate));
    } else {
        $('#nhiA32').val(0);
    }
	}

	function applyPREData(data, inputId) {
		const id = inputId;
		const id_index = id.split('_')[1]; // 例如 nhiA73_1 → 1
		const prefix = id.startsWith('old') ? 'old' : '';

		// 將代碼、名稱、點數、p3 類別填入相關欄位
		$(`#${id}`).val(data.code);
		$(`#nhiName_${id_index}`).val(data.name);
		$(`#nhiA9_${id_index}`).val(data.point);
		$(`#nhip3_${id_index}`).val(data.category);
		var set_group = data.group;
    let price = Number(data.point);
		//0:short_code, 1:treatment_code, 2:treatment_name_zh, 3:points, 4: category, 5: id, 6:group
    if (type === 7) { //HAH個案
			if (data.category !== '2') {
				$('#' + prefix + 'nhiPRICE_' + id_index).val(0);
				$('#' + prefix + 'nhipoint_' + id_index).val(0);
			} else {
				$('#' + prefix + 'nhiPRICE_' + id_index).val(price);
				$('#' + prefix + 'point_' + id_index).val(price);
			}
    } else {
			$('#' + prefix + 'nhiPRICE_' + id_index).val(price);
			$('#' + prefix + 'nhipoint_' + id_index).val(price);
    }

    // 人員群組選單重建
    const selectId = prefix + 'nhip16_' + id_index;
    let option = `<select id="${selectId}" name="${selectId}" class="validate[required]" style="width:90px">`;
    option += `<option>::::::</option>`;
    
    const groupList = set_group.split(';');
    groupList.forEach(group => {
			const members = staffMap[group] || [];
			members.forEach(member => {
				option += `<option value="${member.id}">${member.name}</option>`;
			});
    });
    option += `</select>`;

    $('#' + selectId).select2('destroy');
    $('#' + selectId).replaceWith(option);
    $('#' + selectId).select2();

    // 自動填月份日期範圍
    const currentDate = new Date();
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth() + 1;
    const days = new Date(year, month, 0).getDate();
    let A76 = 1;
		
    if (["54007C1", "P1015C", "P5406C", "05402C"].includes($('#' + id).val())) {
			A76 = days;
			const monthStr = month < 10 ? '0' + month : month;
			const startDate = `${year}-${monthStr}-01 00:00`;
			const endDate = `${year}-${monthStr}-${days} 00:00`;

			$('#' + prefix + 'nhiA71_1_${id_index}').val(startDate);
			$('#' + prefix + 'nhiA71_2_${id_index}').val(endDate);
			// $(`#oldA71_1_${id_index}`).val(startDate);
			// $(`#oldA71_2_${id_index}`).val(endDate);
    }

    $('#' + prefix + 'nhiA76_' + id_index).val(A76);

    if (type === 7 && data.category !== '2') {
			price = 0;
    }

    $('#nhiPRICE_' + id_index).val(price * A76);

    // 綁定 A76 異動就重新計算金額
    $('input[id^=nhiA76_]').off('change').on('change', function () { //先移除再加（避免重複）
			const newDay = $(this).val();
			$('#nhiPRICE_' + id_index).val(price * newDay);
			amountValue();
    });

    amountValue();
    datefresh();
	}

	function datefresh() {
    $('input[id^=nhiA71_1_], input[id^=oldnhiA71_1_]').on('change keyup', function () {
			var id = this.id.split('_')[2];
			var prefix = this.id.includes('old') ? 'old' : '';
			var start = new Date($(this).val());

			if (!isNaN(start.getTime())) {
					var end = new Date(start.getTime() + 3600000); // +1 小時
					var endStr = end.toISOString().slice(0, 16).replace('T', ' ');
					$('#' + prefix + 'nhiA71_2_' + id).val(endStr);
			}

			amountValue();
    });

    $('input[id^=nhiA71_2_], input[id^=oldnhiA71_2_]').on('change keyup', function () {
			amountValue();
    });
	}

	function checkpointdouble(start, end, today) {
		var pointdouble = 1;
		if(start=="____-__-__ __:__" || start=="0000-00-00 00:00"){
				start = today.substr(0,10)+' 00:00';
		}
		if(end=="____-__-__ __:__" || end=="0000-00-00 00:00"){
				end = today.substr(0,10)+' 00:00';
		}

		var starttime_str = start;
		var endtime_str = end;
		var startday = new Date(starttime_str.substr(0, 10));
		var endday = new Date(endtime_str.substr(0, 10));
		var starttime = new Date(starttime_str.substr(0));
		var endtime = new Date(endtime_str.substr(0));
		var flag = 0;
		var work_flag=0;
		var natural_disaster_flag = 0;
		var vocation=0;
		var holiday = '<?php
			$holidaylist = "";
			$db6 = new DB;
			$db6->query("SELECT * FROM `management103` WHERE 1");
			if ($db6->num_rows() > 0) {
				for ($ii = 0; $ii < $db6->num_rows(); $ii++) {
					$r6 = $db6->fetch_assoc();
					$holidaylist .= $r6['date'] . "|";
				}
			}
			echo $holidaylist;
		?>';

		var holidaylist = holiday.split('|');
		for (var i = 0; i < holidaylist.length; i++) {
			if (starttime_str.substr(0, 10) == holidaylist[i] || endtime_str.substr(0, 10) == holidaylist[i]) {
				flag = 1;
			}
		}

		var workday = "2019-10-05|2020-02-15|2020-06-20|2020-09-26|2021-02-20|2021-09-11|2022-01-22|2023-01-07|2023-02-04|2023-02-18|2023-03-25|2023-06-17|2023-09-23|2024-02-17|2025-02-08|";   //補班日
		var workdaylist = workday.split('|');
		for (var i = 0; i < workdaylist.length; i++) {
			if (starttime_str.substr(0, 10) == workdaylist[i] || endtime_str.substr(0, 10) == workdaylist[i]) {
				work_flag = 1;
			}
		}
		var natural_disaster_day = "|";   //天然災害停止上班
		var natural_disaster_daylist = natural_disaster_day.split('|');
		for (var i = 0; i < natural_disaster_daylist.length; i++) {
			if (starttime_str.substr(0, 10) == natural_disaster_daylist[i] || endtime_str.substr(0, 10) == natural_disaster_daylist[i]) {
				natural_disaster_flag = 1;
			}
		}
		if(type==7){
			//為假日
			if ((endday.getDay() == 6 && work_flag!=1 && endtime < new Date(endtime_str.substr(0, 10) + ' 00:30')) || endday.getDay() == 0 || (startday.getDay() == 6 && work_flag!=1) || (startday.getDay() == 0 && starttime < new Date(starttime_str.substr(0, 10) + ' 23:30'))) {
				pointdouble = 1.2;
			}
			if(natural_disaster_flag == 1 || flag == 1){
				pointdouble = 1.5;
			}
		}else{
			//夜間,深夜,假日加成點數
			if (endtime < starttime) {
					pointdouble = 1;
			}
			else {
				if(starttime_str.substr(11,5)=="00:00" && endtime_str.substr(11,5)=="00:00"){
						if ((endday.getDay() == 6 && work_flag!=1 && endtime < new Date(endtime_str.substr(0, 10) + ' 00:30')) || endday.getDay() == 0 || (startday.getDay() == 6 && work_flag!=1) || (startday.getDay() == 0 && starttime < new Date(starttime_str.substr(0, 10) + ' 23:30')) || flag == 1) {
								vocation = 1;
								pointdouble = 1.4;
						}
				}else {
					if (startday.getDay() != endday.getDay()) {
							pointdouble = 1.7;
					} else {
						//為假日
						if ((endday.getDay() == 6 && work_flag!=1 && endtime < new Date(endtime_str.substr(0, 10) + ' 00:30')) || endday.getDay() == 0 || (startday.getDay() == 6 && work_flag!=1) || (startday.getDay() == 0 && starttime < new Date(starttime_str.substr(0, 10) + ' 23:30')) || flag == 1) {
								vocation = 1;
								//普通時間
								if (starttime > new Date(starttime_str.substr(0, 10) + ' 07:30') && endtime > new Date(endtime_str.substr(0, 10) + ' 07:30') && endtime <= new Date(endtime_str.substr(0, 10) + ' 17:30')) {
										pointdouble = 1.4;
								} //夜間
								else if (endtime > new Date(endtime_str.substr(0, 10) + ' 17:30') && endtime <= new Date(endtime_str.substr(0, 10) + ' 22:30') && starttime > new Date(starttime_str.substr(0, 10) + ' 07:30')) {
										pointdouble = 1.5;
								} //深夜
								else {
										pointdouble = 1.7;
								}
						} //非假日
						else {
							//普通時間
							if (starttime > new Date(starttime_str.substr(0, 10) + ' 07:30') && endtime > new Date(endtime_str.substr(0, 10) + ' 07:30') && endtime <= new Date(endtime_str.substr(0, 10) + ' 17:30')) {
									pointdouble = 1;
							} //夜間
							else if (endtime > new Date(endtime_str.substr(0, 10) + ' 17:30') && endtime <= new Date(endtime_str.substr(0, 10) + ' 22:30') && starttime > new Date(starttime_str.substr(0, 10) + ' 07:30')) {
									pointdouble = 1.5;
							} //深夜
							else {
									pointdouble = 1.7;
							}
						}
					}
				}
			}

			if(start==end && vocation==0){
					pointdouble = 1;
			}
		}
		return pointdouble;
	}

	function checkpointdoubleHAH(start, end, today, this_price) {
		var total_price = 0;
		var pointdouble = 1;
		var first_pointdouble = 1;
		if(start=="____-__-__ __:__" || start=="0000-00-00 00:00"){
				start = today.substr(0,10)+' 00:00';
		}
		if(end=="____-__-__ __:__" || end=="0000-00-00 00:00"){
				end = today.substr(0,10)+' 00:00';
		}

		var starttime_str = start;
		var endtime_str = end;
		var startday = new Date(starttime_str.substr(0, 10));
		var endday = new Date(endtime_str.substr(0, 10));
		var starttime = new Date(starttime_str.substr(0));
		var endtime = new Date(endtime_str.substr(0));
		var flag = 0;
		var work_flag=0;
		var natural_disaster_flag = 0;
		var vocation=0;
		var holiday = '<?php
			$holidaylist = "";
			$db6 = new DB;
			$db6->query("SELECT * FROM `management103` WHERE 1");
			if ($db6->num_rows() > 0) {
				for ($ii = 0; $ii < $db6->num_rows(); $ii++) {
					$r6 = $db6->fetch_assoc();
					$holidaylist .= $r6['date'] . "|";
				}
			}
			echo $holidaylist;
		?>';

		var holidaylist = holiday.split('|');
		var workday = "2019-10-05|2020-02-15|2020-06-20|2020-09-26|2021-02-20|2021-09-11|2022-01-22|2023-01-07|2023-02-04|2023-02-18|2023-03-25|2023-06-17|2023-09-23|2024-02-17|2025-02-08|";   //補班日
		var workdaylist = workday.split('|');
		var natural_disaster_day = "|";   //天然災害停止上班
		var natural_disaster_daylist = natural_disaster_day.split('|');

		for (let d = new Date(startday); d <= endday; d.setDate(d.getDate() + 1))
		{
			let currentDate = new Date(d); // 重要：複製 d，避免影響原始值
			let formattedDate = currentDate.toISOString().split("T")[0]; // 轉成 YYYY-MM-DD
			
			for (var i = 0; i < holidaylist.length; i++) {
				if (formattedDate.substr(0, 10) == holidaylist[i]) {
					flag = 1;
				}
			}
			for (var i = 0; i < workdaylist.length; i++) {
				if (formattedDate.substr(0, 10) == workdaylist[i]) {
						work_flag = 1;
				}
			}
			for (var i = 0; i < natural_disaster_daylist.length; i++) {
				if (formattedDate.substr(0, 10) == natural_disaster_daylist[i]) {
					natural_disaster_flag = 1;
				}
			}
			
			//為假日
			if (work_flag!=1 && (currentDate.getDay() == 6 || currentDate.getDay() == 0 || flag==1)) {
					pointdouble = 1.2;
			}
			if(natural_disaster_flag == 1){
				pointdouble = 1.5;
			}
			if(formattedDate.substr(0, 10) == starttime_str.substr(0, 10)){
				first_pointdouble = pointdouble;  
			}
			
			total_price +=  Math.round(this_price*pointdouble);
			// alert(formattedDate+"@@"+this_price*pointdouble+"@@"+pointdouble+"@@"+total_price);
		}
		return total_price+'@@'+first_pointdouble;
	}

</script>
<br>
@endsection
