class Chatbot {
    constructor() {
        this.chatContainer = null;
        this.messagesList = null;
        this.inputField = null;
        this.sendButton = null;
        this.toggleButton = null;
        this.isOpen = false;
        
        this.init();
    }

    init() {
        this.createChatbotUI();
        this.attachEventListeners();
    }

    createChatbotUI() {
        // Crear el botón de alternancia del chatbot
        this.toggleButton = document.createElement('button');
        this.toggleButton.innerHTML = '💬';
        this.toggleButton.className = 'fixed bottom-4 right-4 w-12 h-12 bg-blue-500 text-white rounded-full shadow-lg hover:bg-blue-600 flex items-center justify-center text-2xl z-50';
        document.body.appendChild(this.toggleButton);

        // Crear el contenedor principal del chatbot
        this.chatContainer = document.createElement('div');
        this.chatContainer.className = 'fixed bottom-20 right-4 w-80 bg-white rounded-lg shadow-xl z-50 transition-all duration-300 transform translate-y-full opacity-0';
        
        // Encabezado del chat
        const header = document.createElement('div');
        header.className = 'bg-blue-500 text-white p-4 rounded-t-lg flex justify-between items-center';
        header.innerHTML = `
            <h3 class="font-semibold">Asistente Virtual</h3>
            <button class="text-white hover:text-gray-200">×</button>
        `;
        
        // Lista de mensajes
        this.messagesList = document.createElement('div');
        this.messagesList.className = 'h-80 overflow-y-auto p-4 space-y-4';
        
        // Área de entrada de mensaje
        const inputArea = document.createElement('div');
        inputArea.className = 'p-4 border-t';
        
        this.inputField = document.createElement('input');
        this.inputField.type = 'text';
        this.inputField.placeholder = 'Escribe tu mensaje...';
        this.inputField.className = 'w-full p-2 border rounded-l focus:outline-none focus:border-blue-500';
        
        this.sendButton = document.createElement('button');
        this.sendButton.innerHTML = '➤';
        this.sendButton.className = 'bg-blue-500 text-white px-4 py-2 rounded-r hover:bg-blue-600';
        
        const inputWrapper = document.createElement('div');
        inputWrapper.className = 'flex';
        inputWrapper.appendChild(this.inputField);
        inputWrapper.appendChild(this.sendButton);
        
        inputArea.appendChild(inputWrapper);
        
        // Ensamblar componentes
        this.chatContainer.appendChild(header);
        this.chatContainer.appendChild(this.messagesList);
        this.chatContainer.appendChild(inputArea);
        
        document.body.appendChild(this.chatContainer);

        // Mensaje de bienvenida
        this.addMessage('¡Hola! Soy tu asistente virtual. ¿En qué puedo ayudarte hoy? 😊', 'bot');
    }

    attachEventListeners() {
        // Alternar visibilidad del chat
        this.toggleButton.addEventListener('click', () => this.toggleChat());
        
        // Enviar mensaje
        this.sendButton.addEventListener('click', () => this.sendMessage());
        
        // Enviar mensaje con Enter
        this.inputField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        if (this.isOpen) {
            this.chatContainer.classList.remove('translate-y-full', 'opacity-0');
            this.inputField.focus();
        } else {
            this.chatContainer.classList.add('translate-y-full', 'opacity-0');
        }
    }

    async sendMessage() {
        const message = this.inputField.value.trim();
        if (message) {
            // Mostrar el mensaje del usuario
            this.addMessage(message, 'user');
            this.inputField.value = '';

            try {
                // Enviar mensaje al servidor
                const response = await fetch('/chatbot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message })
                });

                const data = await response.json();
                
                // Mostrar respuesta del bot
                this.addMessage(data.response, 'bot');
            } catch (error) {
                console.error('Error al enviar mensaje:', error);
                this.addMessage('Lo siento, ha ocurrido un error. Por favor, intenta de nuevo más tarde.', 'bot');
            }
        }
    }

    addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;
        
        const message = document.createElement('div');
        message.className = `max-w-xs p-3 rounded-lg ${
            sender === 'user' 
                ? 'bg-blue-500 text-white rounded-br-none' 
                : 'bg-gray-200 text-gray-800 rounded-bl-none'
        }`;
        message.textContent = text;
        
        messageDiv.appendChild(message);
        this.messagesList.appendChild(messageDiv);
        this.messagesList.scrollTop = this.messagesList.scrollHeight;
    }
}