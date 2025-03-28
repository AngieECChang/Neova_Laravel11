<!DOCTYPE html>
<html lang="zh-Hant">
@php
  $gender = config('public.gender');
@endphp
<head>
  <meta charset="UTF-8">
  <title>全人周全性評估 - 基本資料列印</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    @page {
      size: A4;
      margin: 1.5cm;
    }

    body {
      font-family: "Microsoft JhengHei", sans-serif;
      font-size: 11pt;
      margin: 0;
    }

    .table th {
      background-color: #f2f2f2;
      text-align: center;
    }

    img {
      max-width: 150px;
      max-height: 150px;
    }

    .label {
      font-weight: bold;
      width: 120px;
    }
  </style>
</head>
<body>
  <div class="container mt-3">
    <h3 class="text-center mb-4">全人周全性評估 - 基本資料</h3>

    <table class="table table-bordered align-middle">
      <tbody>
        <tr>
          <th>姓名</th>
          <td>{{ optional($the_case)->name }}</td>
          <th>性別</th>
          <td>{{ optional($the_case)->gender ? $gender[optional($the_case)->gender] : '' }}</td>
        </tr>
        <tr>
          <th>出生日期</th>
          <td>{{ dateTo_c(optional($the_case)->birthdate) }}</td>
          <th>身份證字號</th>
          <td>{{ optional($the_case)->IdNo }}</td>
        </tr>
        <tr>
          <th>聯絡電話</th>
          <td colspan="3">{{ optional($result)->PhoneNumber }}</td>
        </tr>
        <tr>
          <th>個案類型</th>
          <td>{{ optional($result)->CaseType ? getOptionList('CaseType')[optional($result)->CaseType] : '' }}</td>
          <th>收案來源</th>
          <td>{{ optional($result)->CaseSource ? getOptionList('CaseSource')[optional($result)->CaseSource] : '' }} {{ optional($result)->CaseSource_other }}</td>
        </tr>
        <tr>
          <th>地址</th>
          <td colspan="3">{{ optional($result)->city }}{{ optional($result)->town }}{{ optional($result)->lane }}</td>
        </tr>
        <tr>
          <th>教育程度</th>
          <td>{{ optional($result)->Education ? getOptionList('Education')[optional($result)->Education] : '' }} {{ optional($result)->Education_other }}</td>
          <th>婚姻狀況</th>
          <td>{{ optional($result)->Marriage ? getOptionList('Marriage')[optional($result)->Marriage] : '' }} {{ optional($result)->Marriage_other }}</td>
        </tr>
        <tr>
          <th>個案描述</th>
          <td colspan="3">{{ optional($result)->CaseDesc }}</td>
        </tr>
        <tr>
          <th>評估日期</th>
          <td>{{ optional($result)->date }}</td>
          <th>評估人員</th>
          <td>{{ optional($result)->NurseID }}</td>
        </tr>
      </tbody>
    </table>

    <div class="text-end mt-4" style="font-size: 10pt;">
      列印日期：{{ now()->format('Y-m-d') }}
    </div>
  </div>

  <script>
    window.onload = function() {
      setTimeout(() => {
        window.print();
      }, 500);
    };

    window.onafterprint = function() {
      window.close();
    };
  </script>
</body>
</html>
