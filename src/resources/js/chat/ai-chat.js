export function initAiChat() {
    const widget = document.getElementById('ai-widget');
    if (!widget) return;

    const askUrl   = widget.dataset.askUrl;
    const clearUrl = widget.dataset.clearUrl;

    const panel    = document.getElementById('ai-widget-panel');
    const fab      = document.getElementById('ai-widget-fab');
    const fabIcon  = document.getElementById('ai-widget-fab-icon');
    const messages = document.getElementById('ai-widget-messages');
    const form     = document.getElementById('ai-widget-form');
    const input    = document.getElementById('ai-widget-input');
    const sendBtn  = document.getElementById('ai-widget-send');
    const clearBtn = document.getElementById('ai-widget-clear');
    const closeBtn = document.getElementById('ai-widget-close');
    const errorEl  = document.getElementById('ai-widget-error');

    let isOpen = false;

    function togglePanel() {
        isOpen = !isOpen;
        panel.style.display = isOpen ? 'flex' : 'none';
        fabIcon.className   = isOpen ? 'fas fa-times' : 'fas fa-robot';
        if (isOpen) {
            scrollToBottom();
            input.focus();
        }
    }

    function addBubble(role, content) {
        const empty = document.getElementById('ai-widget-empty');
        if (empty) empty.remove();

        const wrapper = document.createElement('div');
        wrapper.className = `ai-widget-bubble-wrapper ${role}`;

        const bubble = document.createElement('div');
        bubble.className = 'ai-widget-bubble';
        bubble.textContent = content;

        wrapper.appendChild(bubble);
        messages.appendChild(wrapper);
        scrollToBottom();

        return bubble;
    }

    function addTyping() {
        const wrapper = document.createElement('div');
        wrapper.className = 'ai-widget-bubble-wrapper assistant ai-widget-typing';
        wrapper.id = 'ai-widget-typing';

        const bubble = document.createElement('div');
        bubble.className = 'ai-widget-bubble';
        bubble.textContent = 'Escribiendo...';

        wrapper.appendChild(bubble);
        messages.appendChild(wrapper);
        scrollToBottom();
    }

    function removeTyping() {
        const typing = document.getElementById('ai-widget-typing');
        if (typing) typing.remove();
    }

    function setError(message = '') {
        errorEl.textContent = message;
        errorEl.style.display = message ? 'block' : 'none';
    }

    function setLoading(state) {
        sendBtn.disabled = state;
        input.disabled   = state;
        sendBtn.innerHTML = state
            ? '<span class="spinner-border spinner-border-sm"></span>'
            : '<i class="fas fa-paper-plane"></i>';
    }

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    fab.addEventListener('click', togglePanel);
    closeBtn.addEventListener('click', togglePanel);

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const message = input.value.trim();
        if (!message) return;

        setError();
        setLoading(true);
        addBubble('user', message);
        addTyping();
        input.value = '';

        $.ajax({
            url: askUrl,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ message }),
            success: function (data) {
                removeTyping();
                if (data.ok) {
                    addBubble('assistant', data.response);
                } else {
                    setError(data.message || 'Error al consultar la IA.');
                }
            },
            error: function (xhr) {
                removeTyping();
                const data = xhr.responseJSON;
                setError(data?.message || 'Error de conexión. ¿Está Ollama activo?');
            },
            complete: function () {
                setLoading(false);
                input.focus();
            }
        });
    });

    clearBtn.addEventListener('click', function () {
        $.ajax({
            url: clearUrl,
            method: 'POST',
            success: function (data) {
                if (data.ok) {
                    messages.innerHTML = '<p id="ai-widget-empty" class="ai-widget-empty"><i class="fas fa-comments fa-2x mb-2 d-block"></i>Escribe tu primera pregunta.</p>';
                    setError();
                }
            },
            error: function () {
                setError('No se pudo limpiar el chat.');
            }
        });
    });
}