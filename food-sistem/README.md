# 🍔 Food Ordering System - Laravel Style

Sistema completo de pedidos de comida con arquitectura inspirada en Laravel, chatbot mejorado con NLP y frontend moderno en React + TypeScript.

## 🌟 Características Principales

### Backend (Laravel-Style Node.js)
- ✅ Arquitectura MVC inspirada en Laravel
- ✅ API RESTful con Express.js
- ✅ ORM Sequelize para MySQL
- ✅ Autenticación JWT
- ✅ Controladores, Modelos, Middleware organizados
- ✅ Sistema de seeders y migraciones

### Chatbot Mejorado con IA
- 🤖 Procesamiento de Lenguaje Natural (NLP)
- 🧠 Clasificación de intenciones con Naive Bayes
- 🔍 Extracción de entidades (comida, precios, números)
- 💭 Memoria contextual de conversación
- 😊 Análisis de sentimientos
- 💡 Sugerencias dinámicas e inteligentes
- 📊 Respuestas basadas en datos reales
- 🎯 Recomendaciones personalizadas

### Frontend (React + TypeScript)
- ⚡ Vite + React 18
- 🎨 TailwindCSS para UI moderna
- 📱 Diseño responsive
- 🔄 Context API para gestión de estado
- 🛒 Carrito de compras funcional
- 🔐 Sistema de autenticación completo

## 📁 Estructura del Proyecto

```
/
├── backend/                    # Backend Laravel-style
│   ├── app/
│   │   ├── Controllers/       # Controladores
│   │   ├── Models/           # Modelos Sequelize
│   │   ├── Middleware/       # Middleware
│   │   └── Services/         # Servicios (ChatBot)
│   ├── config/               # Configuraciones
│   ├── database/
│   │   └── seeders/         # Seeders
│   ├── routes/              # Rutas API
│   └── server.js            # Servidor principal
│
├── src/                      # Frontend React
│   ├── components/          # Componentes React
│   ├── contexts/           # Context API
│   ├── services/           # API Service
│   └── types/              # TypeScript types
│
└── package.json
```

## 📚 Documentación Completa

- **[START_PROJECT.md](START_PROJECT.md)** - 🚀 **Inicio rápido en 5 minutos**
- **[PROYECTO_COMPLETADO.md](PROYECTO_COMPLETADO.md)** - ✨ Resumen completo de la transformación
- **[INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)** - 📖 Guía detallada de instalación paso a paso
- **[LARAVEL_STRUCTURE.md](LARAVEL_STRUCTURE.md)** - 🏗️ Explicación de la estructura Laravel en Node.js
- **[CHATBOT_IMPROVEMENTS.md](CHATBOT_IMPROVEMENTS.md)** - 🤖 Detalles de las mejoras del chatbot con IA
- **[Backend README](backend/README.md)** - 🔧 Documentación específica del backend

## 🚀 Instalación Rápida

### Prerequisitos
- Node.js 18+
- MySQL 8+
- npm o yarn

### Instalación en 5 minutos

```bash
# 1. Instalar dependencias del frontend
npm install

# 2. Instalar dependencias del backend
cd backend
npm install
cd ..

# 3. Configurar base de datos MySQL
mysql -u root -p
CREATE DATABASE food_ordering;
EXIT;

# 4. Configurar variables de entorno
cd backend
cp .env.example .env
# Editar backend/.env con tus credenciales MySQL
cd ..
cp .env.example .env

# 5. Poblar base de datos
cd backend
npm run seed
```

**📖 Para instrucciones detalladas, ver [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md)**

### Iniciar Aplicación

**Terminal 1 - Backend:**
```bash
cd backend
npm run dev
```
✅ Servidor en `http://localhost:3000`

**Terminal 2 - Frontend:**
```bash
npm run dev
```
✅ App en `http://localhost:5173`

## 🔑 Credenciales de Prueba

```
Email: user@example.com
Password: user123
```

## 🤖 Características del ChatBot Mejorado

### Inteligencia Artificial
- **Clasificador Bayesiano**: Entrena con ejemplos para identificar intenciones
- **NLP con Natural.js**: Procesamiento avanzado del lenguaje
- **Compromise.js**: Análisis gramatical y extracción de entidades
- **Contexto Persistente**: Recuerda conversaciones previas
- **Respuestas Dinámicas**: Basadas en datos reales de la BD

### Intents Soportados
1. **Saludos** - "hola", "buenos días"
2. **Horarios** - "¿a qué hora abren?"
3. **Entrega** - "cuánto tarda el delivery"
4. **Pagos** - "métodos de pago", "puedo pagar con tarjeta"
5. **Menú** - "quiero pizza", "qué comida tienen"
6. **Estado de Pedido** - "dónde está mi pedido"
7. **Recomendaciones** - "qué me recomiendas"
8. **Ayuda** - "ayuda", "qué puedes hacer"
9. **Cancelar** - "cancelar pedido"

### Ejemplo de Conversación

```
Usuario: "Hola, quiero pizza"
Bot: 🍽️ Encontré 3 opciones de pizza para ti. Te muestro los mejores:
     [Muestra lista de pizzas con precios]
     Sugerencias: [Margherita] [Pepperoni] [Ver más opciones]

Usuario: "Margherita"
Bot: ¡Excelente elección! La Pizza Margherita ($12.99) de Pizza Paradise...
```

### Mejoras sobre la versión anterior
- ✅ Clasificación de intenciones con ML
- ✅ Extracción de entidades (comida, precios)
- ✅ Memoria de contexto de conversación
- ✅ Integración con base de datos
- ✅ Sugerencias dinámicas con botones
- ✅ Análisis de sentimientos
- ✅ Respuestas personalizadas por usuario
- ✅ UI mejorada con gradientes y animaciones

## 📡 API Endpoints

### Autenticación
```
POST   /api/auth/register     - Registrar usuario
POST   /api/auth/login        - Login
POST   /api/auth/logout       - Logout
GET    /api/auth/me           - Usuario actual
```

### Restaurantes
```
GET    /api/restaurants              - Listar restaurantes
GET    /api/restaurants/:id          - Obtener restaurante
GET    /api/restaurants/:id/menu     - Menú del restaurante
GET    /api/restaurants/categories   - Categorías
```

### Pedidos (Autenticados)
```
POST   /api/orders              - Crear pedido
GET    /api/orders              - Listar pedidos
GET    /api/orders/active       - Pedidos activos
GET    /api/orders/:id          - Obtener pedido
PATCH  /api/orders/:id/status   - Actualizar estado
```

### ChatBot
```
POST   /api/chatbot/message                - Enviar mensaje
GET    /api/chatbot/history/:session_id    - Historial
POST   /api/chatbot/clear                  - Limpiar contexto
```

## 🗄️ Modelos de Base de Datos

### User
- Autenticación con bcrypt
- Roles: customer, restaurant_owner, admin
- Relaciones: orders, chatMessages, ownedRestaurants

### Restaurant
- Información completa del restaurante
- Rating, tiempo de entrega, categoría
- Relaciones: menuItems, orders

### MenuItem
- Menú con precios e ingredientes
- Sistema de disponibilidad
- Categorización de platillos

### Order
- Sistema completo de pedidos
- Estados: pending, confirmed, preparing, on_way, delivered, cancelled
- Tracking de pago y entrega

### ChatMessage
- Almacena conversaciones
- Intent y entities para análisis
- Contexto de conversación

## 🎨 Stack Tecnológico

### Backend
- **Express.js** - Framework web
- **Sequelize** - ORM para MySQL
- **JWT** - Autenticación
- **Natural** - NLP y ML
- **Compromise** - Análisis de texto
- **Bcrypt** - Hash de contraseñas

### Frontend
- **React 18** - UI Library
- **TypeScript** - Tipado estático
- **Vite** - Build tool
- **TailwindCSS** - Estilos
- **Lucide React** - Iconos
- **Context API** - Estado global

## 🔐 Seguridad

- ✅ Autenticación JWT con tokens seguros
- ✅ Hash de contraseñas con bcrypt
- ✅ Validación de entrada
- ✅ Middleware de autenticación
- ✅ CORS configurado
- ✅ Variables de entorno para secretos

## 🚀 Producción

### Backend
1. Configurar variables de entorno de producción
2. Usar base de datos segura
3. Implementar rate limiting
4. Configurar HTTPS
5. Logging apropiado

### Frontend
```bash
npm run build
```

## 📈 Próximas Mejoras

- [ ] Integración con OpenAI GPT para chatbot más avanzado
- [ ] Sistema de notificaciones en tiempo real (WebSocket)
- [ ] Pagos integrados (Stripe/PayPal)
- [ ] Panel de administración
- [ ] Sistema de reviews y ratings
- [ ] Geolocalización para delivery
- [ ] PWA (Progressive Web App)
- [ ] Tests unitarios y e2e

## 🤝 Contribuir

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📝 Licencia

MIT License - ver archivo LICENSE para más detalles

## 👨‍💻 Autor

Desarrollado con ❤️ usando mejores prácticas de Laravel y tecnologías modernas

---

¿Preguntas? Abre un issue o contacta al equipo de desarrollo.
