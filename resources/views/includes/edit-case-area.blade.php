<div class="modal fade" id="editareaModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">編輯個案資料 <span id="editareaCaseInfo"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editareaForm">
          {{--  在 POST 請求中自動附加 CSRF Token，並在伺服器端驗證，以防止攻擊者偽造請求 --}}
          @csrf
          @method('PUT')
          <input type="hidden" id="editareaCaseId">
          <input type="hidden" id="editarea_original">
          <input type="hidden" id="editareaBed">
          <div class="mb-3">
            <label class="form-label">區域</label>
            <select class="form-control" id="editarea">
              @foreach ($areaNames as $key=>$area)
                <option value="{{ $key }}">
                    {{ $area }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="d-flex gap-2 mt-5">
            <button type="submit" class="btn btn-success w-50">儲存修改</button>
            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">取消</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>