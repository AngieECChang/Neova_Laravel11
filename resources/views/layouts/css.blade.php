  <!-- jQuery UI -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<!-- jQuery Validation Engine 套件 -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-validation-engine@2.6.4/css/validationEngine.jquery.css"> -->
<!-- <link rel="stylesheet" href="{{ asset('css/validationEngine.jquery.css') }}"> -->
<!-- ValidationEngine 樣式 -->
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-validation-engine@2.6.4/css/validationEngine.jquery.css"> -->

<!-- 其他樣式 -->
  <link rel="stylesheet" type="text/css" href="/app/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/sb-admin-2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
  <link rel='stylesheet' type="text/css" href="{{ asset('css/jquery.datetimepicker.css') }}" />
  <!-- <link rel='stylesheet' type="text/css" href='css/validationEngine.jquery.css' /> -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

  <!-- Bootstrap 5 -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  <!-- Bootstrap Datepicker -->
  <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" rel="stylesheet"> -->

  <style>
    @media print {
      @page {
        size: A4 portrait;
        margin: 1.5cm;
      }
      body {
        margin: 0 !important;
      }
      .sidebar,
      .topbar,
      .navbar,
      .scroll-to-top,
      .btn,
      .modal,
      .dropdown,
      footer,
      #case_selector,
      .sidebar-card {
        display: none !important;
      }
      #content-wrapper, #content, .container-fluid {
        margin: 0 !important;
        padding: 0 !important;
        width: 100%;
      }

      /* 顯示主要內容區域 */
      @media print {
        body * {
          visibility: hidden;
        }
        .container-fluid, .container-fluid * {
          visibility: visible;
        }
        .container-fluid {
          position: absolute;
          left: 0;
          top: 0;
          width: 100%;
        }
      }
    }
  </style>
