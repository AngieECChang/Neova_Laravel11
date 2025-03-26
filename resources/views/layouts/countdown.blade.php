<script>
    var sessionTimeout = {{ config('session.lifetime') }} * 60; // 轉換為秒數
    var warningTime = 3 * 60; // 提前 3 分鐘提醒
    var countdown = sessionTimeout;
    var lastActivity = localStorage.getItem('session_last_activity') || Date.now();
    
    /* 每秒計算倒數時間，並更新 UI。如果時間到了，則讓所有分頁登出 */
    function startSessionTimer() {
      setInterval(function () {
        var lastOpenedPage = localStorage.getItem('last_opened_page');
        if (!lastOpenedPage) {
            lastOpenedPage = Date.now(); // 如果找不到記錄，則使用當前時間
            localStorage.setItem('last_opened_page', lastOpenedPage);
        }

        var now = Date.now();
        var elapsed = Math.floor((now - lastOpenedPage) / 1000); // 計算從最後開啟的分頁到現在過了多久
        countdown = sessionTimeout - elapsed; // 剩餘時間 = 設定的 Session 時間 - 已過去的秒數

        // 存入 localStorage 讓其他分頁同步
        localStorage.setItem('session_remaining_time', countdown);

        // 更新畫面上的倒數時間
        $('#timer-countdown').text(countdown);

        // 當倒數至 3 分鐘時，顯示警告
        if (countdown === warningTime && !localStorage.getItem('session_warning_shown')) {
            localStorage.setItem('session_warning_shown', 'true');
            showSessionWarning();
        }

        // 如果時間歸零，所有分頁強制登出
        if (countdown <= 0) {
            localStorage.setItem('session_logged_out', 'true');
            window.location.href = "{{ route('logout') }}";
        }
      }, 1000);
    }

    /* 監聽 localStorage 變更，當有新分頁開啟時，所有分頁會同步重置倒數計時 */
    function listenForSessionUpdates() {
      window.addEventListener('storage', function (event) {
        if (event.key === 'session_remaining_time') {
          countdown = parseInt(event.newValue);
          $('#timer-countdown').text(countdown);
        }

        if (event.key === 'last_opened_page') {
          lastActivity = parseInt(event.newValue);
        }

        if (event.key === 'session_logged_out' && event.newValue === 'true') {
          alert("Session已過期，請重新登入！");
          window.location.href = "{{ route('logout') }}";
        }
      });
    }

    function showSessionWarning() {
      if (confirm("Session即將到期，是否要延長？")) {
        extendSession();
      }
    }

    function extendSession() {
      $.ajax({
        url: "{{ route('session.extend') }}",
        type: "POST",
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
        success: function () {
          alert("Session 已延長！");
          updateLastOpenedPageTime(); // 重設最後開啟的分頁時間
          localStorage.removeItem('session_warning_shown'); // 移除彈窗標記
        },
        error: function () {
          alert("無法延長 Session，請重新登入！");
          window.location.href = "{{ route('logout') }}";
        }
      });
    }

    /* 每次新開分頁時，將 last_opened_page 更新為當前時間，讓所有分頁同步基於這個時間來計算倒數 */
    function updateLastOpenedPageTime() {
      var now = Date.now(); // 取得當前時間（毫秒）
      localStorage.setItem('last_opened_page', now); // 存入 localStorage
    }

    $(document).ready(function () {
      updateLastOpenedPageTime(); // 記錄新開分頁的時間
      startSessionTimer();
      listenForSessionUpdates();
    });
  </script>