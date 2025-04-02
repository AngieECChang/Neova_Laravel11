<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>使用者登入</title>
  <!-- Bootstrap CSS + Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
    .captcha-img {
      cursor: pointer;
      height: 40px;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="login-container">
    <h3 class="text-center">使用者登入</h3>

    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" id="errorAlert">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger text-center alert-dismissible fade show" id="errorAlert">
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
        <div class="input-group">
          <input type="password" id="password" name="password" class="form-control" placeholder="請輸入密碼" required>
          <span class="input-group-text" style="cursor: pointer;" onclick="togglePasswordVisibility()">
            <i class="bi bi-eye-slash" id="toggleIcon"></i>
          </span>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">驗證碼：</label>
        <div class="input-group">
          <input type="text" name="captcha" class="form-control" placeholder="請輸入右側驗證碼" required>
          <span class="input-group-text p-0">
            <img id="captchaImg" src="{{ captcha_src('flat') }}" class="captcha-img" title="點擊或等待刷新驗證碼">
          </span>
        </div>
        <div class="form-text text-end"><small id="countdownText">60 秒後自動更新</small></div>
      </div>

      <button type="submit" class="btn btn-primary w-100">登入</button>
    </form>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function togglePasswordVisibility() {
  const passwordInput = document.getElementById("password");
  const toggleIcon = document.getElementById("toggleIcon");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    toggleIcon.classList.remove("bi-eye-slash");
    toggleIcon.classList.add("bi-eye");
  } else {
    passwordInput.type = "password";
    toggleIcon.classList.remove("bi-eye");
    toggleIcon.classList.add("bi-eye-slash");
  }
}

// 錯誤訊息自動消失
setTimeout(() => {
  const alert = document.getElementById("errorAlert");
  if (alert) alert.classList.remove("show");
}, 5000);

// 驗證碼圖片點擊/自動刷新
let countdown = 60;
const captchaImg = document.getElementById("captchaImg");
const countdownText = document.getElementById("countdownText");

captchaImg.addEventListener("click", refreshCaptcha);

const timer = setInterval(() => {
  countdown--;
  if (countdown <= 0) {
    refreshCaptcha();
    countdown = 60;
  }
  countdownText.textContent = `${countdown} 秒後自動更新`;
}, 1000);

function refreshCaptcha() {
  captchaImg.src = '{{ captcha_src("flat") }}?' + Math.random();
  countdown = 60;
}
</script>
</body>
</html>
