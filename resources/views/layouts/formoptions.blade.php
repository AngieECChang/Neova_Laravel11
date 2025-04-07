@extends('layouts.app')
@section('content')
<style>
.custom-icon-stack {
  font-size: 2.5rem; /* 調整這裡大小，例如 2rem, 3rem, 4rem 等 */
}
</style>
<h1 class="h3 mb-4 text-gray-800">{{ $permission_items[0]->cate_shortname." / ".$permission_items[0]->subcate_name }}</h1>
  <div class="container">
    <div class="row">
      @foreach ($permission_items as $item)
        <div class="col-auto text-center mb-3">
          <a href="{{ $item->link }}">
            <span class="fa-stack fa-2x custom-icon-stack">
              <i class="fa fa-square fa-stack-2x" style="color: #7373B9;"></i>
              <i class="{{ $item->icon }} fa-stack-1x fa-inverse"></i>
            </span>
            <br>
            <span style="color:#0072E3; font-weight:bold">{{ $item->name }}</span>
          </a>
        </div>
      @endforeach
    </div>
</div>
@endsection
