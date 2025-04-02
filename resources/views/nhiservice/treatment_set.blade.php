@extends('layouts.app')
@section('content')
<style>
  .custom-table tbody tr:hover {
    background-color: #d1ecf1 !important;  /* 淡藍色 */
    color: #750000 !important;
    font-weight: bold !important;
  }
  .custom-table {
    color: #272727 !important;
  }
  .font-control:focus{
    color: #3C3C3C	!important;
  }
</style>
@php
  $treatment_category = config('public.hc_treatment_category');
  $treatment_tab = array('0'=>'2','1'=>'3','2'=>'4','3'=>'99');
  $treatment_tab2 = array('2'=>'0','3'=>'1','4'=>'2','99'=>'3');
@endphp
<div class="row align-items-center mb-4">
  <div class="col-6 col-md-3">
    <h1 class="h3 text-gray-800 mb-0">處置代碼套組</h1>
  </div>
  <div class="col-6 col-md-9 d-flex justify-content-end">
    <input type="text" class="form-control tableSearch" placeholder="🔍 搜尋..." style="width: 200px;">
    <form>
      <div style="padding-left:10px">
        <a href="{{ route('nhiservice.treatment_set_edit') }}" class="btn text-white" style="background-color: orange;">新增處置代碼套組</a>
      </div>
    </form>
  </div>
</div>
<div class="tab-content mt-3">
  <div class="tab-pane fade show active" id="content-all" role="tabpanel">
    @if ($treatment_set_list->isEmpty()) 
      <div class="alert alert-warning text-center mt-3">
        🚨 目前沒有任何處置套組
      </div>
    @else
      <div class="card shadow-sm mb-4">
        <div class="card-body">
          <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
          <table class="table table-striped table-hover custom-table searchable-table">
            <thead class="sticky-top table-dark">
              <tr>
                <th width="300x" class="text-center">套組描述</th>
                <th width="350px" class="text-center">套組內容</th>
                <th width="200px" class="text-center">功能</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($treatment_set_list as $treatment_key => $treatment_value)
              <tr>
                <td>{{ $treatment_value[0]->description }}</td>
                <td>
                  @php
                    $index = 1;
                    $content = "";
                  @endphp
                  @foreach ($treatment_value as $treatment_set)  
                    @php
                      $content .=($content!=""?"\n":"") .$index.". 【".$treatment_set->treatment_code."】".$treatment_set->treatment_name_zh;
                      $index++;
                    @endphp
                  @endforeach
                  <pre style="font-size:1rem !important; font-weight:400 !important; line-height:1.5 !important;">{{ $content }}</pre>
                </td>
                <td>
                  <a href="{{ route('nhiservice.treatment_set_edit', ['set_id' => $treatment_value[0]->set_id]) }}" class="btn btn-sm btn-success" style="font-size: 1rem !important;margin-right:40px !important;">
                    <i class="bi bi-pencil-square"></i>&nbsp;編輯
                  </a>
                  <button class="btn btn-sm close-btn" style="background-color:#e83e8c;color: #ffffff;font-size: 1rem !important;" data-setid="{{ $treatment_value[0]->set_id }}" data-name="{{ $treatment_value[0]->description }}" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i>&nbsp;刪除
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        </div>
      </div>
    @endif
  </div>
</div>
<!-- 刪除處置 -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">處置套組 </h5>
        <button type="button" class="btn-delete" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="delete-treatment-form">
          {{-- CSRF Token 防止跨站請求攻擊 --}}
          @csrf
          <input type="hidden" id="delete-id">
          <span id="treatmentInfo"></span>
          <div class="d-flex gap-2 mt-5">
            <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-danger w-50">確認刪除</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
   document.querySelectorAll('.tableSearch').forEach(function(input) {
    input.addEventListener('keyup', function() {
      let filter = this.value.toLowerCase();
      let tables = document.querySelectorAll(".searchable-table");

      tables.forEach(table => {
        let rows = table.querySelectorAll("tbody tr");
        rows.forEach(row => {
          let text = row.innerText.toLowerCase();
          row.style.display = text.includes(filter) ? "" : "none";
        });
      });
    });
  });
  $(document).ready(function() {
    $("#newstart_date").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true,
      defaultDate: new Date(),
      monthNames: ["一月", "二月", "三月", "四月", "五月", "六月",
                  "七月", "八月", "九月", "十月", "十一月", "十二月"],
      monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月",
                        "7月", "8月", "9月", "10月", "11月", "12月"],
      dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
    });
    $("#newend_date").datepicker({
      dateFormat: "yy-mm-dd",
      changeMonth: true,
      changeYear: true,
      defaultDate: new Date(),
      monthNames: ["一月", "二月", "三月", "四月", "五月", "六月",
                  "七月", "八月", "九月", "十月", "十一月", "十二月"],
      monthNamesShort: ["1月", "2月", "3月", "4月", "5月", "6月",
                        "7月", "8月", "9月", "10月", "11月", "12月"],
      dayNamesMin: ["日", "一", "二", "三", "四", "五", "六"],
    });

    const fieldNames = {
      'short_code': '簡易代碼',
      'treatment_code': '健保代碼',
      'treatment_name_zh': '處置中文名稱',
      'start_date': '生效起日',
      'end_date': '生效迄日',
      'points': '價格/點數',
      'category': '類別',
      'group': '組別'
    };

    $("#newtreatmentForm").submit(function(e) {
      e.preventDefault(); // 阻止表單送出
      let formData = {
        _token: $("input[name=_token]").val(),
        short_code: $("#newshort_code").val(),
        treatment_code: $("#newtreatment_code").val(),
        treatment_name_zh: $("#newtreatment_name_zh").val(),
        treatment_name_en: $("#newtreatment_name_en").val(),
        model_no: $("#newmodel_no").val(),
        unit: $("#newunit").val(),
        points : $("#newpoints ").val(),
        start_date: $("#newstart_date").val(),
        end_date: $("#newend_date").val(),
        comments: $("#newcomments").val(),
        category: $("input[name='category']:checked").val(),
        group: $("input[name='group[]']:checked").map(function () {
          return $(this).val();
        }).get()
      };
      $('#formErrors').addClass('d-none').empty();
      $.ajax({
        url: "/newtreatment",
        type: "POST",
        data: formData,
        dataType: "json",
        success: function(response) {
          console.log("檢查結果：", response);
          if (response.success) {
            alert("新增成功！");
            location.reload(); // 重新整理頁面
          } else {
            alert("錯誤：" + response.messages);
          }
        },
        error: function(xhr) {
          if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors || {};
            // 顯示錯誤
            let allMessages = '';
            Object.entries(errors).forEach(([field, messages]) => {
              console.log(field+'@@'+messages[0]);
              const label = fieldNames[field] || field;
              // 避免重複 field 名稱出現在訊息中
              const cleanMessage = messages[0].replace(new RegExp(field, 'gi'), '').trim();
              allMessages += `${label}：${cleanMessage}\n`;
            }); 
            $('#formErrors').removeClass('d-none').html(allMessages.replace(/\n/g, '<br>'));          
          } else {
            alert("系統錯誤，請稍後再試！");
          }
        }
      });
    });

    $(".close-btn").click(function() {
      $("#delete-id").val($(this).data("setid"));
      $("#treatmentInfo").html(
        $(this).data("name") );
      });

    $("#delete-treatment-form").submit(function(e) {
      e.preventDefault();

      let formData = {
        _token: $("input[name=_token]").val(),
        id: $("#delete-id").val()
      };
    
      $.ajax({
        url: "/delete_treatment_set/" + $("#delete-id").val(),
        method: "POST",
        data: formData,
        dataType: "json",
        success: function(response) {
          if (response.success) {
            alert("刪除成功！");
            location.reload();
          } else {
            alert("錯誤："+response.message);
          }
        },
        error: function() {
          alert("錯誤！");
        }
      });
    });
  }); 
</script>
@endsection
