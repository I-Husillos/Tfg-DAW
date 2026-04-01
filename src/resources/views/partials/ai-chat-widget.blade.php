@auth
<div
    id="ai-widget"
    data-ask-url="{{ route('ai.ask') }}"
    data-clear-url="{{ route('ai.clear') }}"
>
    {{-- Panel del chat --}}
    <div id="ai-widget-panel" class="ai-widget-panel" style="display:none;">
        <div class="ai-widget-header">
            <span><i class="fas fa-robot mr-2"></i> Asistente IA</span>
            <div class="d-flex align-items-center gap-2">
                <button id="ai-widget-clear" type="button" title="Limpiar chat">
                    <i class="fas fa-trash-alt"></i>
                </button>
                <button id="ai-widget-close" type="button" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div id="ai-widget-messages" class="ai-widget-messages">
            <p id="ai-widget-empty" class="ai-widget-empty">
                <i class="fas fa-comments fa-2x mb-2 d-block"></i>
                Escribe tu primera pregunta.
            </p>
        </div>

        <div id="ai-widget-error" class="ai-widget-error" style="display:none;"></div>

        <form id="ai-widget-form" class="ai-widget-form">
            <input
                id="ai-widget-input"
                type="text"
                name="message"
                maxlength="1500"
                placeholder="Escribe tu pregunta..."
                autocomplete="off"
                class="form-control"
            >
            <button id="ai-widget-send" type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

    {{-- Botón flotante --}}
    <button id="ai-widget-fab" type="button" class="ai-widget-fab" title="Asistente IA">
        <i id="ai-widget-fab-icon" class="fas fa-robot"></i>
    </button>
</div>
@endauth