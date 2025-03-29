<?php
  if (!function_exists('dateTo_c')) {
    function dateTo_c($in_date, $in_txt = "")
    {
      $date="";
      $ch_date = explode("-", $in_date);
      if (count($ch_date) !== 3) return "無效日期格式";
      $ch_date[0] = $ch_date[0] - 1911;

      if ($in_txt == "") {
        if ($ch_date[0] > 0) {
          $date = $ch_date[0] . "/" . $ch_date[1] . "/" . $ch_date[2];
        } else {
          $date = "民國前 " . abs($ch_date[0]) . "/" . $ch_date[1] . "/" . $ch_date[2];
        }
      } else {
        if ($ch_date[0] > 0) {
          $date = "民國 " . $ch_date[0] . "/" . $ch_date[1] . "/" . $ch_date[2];
        } else {
          $date = "民國前 " . abs($ch_date[0]) . "/" . $ch_date[1] . "/" . $ch_date[2];
        }
      }
      return $date;
    }
  }

  if (!function_exists('calcperiodwithyear')) {
  //計算天數 (大於一年即回傳年數)
    function senioritywithyear($startdate, $enddate)
    {
      if ($startdate != "" && $enddate != "") {
        $days = (((strtotime($enddate) - strtotime($startdate)) / 3600) / 24) + 1;
        if ($days >= 365) {
            $years = round(($days / 365), 1);
            return $years . "年";
        } else {
            return round($days) . "天";
        }
      } else {
        return "---";
      }
    }
  }

  //遮罩身份證字號
  if (!function_exists('maskIdNo')) {
    function maskIdNo($IdNo)
    {
      if (strlen($IdNo) <= 6) return $IdNo;
      return substr($IdNo, 0, 3) . str_repeat('*', strlen($IdNo) - 6) . substr($IdNo, -3);
    }
  }

  /**
   * 將選項依欄數切塊，並加入對應 col class
   *
   * @param array $options 選項陣列（key => label）
   * @param int $columnsPerRow 幾個一排（用來算 col-md）
   * @return array 每列一組的陣列，每項含 key, label, colClass
   * 自動處理排版
  */
  if (!function_exists('chunkWithBootstrapCol')) {
    function chunkWithBootstrapCol(array $options, int $columnsPerRow): array
    {
      $col = 12 / $columnsPerRow;
      $chunks = collect($options)->map(function ($label, $key) use ($col) {
          return [
            'key' => $key,
            'label' => $label,
            'colClass' => "col-12 col-md-{$col}",
          ];
      })->chunk($columnsPerRow);

      return $chunks->toArray();
    }
  }

  if (!function_exists('mapRequestData')) {
    /**
     * 根據欄位對應表轉換 request 輸入值為資料庫對應格式
     * @param \Illuminate\Http\Request $request
     * @param array $map 例如 ['PhoneNumber' => 'phone']
     * @return array
     */
    function mapRequestData($request, array $map)
    {
      return collect($map)->mapWithKeys(function ($dbColumn, $requestKey) use ($request) {
        return [$dbColumn => $request->input($requestKey)];
      })->toArray();
    }
  }
