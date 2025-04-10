<div id="nhi_addShow">
  <div id="nhi_Title"></div>
  <div id="nhi_File"></div>
</div>
  <div class="mt-3">
    <input type="hidden" id="nhi_fileCount" name="nhi_fileCount" >
    <input type="button" id="nhi_addFile" class="btn btn-info" value="新增醫令">
  </div>

<select id="emp-template" style="display:none;">
  <option value="" data-id=""></option>
  @foreach ($open_staffs_withIdNo as $emp)
    <option value="{{ $emp->IdNo }}" data-id="{{ $emp->employeeID }}">
    {{ $emp->name }}
    </option>
  @endforeach
</select>

<script src="{{ asset('js/nhiservice/add_nhicode.js') }}"></script>