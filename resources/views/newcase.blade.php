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
          <!-- 個案姓名 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseName" class="form-label m-0">個案姓名</label>
            </div>
            <div class="col-9">
              <input type="text" class="form-control" id="newCaseName" placeholder="請輸入個案姓名" required>
            </div>
          </div>
          <!-- 性別 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseGender" class="form-label m-0">性別</label>
            </div>
            <div class="col-9">
              <select class="form-select" id="newCaseGender" required>
                <option value="1">男</option>
                <option value="0">女</option>
                <option value="2">其他</option>
              </select>
            </div>
          </div>
          <!-- 身分證字號 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseID" class="form-label m-0">身分證字號</label>
            </div>
            <div class="col-9">
              <input type="text" class="control" id="newCaseID" placeholder="請輸入身分證字號" required>
            </div>
          </div>
          <!-- 生日 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseBD" class="form-label m-0">生日</label>
            </div>  
            <div class="col-9">
              <input type="text" id="newCaseBD" class="form-control" placeholder="請輸入民國年 (如50/03/18)" readonly>
              <span id="newCaseBD_error" style="color: red; display: none;">日期格式錯誤，請輸入 民國年/月/日 (50/03/18)</span>
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
          <!-- 收案日期 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseDate" class="form-label m-0">收案日期</label>
            </div>
            <div class="col-9">
              <input type="text" id="newCaseDate" name="newCaseDate" class="form-control" placeholder="請輸入西元年" value="{{ date('Y-m-d')}}" readonly>
            </div>
          </div>
          <!-- 案號 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseNo" class="form-label m-0">案號</label>
            </div>
            <div class="col-9">
              <input type="text" class="form-control" id="newCaseNo" placeholder="請輸入案號" required>
            </div>
          </div>
          <!-- 區域 -->
          <div class="row mb-3">
            <div class="col-3 bg-primary text-white d-flex align-items-center justify-content-center rounded-start">
              <label for="newCaseArea" class="form-label m-0">區域</label>
            </div>
            <div class="col-9">
              <select class="form-select" id="newCaseArea" required>
                @foreach ($areaNames as $key=>$area)
                  <option value="{{ $key }}">
                      {{ $area }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <!-- 提交按鈕 -->
          <button type="submit" class="btn btn-success w-100">新增個案</button>
        </form>
      </div>
    </div>
  </div>
</div>