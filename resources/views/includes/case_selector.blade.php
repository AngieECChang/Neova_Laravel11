<div class="sticky-top shadow-sm p-3 rounded" style="background-color:#FFDCB9	;" id="case_selector">
  <div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
      <div class="col-1">
        <label class="form-label me-2" style="width:250px;color:	#3C3C3C !important;">選擇個案</label>
      </div>
      <div class="col-4">
        <select class="form-control w-30 me-4" id="selectCase">
            <option value="">請選擇</option>
            @foreach ($open_cases as $case)
                <option value="{{ $case->caseID }}" {{ ($the_case->caseID ?? '') == $case->caseID ? 'selected' : '' }}>
                    {{ $case->name." 【".(string)$case->caseNoDisplay."】" }}
                </option>
            @endforeach
        </select>
      </div>
      <div class="col-1">
        <label class="form-label ms-3" style="width:250px;color:	#3C3C3C !important;">選擇日期</label>
      </div>
      <div class="col-4">
        <select class="form-control w-20" id="selectDate" disabled>
            <option value="">請先選擇個案</option>
        </select>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="formID" value="{{ $formID ?? '' }}">
<input type="hidden" id="print_url">
<script>
  $('#selectCase').select2({
    placeholder: "請選擇或輸入個案名稱",
    width: '100%' // 讓選單寬度適應
  });

  document.addEventListener("DOMContentLoaded", function () {
    let selectCase = document.getElementById("selectCase");
    let selectDate = document.getElementById("selectDate");
    let formID = document.getElementById("formID")?.value || '';  // 取得 formID
    
    // 當選擇個案時，載入該個案的所有評估日期
    $('#selectCase').on('change', function () {
      let caseID = this.value;
      selectDate.innerHTML = '<option value="">請先選擇個案</option>';
      selectDate.disabled = true;

      if (!caseID || !formID) return;

      fetch(`/get-evaluation-dates/${formID}/${caseID}`)
        .then(response => response.json())
        .then(data => {
          if (data.no_records) {
            // **沒有紀錄時，直接跳轉**
            window.location.href = `/hcevaluation/${formID}/${caseID}`;
            return;
          }
          selectDate.innerHTML = '<option value="">請選擇日期</option>';
          data.forEach(date => {
              let option = document.createElement("option");
              option.value = date;
              option.textContent = date;
              selectDate.appendChild(option);
          });
          selectDate.disabled = false;
        })
        .catch(error => console.error('Error:', error));
    });

    // 當選擇日期時，根據 formid、caseID 和 date 轉跳到相應表單
    selectDate.addEventListener("change", function () {
      let caseID = selectCase.value;
      let date = this.value;

      if (!caseID || !date || !formID) return;
      // 這裡假設所有表單的 URL 格式為 `/hcevaluation/{formid}/{caseID}/{date}`
      window.location.href = `/hcevaluation/${formID}/${caseID}/${date}`;
    });
  });
</script>