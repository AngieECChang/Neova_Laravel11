<!-- 編輯 Modal -->
<div class="modal fade" id="closecaseModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">個案資料 <span id="closeCaseInfo"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="closeForm">
          {{--  在 POST 請求中自動附加 CSRF Token，並在伺服器端驗證，以防止攻擊者偽造請求 --}}
          @csrf
          <input type="hidden" id="closeCaseId">
          <input type="hidden" id="opendate">
          <input type="hidden" id="caseType">
          <input type="hidden" id="caseArea">
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="closeDate" class="form-label m-0">結案日期</label>
            </div>
            <div class="col-9">
              <input type="text" id="closeDate" class="form-control" placeholder="請輸入西元年" value="{{ date('Y-m-d')}}" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="closeReason" class="form-label m-0">結案原因</label>
            </div>
            <div class="col-4 pe-0">
              <select class="form-select" id="closeReason" style="width:140px" required>
                @foreach ($close_reason as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-5 ps-1">
              <input type="text" id="closeReasonOther" class="form-control" placeholder="請輸入其他原因" style="display: none;">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="closefiller" class="form-label m-0">結案人員</label>
            </div>
            <div class="col-9">
              <input type="text" id="closefiller" class="form-control" value="{{session('user_id')}}">
            </div>
          </div>
          <button type="submit" class="btn btn-success w-100">確認結案</button>
        </form>
      </div>
    </div>
  </div>
</div>