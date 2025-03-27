<?php

  if (! function_exists('getOptionLabel')) {
    function getOptionLabel($type, $key)
    {
      $options = include app_path('Helpers/options.php');
      return $options[$type][$key] ?? null;
    }
    //Blade 中這樣用
    //<p>狀態：{{ getOptionLabel('status', 1) }}</p>
    //<p>性別：{{ getOptionLabel('gender', 'F') }}</p>
  }

  if (! function_exists('getOptionKey')) {
    function getOptionKey($type, $label)
    {
      $options = include app_path('Helpers/options.php');
      $flipped = array_flip($options[$type] ?? []);
      return $flipped[$label] ?? null;
    }
    //Blade 中這樣用
    //<p>「女性」的代碼是：{{ getOptionKey('gender', '女性') }}</p>
  }