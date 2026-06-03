/**
 * SIMGURU Client-Side Forms & Toast System Helpers
 */

// Global Toast Manager
window.SimguruToast = {
    show(type, message) {
        // Translate theme shorthand
        if (type === 'error') type = 'danger';
        
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast-item toast-${type}`;
        
        let iconClass = 'ti-circle-check';
        if (type === 'danger') iconClass = 'ti-circle-x';
        else if (type === 'warning') iconClass = 'ti-alert-triangle';
        else if (type === 'info') iconClass = 'ti-info-circle';

        toast.innerHTML = `
            <div class="toast-content">
                <i class="toast-icon ti ${iconClass}"></i>
                <div class="toast-body">
                    <span class="toast-message">${message}</span>
                </div>
                <button type="button" class="toast-close" aria-label="Close Toast">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="toast-progress-bar">
                <div class="toast-progress-fill" style="transform: scaleX(1);"></div>
            </div>
        `;

        container.appendChild(toast);

        // Auto Close Click
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', () => {
            toast.style.transition = 'opacity 0.25s ease-out, transform 0.25s ease-out';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 250);
        });

        // Progress Fill Animation (4 seconds)
        const duration = 4000;
        const progressFill = toast.querySelector('.toast-progress-fill');
        const start = performance.now();
        let animationFrameId;

        function animateProgress(timestamp) {
            const elapsed = timestamp - start;
            const progress = Math.max(0, 1 - (elapsed / duration));
            progressFill.style.transform = `scaleX(${progress})`;

            if (elapsed < duration) {
                animationFrameId = requestAnimationFrame(animateProgress);
            } else {
                dismissToast();
            }
        }

        function dismissToast() {
            toast.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(50px)';
            setTimeout(() => {
                toast.remove();
            }, 400);
        }

        animationFrameId = requestAnimationFrame(animateProgress);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // 1. NIP Auto-Formatting Field (numeric only, max 18 digits)
    const nipInputs = document.querySelectorAll('input[name="nik"]');
    nipInputs.forEach(input => {
        // Enforce rules on keydown/input
        input.addEventListener('input', (e) => {
            let value = e.target.value;
            // Strip any non-digit chars
            let cleaned = value.replace(/\D/g, '');
            // Slice to 18 digits limit
            if (cleaned.length > 18) {
                cleaned = cleaned.substring(0, 18);
            }
            e.target.value = cleaned;
        });

        // Prevent copying non-numeric data into the field
        input.addEventListener('paste', (e) => {
            const pasteData = (e.clipboardData || window.clipboardData).getData('text');
            if (/\D/.test(pasteData)) {
                e.preventDefault();
                let cleaned = pasteData.replace(/\D/g, '').substring(0, 18);
                document.execCommand('insertText', false, cleaned);
            }
        });
    });
});
