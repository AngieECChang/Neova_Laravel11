<div id="form01_1_addShow">
  <div id="form01_1_Title"></div>
  <div id="form01_1_File"></div>
</div>
<div class="mt-3">
  <input type="button" id="form01_1_addFile" class="btn btn-warning" value="新增共照團隊親友">
</div>

<select id="relative-template" style="display:none;">
  <option value="" data-id=""></option>
  @foreach (getOptionList('Relationship') as $relation)
    <option value="{{ $relation }}" data-id="{{ $relation }}">
     {{ $relation }}
    </option>
  @endforeach
</select>
<script src="{{ asset('js/hcevaluation/hcevaluation01_relative.js') }}"></script>