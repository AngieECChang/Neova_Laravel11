<div class="modal fade" id="delete-closeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">個案資料 <span id="deleteInfo"></span></h5>
        <button type="button" class="btn-delete" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="deleteForm">
        <input type="hidden" id="deleteCaseId">
          {{--  在 POST 請求中自動附加 CSRF Token，並在伺服器端驗證，以防止攻擊者偽造請求 --}}
          @csrf
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="deleteopendate" class="form-label m-0">收案日期</label>
            </div>
            <div class="col-9">
              <input type="text" id="deleteopendate" class="form-control" readonly>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="deleteclosedate" class="form-label m-0">結案日期</label>
            </div>
            <div class="col-9">
              <input type="text" id="deleteclosedate" class="form-control" readonly>
            </div>
          </div>
          <div class="d-flex gap-2 mt-5">
            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-success w-50">確認刪除結案紀錄</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>