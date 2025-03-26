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
