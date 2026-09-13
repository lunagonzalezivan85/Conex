document.addEventListener('DOMContentLoaded', function() {
    const toggleBtns = document.querySelectorAll('.toggle-btn');
    const tipoCuentaInput = document.getElementById('tipo_cuenta');
    const razonSocialGroup = document.getElementById('razonSocialGroup');

    if (toggleBtns.length && tipoCuentaInput) {
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tipo = this.dataset.tipo;
                switchTipoCuenta(tipo);
            });
        });

        function switchTipoCuenta(tipo) {
            tipoCuentaInput.value = tipo;

            toggleBtns.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.tipo === tipo);
            });

            if (razonSocialGroup) {
                const razonInput = razonSocialGroup.querySelector('input');
                if (tipo === 'empresa') {
                    razonSocialGroup.classList.remove('hidden');
                    razonInput?.setAttribute('required', '');
                } else {
                    razonSocialGroup.classList.add('hidden');
                    razonInput?.removeAttribute('required');
                }
            }
        }
    }

    // Password show/hide toggle
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const eyeOpen = this.querySelector('.eye-open');
            const eyeClosed = this.querySelector('.eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen?.classList.add('hidden');
                eyeClosed?.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeOpen?.classList.remove('hidden');
                eyeClosed?.classList.add('hidden');
            }
        });
    });

    // Password strength meter
    const passwordInput = document.getElementById('password');
    const strengthFill = document.getElementById('strengthFill');
    const strengthLabel = document.getElementById('strengthLabel');
    const reqItems = document.querySelectorAll('.password-requirements li');

    if (passwordInput && strengthFill && strengthLabel) {
        passwordInput.addEventListener('input', function() {
            updateStrength(this.value);
        });
    }

    function updateStrength(password) {
        const checks = {
            length: password.length >= 10,
            upper: /[A-Z]/.test(password),
            lower: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[^A-Za-z0-9]/.test(password),
        };

        const score = Object.values(checks).filter(Boolean).length;

        reqItems.forEach(li => {
            const req = li.dataset.req;
            if (checks[req]) {
                li.classList.add('met');
                li.classList.remove('unmet');
                li.querySelector('.check-icon').innerHTML = checkSvg();
            } else {
                li.classList.add('unmet');
                li.classList.remove('met');
                li.querySelector('.check-icon').innerHTML = dashSvg();
            }
        });

        const levels = ['weak', 'fair', 'good', 'strong'];
        const labels = ['Debil', 'Regular', 'Buena', 'Fuerte'];

        strengthFill.className = 'password-strength-fill';
        strengthLabel.className = 'password-strength-label';

        if (password.length === 0) {
            strengthFill.classList.add('');
            strengthLabel.textContent = '';
        } else if (score <= 2) {
            strengthFill.classList.add('weak');
            strengthLabel.classList.add('weak');
            strengthLabel.textContent = 'Fortaleza: Debil';
        } else if (score === 3) {
            strengthFill.classList.add('fair');
            strengthLabel.classList.add('fair');
            strengthLabel.textContent = 'Fortaleza: Regular';
        } else if (score === 4) {
            strengthFill.classList.add('good');
            strengthLabel.classList.add('good');
            strengthLabel.textContent = 'Fortaleza: Buena';
        } else {
            strengthFill.classList.add('strong');
            strengthLabel.classList.add('strong');
            strengthLabel.textContent = 'Fortaleza: Fuente';
        }
    }

    function checkSvg() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>';
    }

    function dashSvg() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>';
    }

    // Suggest password
    const suggestBtn = document.getElementById('suggestPasswordBtn');
    const suggestValue = document.getElementById('suggestPasswordValue');

    if (suggestBtn) {
        suggestBtn.addEventListener('click', function() {
            const suggested = generatePassword();
            if (suggestValue) {
                suggestValue.textContent = suggested;
                suggestValue.parentElement.classList.remove('hidden');
            }
            if (passwordInput) {
                passwordInput.value = suggested;
                updateStrength(suggested);
            }
            const confirmInput = document.getElementById('password_confirm');
            if (confirmInput) {
                confirmInput.value = suggested;
            }
        });
    }

    // Wizard navigation
    const wizardNextBtns = document.querySelectorAll('.wizard-next');
    const wizardPrevBtns = document.querySelectorAll('.wizard-prev');
    const wizardPanels = document.querySelectorAll('.wizard-panel');
    const wizardIndicators = document.querySelectorAll('.wizard-step-indicator');

    function generatePassword() {
        const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        const lower = 'abcdefghjkmnpqrstuvwxyz';
        const numbers = '23456789';
        const special = '!@#$%&*?';
        const all = upper + lower + numbers + special;

        let password = '';
        password += upper[Math.floor(Math.random() * upper.length)];
        password += lower[Math.floor(Math.random() * lower.length)];
        password += numbers[Math.floor(Math.random() * numbers.length)];
        password += special[Math.floor(Math.random() * special.length)];

        for (let i = 0; i < 8; i++) {
            password += all[Math.floor(Math.random() * all.length)];
        }

        return password.split('').sort(() => Math.random() - 0.5).join('');
    }

    function goToStep(step) {
        wizardPanels.forEach(panel => {
            panel.classList.toggle('active', panel.dataset.panel == step);
        });
        wizardIndicators.forEach(ind => {
            const indStep = parseInt(ind.dataset.step);
            ind.classList.toggle('active', indStep === step);
            ind.classList.toggle('completed', indStep < step);
        });
    }

    wizardNextBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const nextStep = parseInt(this.dataset.next);
            const currentPanel = this.closest('.wizard-panel');
            const requiredInputs = currentPanel.querySelectorAll('input[required]');
            let valid = true;

            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = 'var(--danger)';
                    input.addEventListener('input', function handler() {
                        this.style.borderColor = '';
                        this.removeEventListener('input', handler);
                    });
                }
            });

            if (valid) {
                goToStep(nextStep);
            }
        });
    });

    wizardPrevBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const prevStep = parseInt(this.dataset.prev);
            goToStep(prevStep);
        });
    });
});
