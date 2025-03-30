@if(!empty(optional($medical_result)))
  <table class="table table-bordered table-sm" style="font-size: 10pt;">
    <thead class="table-light">
      <tr>
      { tagName: 'th', className:'table-info', style: 'width:110px;', innerHTML: '成員姓名' },
          { tagName: 'th', className:'table-info', style: 'width:120px;', innerHTML: '加入照護團隊日期' },
          { tagName: 'th', className:'table-info', style: 'width:125px;', innerHTML: '身分證字號' },
          { tagName: 'th', className:'table-info', style: 'width:100px;', innerHTML: '職稱' },
          { tagName: 'th', className:'table-info', style: 'width:110px;', innerHTML: '電話' },
          { tagName: 'th', className:'table-info', style: 'width:120px;', innerHTML: '備註' },
          { tagName: 'th', className:'table-info', style: 'width:60px;', innerHTML: '功能' }
        <th width="80">成員姓名</th>
        <th width="80">加入照護團隊日期</th>
        <th width="80">身分證字號</th>
        <th width="80">職稱</th>
        <th width="80">電話</th>
        <th>備註</th>
        <th width="90">填寫人員</th>
        <th class="printcol" width="40">功能</th>
      </tr>
    </thead>
  </tbable>
@endif

<div id="form01_addShow">
  <div id="form01_Title"></div>
  <div id="form01_File"></div>
</div>
  <input type="hidden" id="form01_fileCount" name="form01_fileCount" value="0">
  <input type="hidden" id="form01_oldCount" name="form01_oldCount" value="0">
  <div class="mt-3">
    <input type="button" id="form01_addFile" class="btn btn-info" value="新增共照團隊醫事人員">
  </div>
<select id="medical-template" style="display:none;">
  <option value="" data-id=""></option>
  @foreach (getOptionList('JobTitle') as $job)
    <option value="{{ $job }}" data-id="{{ $job }}">
     {{ $job }}
    </option>
  @endforeach
</select>
<script src="{{ asset('js/hcevaluation/hcevaluation01_medical.js') }}"></script>