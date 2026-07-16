<style>
.hallsync-field-invalid {
    border-color: #d47b66 !important;
    box-shadow: 0 0 0 3px rgba(212, 123, 102, 0.14) !important;
}
.hallsync-field-invalid:focus {
    outline: 2px solid rgba(212, 123, 102, 0.34) !important;
    outline-offset: 1px;
}
.hallsync-field-invalid-container {
    border-color: #d47b66 !important;
    box-shadow: 0 0 0 3px rgba(212, 123, 102, 0.14) !important;
}
.hallsync-validation-bubble {
    position: absolute;
    z-index: 10000;
    display: flex;
    align-items: center;
    gap: 8px;
    width: max-content;
    max-width: min(320px, calc(100vw - 24px));
    padding: 9px 11px;
    border: 1px solid #d8d1c8;
    border-radius: 5px;
    background: #ffffff;
    box-shadow: 0 4px 12px rgba(48, 40, 32, 0.18);
    color: #3e372f;
    font-family: 'Inter', sans-serif;
    font-size: 0.76rem;
    font-weight: 500;
    line-height: 1.35;
    pointer-events: none;
}
.hallsync-validation-bubble[hidden] {
    display: none;
}
.hallsync-validation-bubble::before {
    position: absolute;
    top: -7px;
    left: 17px;
    width: 12px;
    height: 12px;
    border-top: 1px solid #d8d1c8;
    border-left: 1px solid #d8d1c8;
    background: #ffffff;
    content: '';
    transform: rotate(45deg);
}
.hallsync-validation-bubble-icon {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    flex: 0 0 auto;
    border-radius: 2px;
    background: #eea928;
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 800;
}
</style>
<script>
    (() => {
        const bubble = document.createElement('div');
        const icon = document.createElement('span');
        const message = document.createElement('span');
        let activeField = null;

        bubble.className = 'hallsync-validation-bubble';
        bubble.hidden = true;
        bubble.setAttribute('role', 'alert');
        icon.className = 'hallsync-validation-bubble-icon';
        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = '!';
        bubble.append(icon, message);
        document.body.append(bubble);

        const isFormField = (field) => field instanceof HTMLInputElement
            || field instanceof HTMLSelectElement
            || field instanceof HTMLTextAreaElement;

        const validationMessage = (field) => {
            if (field.validity.valueMissing) {
                return 'Please fill out this field.';
            }

            if (field.validity.typeMismatch && field.type === 'email') {
                return 'Please enter a valid email address.';
            }

            if (field.validity.tooShort) {
                return `Please use at least ${field.minLength} characters.`;
            }

            if (field.validity.tooLong) {
                return `Please use no more than ${field.maxLength} characters.`;
            }

            if (field.validity.patternMismatch) {
                return 'Please match the requested format.';
            }

            return field.validationMessage || 'Please check this field.';
        };

        const anchorFor = (field) => {
            const fieldRect = field.getBoundingClientRect();

            if (fieldRect.width > 0 && fieldRect.height > 0) {
                return field;
            }

            return field.closest('label') || field.parentElement || field;
        };

        const clearInvalidState = (field) => {
            const relatedFields = field.type === 'radio' && field.form && field.name
                ? [...field.form.elements].filter((candidate) => candidate.name === field.name)
                : [field];

            relatedFields.forEach((candidate) => {
                candidate.classList.remove('hallsync-field-invalid');
                anchorFor(candidate).classList.remove('hallsync-field-invalid-container');
                candidate.removeAttribute('aria-invalid');
            });
        };

        const positionBubble = () => {
            if (!activeField || bubble.hidden) {
                return;
            }

            const fieldRect = anchorFor(activeField).getBoundingClientRect();
            const bubbleRect = bubble.getBoundingClientRect();
            const left = Math.min(
                Math.max(window.scrollX + fieldRect.left, window.scrollX + 12),
                window.scrollX + window.innerWidth - bubbleRect.width - 12
            );

            bubble.style.left = `${left}px`;
            bubble.style.top = `${window.scrollY + fieldRect.bottom + 8}px`;
        };

        const showBubble = (field) => {
            activeField = field;
            message.textContent = validationMessage(field);
            bubble.hidden = false;
            positionBubble();
        };

        const hideBubble = (field = null) => {
            if (field && activeField !== field) {
                return;
            }

            bubble.hidden = true;
            activeField = null;
        };

        document.addEventListener('invalid', (event) => {
            const field = event.target;

            if (!isFormField(field)) {
                return;
            }

            event.preventDefault();
            field.classList.add('hallsync-field-invalid');
            anchorFor(field).classList.add('hallsync-field-invalid-container');
            field.setAttribute('aria-invalid', 'true');

            if (!activeField || activeField === field) {
                showBubble(field);
                window.setTimeout(() => field.focus(), 0);
            }
        }, true);

        const refreshField = (event) => {
            const field = event.target;

            if (!isFormField(field)) {
                return;
            }

            if (field.validity.valid) {
                clearInvalidState(field);
                hideBubble(field);
                return;
            }

            if (activeField === field) {
                showBubble(field);
            }
        };

        document.addEventListener('input', refreshField, true);
        document.addEventListener('change', refreshField, true);
        document.addEventListener('focusin', (event) => {
            if (activeField && activeField !== event.target) {
                hideBubble();
            }
        });
        window.addEventListener('resize', positionBubble);
        window.addEventListener('scroll', positionBubble, true);
    })();
</script>
