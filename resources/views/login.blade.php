<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>使用者登入</title>
  <!-- 加入 Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      background-image: url('{{ asset("images/index_image.jpg") }}');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }
    .login-container {
      max-width: 400px;
      margin: 100px auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>
<div class="container">
  <div class="login-container">
    <h3 class="text-center">使用者登入</h3>
    @if(session('error'))
      <div class="alert alert-danger text-center">
        {{ session('error') }}
      </div>
    @endif
    <form action="{{ route('login.process') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label class="form-label">使用者 ID：</label>
        <input type="text" name="user_id" class="form-control" placeholder="請輸入帳號" required>
      </div>
      <div class="mb-3">
        <label class="form-label">密碼：</label>
        <input type="password" name="password" class="form-control" placeholder="請輸入密碼" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">登入</button>
    </form>
  </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
