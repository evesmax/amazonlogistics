<?php
ini_set('session.cookie_httponly',1);
?>
<html>
    <body>
        <script type="text/javascript">
            document.location = "netwarelog/accelog/index.php";
        </script>
    </body>
</html>

<script>
(function() {
    const PROJECT_ID = '8861bfc2-d039-448d-bc0d-9493decf2812';
    const IFRAME_URL = `https://qssintelligence.replit.app/embed/${PROJECT_ID}`;

    // 1. Estilos del Botón y del Iframe
    const style = document.createElement('style');
    style.innerHTML = `
        #gemini-chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #2563eb;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9998;
            transition: transform 0.2s;
        }
        #gemini-chat-button:hover {
            transform: scale(1.05);
        }
        #gemini-chat-button svg {
            width: 32px;
            height: 32px;
            fill: white;
        }
        #gemini-chat-iframe {
            position: fixed;
            bottom: 100px;
            right: 20px;
            width: 350px;
            height: 500px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            display: none;
            z-index: 9999;
        }
    `;
    document.head.appendChild(style);

    // 2. Crear el Iframe (oculto)
    const iframe = document.createElement('iframe');
    iframe.id = 'gemini-chat-iframe';
    iframe.src = IFRAME_URL;
    document.body.appendChild(iframe);

    // 3. Crear el Botón flotante
    const button = document.createElement('div');
    button.id = 'gemini-chat-button';
    button.innerHTML = '<svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>';
    document.body.appendChild(button);

    // 4. Lógica de Toggle
    button.addEventListener('click', () => {
        const chatIframe = document.getElementById('gemini-chat-iframe');
        chatIframe.style.display = (chatIframe.style.display === 'none' || chatIframe.style.display === '') ? 'block' : 'none';
    });
})();
</script>
