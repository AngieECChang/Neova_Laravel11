

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