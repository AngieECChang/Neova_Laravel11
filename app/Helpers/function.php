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
