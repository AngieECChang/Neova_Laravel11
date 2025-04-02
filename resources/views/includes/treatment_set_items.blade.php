<div id="treatmentset_addShow">
  <div id="treatmentset_Title"></div>
  <div id="treatmentset_File"></div>
</div>
<div class="mt-3">
  <input type="button" id="treatmentset_addFile" class="btn btn-info" value="新增項目">
</div>

<select id="treatment-template" style="display:none;">
  <option value="" data-id="" data-code="" data-name=""></option>
  @foreach ($treatment_items as $item_key=>$item_value)
    <option value="{{ $item_value->short_code }}" data-id="{{ $item_value->id }}" data-code="{{ $item_value->treatment_code }}" data-name="{{ $item_value->treatment_name_zh }}">
     {{ $item_value->short_code."【".$item_value->treatment_code."】" }}
    </option>
  @endforeach
</select>

<script src="{{ asset('js/nhiservice/treatment_set_items.js') }}"></script>