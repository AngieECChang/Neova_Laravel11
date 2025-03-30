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
<input type="hidden" id="selectedDate" value="{{ optional($result)->date ?? '' }}">
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
    // let selectedDateVal = document.getElementById('selectedDate')?.value || '';

    // 頁面載入時如果已選個案 → 也載入日期清單
    if (selectCase.value && formID) {
      loadEvaluationDates(formID, selectCase.value, true); // ✅ 再次呼叫
    }
    // 當選擇個案時，載入該個案的所有評估日期
    $('#selectCase').on('change', function () {
      let caseID = this.value;
      localStorage.setItem('remember_case', caseID);
      loadEvaluationDates(formID, caseID, false);
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

  function loadEvaluationDates(formID, caseID, isInit = false) {
    let selectedDate = document.getElementById("selectedDate").value;

    fetch(`/get-evaluation-dates/${formID}/${caseID}`)
    .then(response => {
      if (!response.ok) throw new Error("Fetch failed");
      return response.json();
    })
    .then(data => {
      if (data.no_records) {
        selectDate.innerHTML = '<option value="">沒有紀錄</option>';
        selectDate.disabled = true;
        if (!isInit) {
          window.location.href = `/hcevaluation/${formID}/${caseID}`;
        }
        return;
      }

      selectDate.innerHTML = '<option value="">請選擇日期</option>';
      data.forEach(date => {
        let option = document.createElement("option");
        option.value = date;
        option.textContent = date;
        if (date.trim() === selectedDate.trim()) {
          option.selected = true;
        }
        selectDate.appendChild(option);
      });

      selectDate.disabled = false;
    })
    .catch(error => console.error('Error:', error));
  }

</script>