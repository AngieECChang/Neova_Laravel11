<div class="modal fade" id="reopenModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">個案資料 <span id="reopenInfo"></span></h5>
        <button type="button" class="btn-reopen" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="reopenForm">
          {{--  在 POST 請求中自動附加 CSRF Token，並在伺服器端驗證，以防止攻擊者偽造請求 --}}
          @csrf
          <input type="hidden" id="reopenCaseId">
          <input type="hidden" id="opendate">
          <input type="hidden" id="closedate">
          <input type="hidden" id="caseType">
          <input type="hidden" id="closeArea">
          <input type="hidden" id="closeBed">
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="reopenDate" class="form-label m-0">重收日期</label>
            </div>
            <div class="col-9">
              <input type="text" id="reopenDate" class="form-control" placeholder="請輸入西元年" value="{{ date('Y-m-d')}}" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="reopenArea" class="form-label m-0">重收區域</label>
            </div>
            <div class="col-9">
              <select class="form-select" id="reopenArea" style="width:140px" required>
                @foreach ($area_arrayinfo as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="reopenType" class="form-label m-0">重收類型</label>
            </div>
            <div class="col-9">
              <select class="form-select" id="reopenType" style="width:140px" required>
                @foreach ($patient_type as $type_key => $type_value)
                  <option value="{{ $type_key }}">{{ $type_value }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="d-flex gap-2 mt-5">
            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-success w-50">儲存修改</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>