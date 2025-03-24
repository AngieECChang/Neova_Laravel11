<!-- 新增個案 Modal -->
<div class="modal fade" id="newcaseModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">新增個案資料</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="newcaseForm">
          {{-- CSRF Token 防止跨站請求攻擊 --}}
          @csrf
          @method('POST')

          <!-- 個案姓名 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseName" class="form-label m-0">個案姓名</label>
            </div>
            <div class="col-9">
              <input type="text" class="form-control" id="newCaseName" placeholder="請輸入個案姓名" required>
            </div>
          </div>
          <!-- 身分證字號 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseID" class="form-label m-0">身分證字號</label>
            </div>
            <div class="col-9">
              <input type="text" class="form-control" id="newCaseID" placeholder="請輸入身分證字號" required>
            </div>
          </div>
          <!-- 生日 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="roc_date" class="form-label m-0">生日</label>
            </div>
            <div class="col-9">
              <input type="text" id="roc_date" name="roc_date" class="form-control" placeholder="請輸入民國年 (112/03/18)">
              <span id="roc_date_error" style="color: red; display: none;">日期格式錯誤，請輸入 民國年/月/日 (112/03/18)</span>
            </div>
          </div>
          <!-- 個案類型 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseType" class="form-label m-0">個案類型</label>
            </div>
            <div class="col-9">
              <select class="form-select" id="newCaseType" required>
                @foreach ($patient_type as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- 案號 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseNo" class="form-label m-0">案號</label>
            </div>
            <div class="col-9">
              <input type="text" class="form-control" id="newCaseNo" placeholder="請輸入案號">
            </div>
          </div>

          <!-- 提交按鈕 -->
          <button type="submit" class="btn btn-success w-100">新增個案</button>
        </form>
      </div>
    </div>
  </div>
</div>