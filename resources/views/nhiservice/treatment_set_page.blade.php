@extends('layouts.app')
@section('content')
@php
//print_r($treatment_item_arrayinfo);
//return;
@endphp
<h1 class="h3 mb-4 text-gray-800">{{ request()->segment(3)==''?'新增':'編輯' }}處置套組</h1>
<form id="form1" method="POST" action="{{ route('treatment_set_save') }}">
<div class="card shadow-sm mb-2">
  <div class="card-body" style="max-height: 570px; overflow-y: auto;">
    @csrf
    <input type="hidden" name="set_id" value="{{ request()->segment(3) ?? '0' }}">
    <table class="table table-bordered align-middle">
      <tbody>
        <tr>
          <th width="120" class="table-success">套組名稱</th>
          <td>
         
              <input type="text" class="form-control" name="description" id="description" value="{{ optional($result[0])->description ?? '' }}">
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div>
  @include('includes.treatment_set_items_saved')
</div>
<div>
  @include('includes.treatment_set_items')
</div>
<div class="text-center mt-4">
  <a href="{{ route('treatment_set') }}" class="btn btn-secondary" style="margin-right:40px !important;">回上頁</a>
  <button type="submit" class="btn btn-primary">{{ request()->segment(3)==''?'儲存':'更新' }}</button>
</div>
</form>
@endsection
