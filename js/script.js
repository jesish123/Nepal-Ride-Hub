document.addEventListener('DOMContentLoaded', () => {

    // Password Visibility Toggle using Event Delegation (bulletproof)
    document.addEventListener('click', function (e) {
        const toggleIcon = e.target.closest('.toggle-password');
        if (toggleIcon) {
            const wrapper = toggleIcon.closest('.input-with-icon');
            if (wrapper) {
                const input = wrapper.querySelector('input');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    }
                }
            }
        }
    });


    // Mobile navigation toggle
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    }

    // Helper function to show alerts
    const showAlert = (alertEl, message, isSuccess) => {
        if (!alertEl) return;
        alertEl.style.display = 'block';
        alertEl.innerHTML = message;
        alertEl.className = 'alert ' + (isSuccess ? 'alert-success' : 'alert-danger');
    };

    // Login Form AJAX
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('loginBtn');
            const alertEl = document.getElementById('loginAlert');
            const formData = new FormData(loginForm);

            btn.disabled = true;
            btn.innerHTML = 'Logging in... <i class="fas fa-spinner fa-spin"></i>';

            try {
                const apiPath = window.location.pathname.includes('/uploads/') ? '../api/auth.php' : 'api/auth.php';
                const response = await fetch(apiPath + '?action=login', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showAlert(alertEl, data.message, true);
                    window.location.href = data.redirect;
                } else {
                    showAlert(alertEl, data.message, false);
                    btn.disabled = false;
                    btn.innerHTML = 'Login <i class="fas fa-sign-in-alt"></i>';
                }
            } catch (err) {
                showAlert(alertEl, 'A network error occurred.', false);
                btn.disabled = false;
                btn.innerHTML = 'Login <i class="fas fa-sign-in-alt"></i>';
            }
        });
    }

    // Register Form AJAX
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('registerBtn');
            const alertEl = document.getElementById('registerAlert');
            const formData = new FormData(registerForm);

            btn.disabled = true;
            btn.innerHTML = 'Registering... <i class="fas fa-spinner fa-spin"></i>';

            try {
                const apiPath = window.location.pathname.includes('/uploads/') ? '../api/auth.php' : 'api/auth.php';
                const response = await fetch(apiPath + '?action=register', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showAlert(alertEl, data.message, true);
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
                } else {
                    showAlert(alertEl, data.message, false);
                    btn.disabled = false;
                    btn.innerHTML = 'Register <i class="fas fa-user-plus"></i>';
                }
            } catch (err) {
                showAlert(alertEl, 'A network error occurred.', false);
                btn.disabled = false;
                btn.innerHTML = 'Register <i class="fas fa-user-plus"></i>';
            }
        });
    }

    // Verify Form AJAX (2FA Email Code)
    const verifyForm = document.getElementById('verifyForm');
    if (verifyForm) {
        verifyForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('verifyBtn');
            const alertEl = document.getElementById('verifyAlert');
            const formData = new FormData(verifyForm);

            btn.disabled = true;
            btn.innerHTML = 'Verifying... <i class="fas fa-spinner fa-spin"></i>';

            try {
                const apiPath = window.location.pathname.includes('/uploads/') ? '../api/auth.php' : 'api/auth.php';
                const response = await fetch(apiPath + '?action=verify_code', {
                    method: 'POST', body: formData
                });
                const data = await response.json();

                if (data.success) {
                    showAlert(alertEl, data.message, true);
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    showAlert(alertEl, data.message, false);
                    btn.disabled = false;
                    btn.innerHTML = 'Verify Code <i class="fas fa-check-circle"></i>';
                }
            } catch (err) {
                showAlert(alertEl, 'A network error occurred.', false);
                btn.disabled = false;
                btn.innerHTML = 'Verify Code <i class="fas fa-check-circle"></i>';
            }
        });
    }

    // User Profile Dropdown Toggle
    const profileDropdown = document.querySelector('.user-profile-dropdown');
    if (profileDropdown) {
        const trigger = profileDropdown.querySelector('div');
        const menu = profileDropdown.querySelector('.dropdown-menu');

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });

        window.addEventListener('click', () => {
            menu.style.display = 'none';
        });
    }

    // AI Assistant is now handled by js/ai_chatbot.js
});
