<style>
    /* ===== STYLE ASLI (TIDAK DIUBAH) ===== */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 50%, #80cbc4 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 15px
    }

    .card {
      background: #fff;
      padding: 30px 28px;
      width: 100%;
      max-width: 420px;
      border-radius: 12px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, .2)
    }

    .logo-container {
      text-align: center;
      margin-bottom: 15px
    }

    .logo-container img {
      height: 70px
    }

    h1 {
      text-align: center;
      font-size: 20px;
      font-weight: 600;
      color: #2c3e50;
      margin-bottom: 20px
    }

    .tabs {
      display: flex;
      border-bottom: 2px solid #e0e0e0;
      margin-bottom: 20px
    }

    .tab {
      flex: 1;
      padding: 10px;
      border: none;
      background: none;
      font-size: 14px;
      font-weight: 600;
      color: #7f8c8d;
      cursor: pointer;
      border-bottom: 3px solid transparent;
      transition: all 0.3s ease;
      position: relative;
    }

    .tab:hover {
      color: #26a69a;
      background: rgba(38, 166, 154, 0.05);
    }

    .tab.active {
      color: #26a69a;
      border-bottom-color: #26a69a
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
      animation: fadeSlide 0.4s ease;
    }

    @keyframes fadeSlide {
      from {
        opacity: 0;
        transform: translateY(15px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-group {
      margin-bottom: 14px
    }

    label {
      font-size: 13px;
      font-weight: 500;
      color: #34495e;
      margin-bottom: 6px;
      display: block
    }

    input,
    select {
      width: 100%;
      padding: 11px 12px;
      font-size: 14px;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      transition: all 0.3s ease;
      font-family: inherit;
      line-height: 1.4;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }

    input:focus, select:focus {
      outline: none;
      border-color: #26a69a;
      box-shadow: 0 0 0 3px rgba(38, 166, 154, 0.1);
    }

    input::placeholder {
      color: #b0bec5;
      font-size: 13px;
    }

    button[type="submit"], button.submit-btn {
      width: 100%;
      padding: 11px;
      background: linear-gradient(135deg, #4db6ac, #26a69a);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      margin-top: 8px;
      position: relative;
    }

    button[type="submit"]:hover:not(:disabled), button.submit-btn:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(77, 182, 172, 0.4);
    }

    button[type="submit"]:active:not(:disabled), button.submit-btn:active:not(:disabled) {
      transform: translateY(0);
    }

    button[type="submit"]:disabled, button.submit-btn:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }

    /* Loading Spinner */
    .spinner {
      display: none;
      width: 18px;
      height: 18px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      border-top-color: white;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: 0 auto;
    }

    button.loading .spinner {
      display: block;
    }

    button.loading .btn-text {
      display: none;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .error-box {
      background: #fee;
      border-left: 4px solid #e74c3c;
      color: #c0392b;
      padding: 10px;
      border-radius: 6px;
      font-size: 12px;
      margin-top: 12px;
      display: none
    }

    .error-box.show {
      display: block
    }

    /* ===== PHONE INPUT WITH PREFIX ===== */
    .phone-input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .phone-prefix {
      position: absolute;
      left: 12px;
      font-size: 14px;
      color: #34495e;
      font-weight: 500;
      pointer-events: none;
      z-index: 1;
    }

    #regNoHP {
      padding-left: 50px;
    }

    /* ===== POPUP MODAL BASE ===== */
    .popup-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 9999;
      justify-content: center;
      align-items: center;
      animation: fadeInOverlay 0.3s ease;
    }

    .popup-overlay.show {
      display: flex;
    }

    @keyframes fadeInOverlay {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .popup-content {
      background: white;
      padding: 40px 35px;
      border-radius: 16px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      max-width: 420px;
      width: 90%;
      text-align: center;
      animation: slideUp 0.4s ease;
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .popup-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #4db6ac 0%, #26a69a 100%);
      border-radius: 50%;
      margin: 0 auto 25px;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: scaleIn 0.5s ease 0.2s both;
    }

    @keyframes scaleIn {
      from {
        transform: scale(0);
      }
      to {
        transform: scale(1);
      }
    }

    .popup-icon svg {
      width: 45px;
      height: 45px;
      color: white;
    }

    .popup-content h2 {
      font-size: 24px;
      color: #2c3e50;
      margin-bottom: 15px;
      font-weight: 600;
    }

    .popup-content p {
      color: #7f8c8d;
      font-size: 15px;
      line-height: 1.6;
      margin-bottom: 30px;
    }

    .popup-btn {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #4db6ac 0%, #26a69a 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .popup-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(77, 182, 172, 0.4);
    }

    .popup-btn:active {
      transform: translateY(0);
    }

    /* ===== CONFIRM POPUP (2 BUTTONS) ===== */
    .popup-buttons {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }

    .popup-btn-cancel {
      flex: 1;
      padding: 13px;
      background: #e0e0e0;
      color: #34495e;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .popup-btn-cancel:hover {
      background: #d0d0d0;
      transform: translateY(-2px);
    }

    .popup-btn-confirm {
      flex: 1;
      padding: 13px;
      background: linear-gradient(135deg, #4db6ac 0%, #26a69a 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .popup-btn-confirm:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(77, 182, 172, 0.4);
    }

    .popup-btn-confirm:active {
      transform: translateY(0);
    }

    /* ===== CEK INVOICE ===== */
    .invoice-input-row {
      display: flex;
      gap: 8px
    }

    .invoice-loading {
      margin-top: 10px;
      font-size: 13px;
      color: #7f8c8d;
      display: none
    }

    .invoice-error {
      margin-top: 10px;
      color: #e74c3c;
      font-size: 13px
    }

    .invoice-box {
      margin-top: 18px;
      padding-top: 14px;
      border-top: 1px solid #eee;
      display: none
    }

    .invoice-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 14px
    }

    .invoice-label {
      font-weight: 600
    }

    .invoice-value {
      text-align: right;
      max-width: 60%
    }

    /* Mobile Responsiveness */
    @media (max-width: 480px) {
      body {
        padding: 10px;
      }

      .card {
        padding: 25px 20px;
      }

      h1 {
        font-size: 18px;
        margin-bottom: 18px;
      }

      .tab {
        font-size: 13px;
        padding: 9px 10px;
      }

      .logo-container img {
        height: 60px;
      }

      input, select {
        font-size: 16px;
      }

      .popup-content {
        padding: 30px 25px;
      }

      .popup-content h2 {
        font-size: 20px;
      }

      .popup-content p {
        font-size: 14px;
      }
    }
  </style>
