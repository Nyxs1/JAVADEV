/**
 * Username Checker Module
 * 
 * Handles Instagram-style username availability check and save functionality.
 * Used in settings page Account tab.
 */
export class UsernameChecker {
    constructor(options = {}) {
        this.input = document.getElementById(options.inputId || 'username-input');
        this.checkBtn = document.getElementById(options.checkBtnId || 'btn-check-username');
        this.saveBtn = document.getElementById(options.saveBtnId || 'btn-save-username');
        this.statusDiv = document.getElementById(options.statusDivId || 'username-check-status');
        this.statusIcon = document.getElementById(options.statusIconId || 'username-status-icon');
        this.statusText = document.getElementById(options.statusTextId || 'username-status-text');
        
        this.checkUrl = options.checkUrl || '/settings/username/check';
        this.saveUrl = options.saveUrl || '/settings/username';
        this.redirectUrl = options.redirectUrl || '/profile/settings?tab=account';
        
        this.isAvailable = false;
        this.originalUsername = this.input?.dataset.original || '';
        
        this.init();
    }

    init() {
        if (!this.input || !this.checkBtn || !this.saveBtn) return;

        this.checkBtn.addEventListener('click', () => this.checkAvailability());
        this.saveBtn.addEventListener('click', () => this.saveUsername());
        this.input.addEventListener('input', () => this.resetState());
    }

    async checkAvailability() {
        const username = this.input.value.trim().toLowerCase();

        // Client-side validation
        if (!username || username.length < 3) {
            this.showStatus('error', 'Username must be at least 3 characters');
            return;
        }

        if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            this.showStatus('error', 'Only letters, numbers, and underscores allowed');
            return;
        }

        if (username === this.originalUsername) {
            this.showStatus('info', 'This is your current username');
            this.saveBtn.disabled = true;
            return;
        }

        // Server check
        this.setCheckingState(true);

        try {
            const response = await fetch(`${this.checkUrl}?username=${encodeURIComponent(username)}`);
            const data = await response.json();

            if (data.available) {
                this.showStatus('success', 'Username is available!');
                this.isAvailable = true;
                this.saveBtn.disabled = false;
            } else {
                this.showStatus('error', data.reason || 'Username is taken');
                this.isAvailable = false;
                this.saveBtn.disabled = true;
            }
        } catch (e) {
            this.showStatus('error', 'Failed to check username');
        }

        this.setCheckingState(false);
    }

    async saveUsername() {
        if (!this.isAvailable) return;

        const username = this.input.value.trim().toLowerCase();
        this.saveBtn.disabled = true;
        this.saveBtn.textContent = 'Saving...';

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            
            const response = await fetch(this.saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ username })
            });

            const data = await response.json();

            if (data.success) {
                this.showStatus('success', 'Username updated! Redirecting...');
                setTimeout(() => {
                    window.location.href = this.redirectUrl;
                }, 1000);
            } else {
                this.showStatus('error', data.message || 'Failed to update username');
                this.saveBtn.disabled = false;
            }
        } catch (e) {
            this.showStatus('error', 'Failed to save username');
            this.saveBtn.disabled = false;
        }

        this.saveBtn.textContent = 'Save Username';
    }

    resetState() {
        this.isAvailable = false;
        this.saveBtn.disabled = true;
        this.statusDiv?.classList.add('hidden');
    }

    setCheckingState(checking) {
        if (this.checkBtn) {
            this.checkBtn.disabled = checking;
            this.checkBtn.textContent = checking ? 'Checking...' : 'Check';
        }
    }

    showStatus(type, message) {
        if (!this.statusDiv || !this.statusIcon || !this.statusText) return;

        this.statusDiv.classList.remove('hidden');
        this.statusText.textContent = message;

        const configs = {
            success: { icon: '✓', iconClass: 'text-green-500', textClass: 'text-sm text-green-600' },
            error: { icon: '✗', iconClass: 'text-red-500', textClass: 'text-sm text-red-600' },
            info: { icon: 'ℹ', iconClass: 'text-blue-500', textClass: 'text-sm text-blue-600' },
        };

        const config = configs[type] || configs.info;
        this.statusIcon.innerHTML = config.icon;
        this.statusIcon.className = config.iconClass;
        this.statusText.className = config.textClass;
    }
}

// Auto-initialize if elements exist
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('username-input')) {
        new UsernameChecker();
    }
});
