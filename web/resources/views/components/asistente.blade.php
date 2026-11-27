
<style>
    #hegia-widget {
        --hb-primary: #15365e;
        --hb-blue: #2563a8;
        --hb-border: #dce5ef;
        font-family: 'Segoe UI', Arial, sans-serif;
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 9999;
        color: #24364b;
        line-height: 1.5;
        text-align: left;
    }

    #hegia-widget * {
        box-sizing: border-box;
    }

    #hegia-widget button {
        font-family: inherit;
        cursor: pointer;
    }

    #hegia-widget button:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    .hb-launcher {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-left: auto;
        border: 0;
        padding: 12px 20px 12px 13px;
        border-radius: 50px;
        background: linear-gradient(135deg, #15365e, #2477b5);
        color: white;
        box-shadow: 0 8px 25px #15365e40;
        transition: transform .2s;
    }

    .hb-launcher:hover {
        transform: translateY(-3px);
    }

    .hb-launcher strong {
        display: block;
        font-size: 15px;
        color: white;
    }

    .hb-launcher small {
        display: block;
        font-size: 10px;
        color: #dbeafe;
    }

    .hb-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #ffffff22;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .hb-avatar svg {
        width: 34px;
        height: 34px;
    }

    .hb-panel {
        display: none;
        flex-direction: column;
        width: 430px;
        height: 640px;
        max-height: calc(100dvh - 110px);
        margin-bottom: 14px;
        background: white;
        border: 1px solid var(--hb-border);
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 15px 55px #102a4540;
    }

    .hb-panel.hb-open {
        display: flex;
        animation: hb-appear .25s ease;
    }

    @keyframes hb-appear {
        from {
            opacity: 0;
            transform: translateY(12px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hb-header {
        background: linear-gradient(120deg, #15365e, #227bb1);
        color: white;
        padding: 17px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .hb-header .hb-avatar {
        width: 52px;
        height: 52px;
        border: 1px solid #ffffff55;
    }

    .hb-header .hb-avatar svg {
        width: 43px;
        height: 43px;
    }

    .hb-heading {
        flex: 1;
        min-width: 0;
    }

    .hb-heading h3 {
        color: white;
        margin: 0;
        font-size: 19px;
        font-weight: 700;
    }

    .hb-heading p {
        margin: 2px 0 5px;
        font-size: 11px;
        color: #dbeafe;
    }

    .hb-status {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 10px;
        color: #d1fae5;
    }

    .hb-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #4ade80;
        box-shadow: 0 0 8px #4ade8066;
    }

    .hb-header-btn {
        background: #ffffff15;
        border: 1px solid #ffffff44;
        color: white;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        font-size: 22px;
        display: grid;
        place-items: center;
    }

    .hb-header-btn:hover {
        background: #ffffff30;
    }

    .hb-security {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 11px 16px;
        background: #f8fafc;
        border-bottom: 1px solid var(--hb-border);
        color: #64748b;
        font-size: 11px;
        flex-shrink: 0;
    }

    .hb-security svg {
        color: #2563a8;
        flex-shrink: 0;
    }

    .hb-messages {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 18px 13px;
        background: #f0f4f9;
        scroll-behavior: smooth;
        scrollbar-width: thin;
        scrollbar-color: #b8c7d8 transparent;
    }

    .hb-date {
        text-align: center;
        color: #8291a4;
        font-size: 10px;
        margin-bottom: 18px;
    }

    .hb-message {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 16px;
        animation: hb-appear .2s ease;
    }

    .hb-message .hb-avatar {
        width: 30px;
        height: 30px;
        background: #dbeafe;
        border: 1px solid #c5d9f0;
    }

    .hb-message .hb-avatar svg {
        width: 25px;
        height: 25px;
    }

    .hb-bubble {
        background: white;
        border: 1px solid #dce5ef;
        border-radius: 3px 16px 16px 16px;
        padding: 12px 14px;
        max-width: calc(100% - 38px);
        box-shadow: 0 2px 6px #15365e06;
        min-width: 0;
    }

    .hb-name {
        display: block;
        font-size: 10px;
        font-weight: 700;
        color: #2563a8;
        margin-bottom: 6px;
    }

    .hb-content {
        font-size: 13px;
        line-height: 1.65;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .hb-content strong {
        font-weight: 700;
        color: #15365e;
    }

    .hb-user {
        justify-content: flex-end;
    }

    .hb-user .hb-bubble {
        background: linear-gradient(135deg, #1e568c, #247bb5);
        color: white;
        border: 0;
        border-radius: 16px 3px 16px 16px;
        max-width: 86%;
    }

    .hb-user .hb-name {
        color: #dbeafe;
    }

    .hb-time {
        font-size: 9px;
        color: #94a3b8;
        margin-top: 7px;
        text-align: right;
    }

    .hb-user .hb-time {
        color: #dbeafe;
    }

    .hb-quick {
        background: white;
        padding: 12px 14px;
        border-top: 1px solid var(--hb-border);
        flex-shrink: 0;
    }

    .hb-quick-title {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .7px;
        color: #8492a6;
        margin-bottom: 8px;
    }

    .hb-quick-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 7px;
    }

    .hb-quick-btn {
        display: flex;
        align-items: center;
        gap: 7px;
        text-align: left;
        border: 1px solid #dce5ef;
        border-radius: 10px;
        background: #f8fafc;
        padding: 9px;
        font-size: 11px;
        color: #334e6d;
        transition: .2s;
    }

    .hb-quick-btn svg {
        width: 16px;
        height: 16px;
        color: #2563a8;
        flex-shrink: 0;
    }

    .hb-quick-btn:hover {
        background: #eaf3ff;
        border-color: #93b9e6;
    }

    .hb-footer {
        padding: 12px 14px 9px;
        border-top: 1px solid var(--hb-border);
        background: white;
        flex-shrink: 0;
    }

    .hb-input-row {
        display: flex;
        align-items: flex-end;
        gap: 9px;
    }

    #hb-input {
        width: 100%;
        min-width: 0;
        min-height: 46px;
        max-height: 95px;
        padding: 12px;
        border: 1px solid #dce5ef;
        border-radius: 13px;
        background: #f8fafc;
        color: #24364b;
        font-family: inherit;
        font-size: 12px;
        resize: none;
        outline: none;
    }

    #hb-input:focus {
        border-color: #4386c5;
        box-shadow: 0 0 0 3px #2563a812;
    }

    .hb-send {
        width: 46px;
        height: 46px;
        border: 0;
        border-radius: 13px;
        background: linear-gradient(135deg, #15365e, #247bb5);
        color: white;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .hb-send:hover {
        filter: brightness(1.12);
    }

    .hb-footer-note {
        font-size: 9px;
        color: #94a3b8;
        text-align: center;
        margin-top: 9px;
    }

    .hb-typing {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 6px 0;
    }

    .hb-typing span {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #6095c8;
        animation: hb-bounce 1.2s infinite;
    }

    .hb-typing span:nth-child(2) {
        animation-delay: .15s;
    }

    .hb-typing span:nth-child(3) {
        animation-delay: .3s;
    }

    @keyframes hb-bounce {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: .5;
        }
        30% {
            transform: translateY(-5px);
            opacity: 1;
        }
    }

    @media (max-width: 500px) {
        #hegia-widget {
            right: 12px;
            bottom: 12px;
        }

        .hb-panel {
            width: calc(100vw - 24px);
            height: 650px;
            max-height: calc(100dvh - 95px);
            border-radius: 17px;
        }

        .hb-header {
            padding: 12px;
        }

        .hb-heading h3 {
            font-size: 17px;
        }
    }
</style>

<div id="hegia-widget">

    <section class="hb-panel" id="hb-panel"
             aria-label="Asistente HegiaBot">

        <header class="hb-header">

            <div class="hb-avatar" data-hb-robot></div>

            <div class="hb-heading">
                <h3>HegiaBot</h3>
                <p>Asistente inteligente de almacén</p>

                <div class="hb-status">
                    <span class="hb-dot"></span>
                    <span>Asistente de inventario</span>
                </div>
            </div>

            <button type="button"
                    class="hb-header-btn"
                    id="hb-close"
                    title="Minimizar asistente"
                    aria-label="Minimizar asistente">
                &minus;
            </button>

        </header>

        <div class="hb-security">

            <svg width="19" height="19" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.7">
                <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/>
                <path d="m9 12 2 2 4-4"/>
            </svg>

            <span>
                Consultas de inventario mediante MCP.
                Acceso de solo lectura.
            </span>

        </div>

        <div class="hb-messages" id="hb-messages"
             role="log" aria-live="polite">

            <div class="hb-date">
                Asistente virtual de Hegiapesa
            </div>

        </div>

        <div class="hb-quick">

            <div class="hb-quick-title">
                CONSULTAS RÁPIDAS
            </div>

            <div class="hb-quick-grid">

                <button type="button" class="hb-quick-btn"
                        data-prompt="¿Cuánto stock tenemos de "
                        data-fill="true">

                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.7">
                        <path d="m12 3 9 5-9 5-9-5 9-5z"/>
                        <path d="M3 8v9l9 5 9-5V8M12 13v9"/>
                    </svg>

                    Consultar stock
                </button>

                <button type="button" class="hb-quick-btn"
                        data-prompt="¿Dónde está ubicado "
                        data-fill="true">

                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.7">
                        <path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>

                    Localizar producto
                </button>

                <button type="button" class="hb-quick-btn"
                        data-prompt="¿Qué productos tienen stock bajo?">

                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.7">
                        <path d="M10.3 3.9 2.2 18a2 2 0 0 0 1.7 3h16.2a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>
                        <path d="M12 9v4M12 17h.01"/>
                    </svg>

                    Alertas de stock
                </button>

                <button type="button" class="hb-quick-btn"
                        data-prompt="Dame un resumen del inventario">

                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.7">
                        <path d="M3 3v18h18M8 16v-5M13 16V7M18 16V4"/>
                    </svg>

                    Ver resumen
                </button>

            </div>

        </div>

        <footer class="hb-footer">

            <div class="hb-input-row">

                <textarea id="hb-input"
                          rows="1"
                          maxlength="500"
                          aria-label="Escribe tu consulta"
                          placeholder="Escribe tu consulta aquí..."></textarea>

                <button type="button"
                        class="hb-send"
                        id="hb-send"
                        title="Enviar mensaje"
                        aria-label="Enviar mensaje">

                    <svg width="21" height="21" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="1.8"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="m22 2-7 20-4-9-9-4 20-7z"/>
                        <path d="M22 2 11 13"/>
                    </svg>

                </button>

            </div>

            <div class="hb-footer-note">
                HegiaBot &bull; Gemini + MCP &bull; Hegiapesa 2026
            </div>

        </footer>

    </section>

    <button type="button"
            class="hb-launcher"
            id="hb-launcher"
            aria-expanded="false"
            aria-controls="hb-panel">

        <div class="hb-avatar" data-hb-robot></div>

        <div>
            <strong>HegiaBot</strong>
            <small>¿Necesitas ayuda?</small>
        </div>

    </button>

</div>

<script>
(function () {

    const panel = document.getElementById('hb-panel');
    const launcher = document.getElementById('hb-launcher');
    const cerrar = document.getElementById('hb-close');
    const mensajes = document.getElementById('hb-messages');
    const input = document.getElementById('hb-input');
    const enviar = document.getElementById('hb-send');

    const botonesRapidos =
        document.querySelectorAll('#hegia-widget .hb-quick-btn');

    let procesando = false;

    const robotSVG = `
        <svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <path d="M32 7v10"
                  stroke="#60a5fa" stroke-width="3"
                  stroke-linecap="round"/>
            <circle cx="32" cy="7" r="4" fill="#60a5fa"/>
            <rect x="5" y="28" width="7" height="15"
                  rx="3" fill="#93c5fd"/>
            <rect x="52" y="28" width="7" height="15"
                  rx="3" fill="#93c5fd"/>
            <rect x="10" y="17" width="44" height="38"
                  rx="13" fill="#ffffff" stroke="#93c5fd"/>
            <rect x="15" y="24" width="34" height="22"
                  rx="8" fill="#15365e"/>
            <circle cx="24" cy="34" r="4" fill="#60d5fa"/>
            <circle cx="40" cy="34" r="4" fill="#60d5fa"/>
            <path d="M27 49q5 4 10 0"
                  stroke="#2563eb" stroke-width="2"
                  fill="none" stroke-linecap="round"/>
        </svg>
    `;

    document.querySelectorAll('[data-hb-robot]')
        .forEach(elemento => {
            elemento.innerHTML = robotSVG;
        });

    function bajarScroll() {
        mensajes.scrollTop = mensajes.scrollHeight;
    }

    function abrirChat() {
        panel.classList.add('hb-open');
        launcher.setAttribute('aria-expanded', 'true');
        bajarScroll();
        input.focus();
    }

    function cerrarChat() {
        panel.classList.remove('hb-open');
        launcher.setAttribute('aria-expanded', 'false');
    }

    launcher.addEventListener('click', function () {
        if (panel.classList.contains('hb-open')) {
            cerrarChat();
        } else {
            abrirChat();
        }
    });

    cerrar.addEventListener('click', cerrarChat);

    function horaActual() {
        return new Date().toLocaleTimeString('es-PE', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function mostrarTexto(elemento, texto) {

        elemento.replaceChildren();

        String(texto)
            .replace(/^\s*[-*]\s+/gm, '• ')
            .split(/(\*\*[^*]+\*\*)/g)
            .forEach(parte => {

                if (
                    parte.startsWith('**') &&
                    parte.endsWith('**')
                ) {
                    const negrita =
                        document.createElement('strong');

                    negrita.textContent =
                        parte.slice(2, -2);

                    elemento.appendChild(negrita);

                } else {

                    elemento.appendChild(
                        document.createTextNode(parte)
                    );

                }

            });
    }

    function agregarMensaje(texto, usuario = false) {

        const fila = document.createElement('div');

        fila.className =
            'hb-message' + (usuario ? ' hb-user' : '');

        if (!usuario) {

            const avatar = document.createElement('div');

            avatar.className = 'hb-avatar';
            avatar.innerHTML = robotSVG;

            fila.appendChild(avatar);
        }

        const burbuja = document.createElement('div');
        burbuja.className = 'hb-bubble';

        const nombre = document.createElement('span');
        nombre.className = 'hb-name';
        nombre.textContent = usuario ? 'Tú' : 'HegiaBot';

        const contenido = document.createElement('div');
        contenido.className = 'hb-content';

        if (usuario) {
            contenido.textContent = texto;
        } else {
            mostrarTexto(contenido, texto);
        }

        const hora = document.createElement('div');
        hora.className = 'hb-time';
        hora.textContent = horaActual();

        burbuja.append(nombre, contenido, hora);
        fila.appendChild(burbuja);
        mensajes.appendChild(fila);

        bajarScroll();

        return contenido;
    }

    agregarMensaje(
        '¡Hola! 👋 Soy **HegiaBot**, el asistente de inteligencia artificial de Hegiapesa.\n\n' +
        'Puedo ayudarte a consultar existencias, localizar productos y revisar alertas de inventario.\n\n' +
        '¿En qué puedo ayudarte hoy?'
    );

    function bloquearControles(estado) {

        procesando = estado;
        enviar.disabled = estado;

        botonesRapidos.forEach(boton => {
            boton.disabled = estado;
        });
    }

    async function enviarMensaje(texto) {

        texto = String(texto || '').trim();

        if (!texto || procesando) return;

        if (texto.length > 500) {
            agregarMensaje(
                'La consulta no debe superar los 500 caracteres.'
            );
            return;
        }

        agregarMensaje(texto, true);

        input.value = '';
        bloquearControles(true);

        const espera = agregarMensaje('');

        const animacion = document.createElement('div');
        animacion.className = 'hb-typing';

        for (let i = 0; i < 3; i++) {
            animacion.appendChild(
                document.createElement('span')
            );
        }

        espera.appendChild(animacion);

        const etiqueta = document.createElement('small');

        etiqueta.textContent = 'Procesando tu consulta...';
        etiqueta.style.color = '#8492a6';

        espera.appendChild(etiqueta);

        bajarScroll();

        const controlador = new AbortController();

        const temporizador = setTimeout(
            () => controlador.abort(),
            65000
        );

        try {

            const csrf = document.querySelector(
                'meta[name="csrf-token"]'
            );

            const respuesta = await fetch(
                "{{ route('asistente.chat') }}",
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                            ? csrf.content
                            : "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        mensaje: texto
                    }),
                    signal: controlador.signal
                }
            );

            if (
                respuesta.status === 401 ||
                respuesta.status === 419
            ) {
                mostrarTexto(
                    espera,
                    'Tu sesión ha expirado. Actualiza la página e inicia sesión nuevamente.'
                );
                return;
            }

            if (respuesta.status === 429) {
                mostrarTexto(
                    espera,
                    'Has realizado varias consultas. Espera un momento antes de continuar.'
                );
                return;
            }

            if (!respuesta.ok) {
                throw new Error('Error del servidor');
            }

            const datos = await respuesta.json();

            mostrarTexto(
                espera,
                datos.respuesta ||
                'No se obtuvo una respuesta.'
            );

        } catch (error) {

            mostrarTexto(
                espera,
                error.name === 'AbortError'
                    ? 'La consulta está tardando demasiado. Intenta nuevamente en unos momentos.'
                    : 'No pude completar la consulta. Verifica que el asistente esté disponible e intenta nuevamente.'
            );

        } finally {

            clearTimeout(temporizador);
            bloquearControles(false);
            bajarScroll();

            if (panel.classList.contains('hb-open')) {
                input.focus();
            }

        }
    }

    enviar.addEventListener('click', function () {
        enviarMensaje(input.value);
    });

    input.addEventListener('keydown', function (evento) {

        if (
            evento.key === 'Enter' &&
            !evento.shiftKey &&
            !evento.isComposing
        ) {
            evento.preventDefault();
            enviarMensaje(input.value);
        }

    });

    botonesRapidos.forEach(boton => {

        boton.addEventListener('click', function () {

            const pregunta = boton.dataset.prompt;

            if (boton.dataset.fill === 'true') {

                input.value = pregunta;
                input.focus();

                input.setSelectionRange(
                    input.value.length,
                    input.value.length
                );

            } else {

                enviarMensaje(pregunta);

            }

        });

    });

})();
</script>
