<?php

class AuthController {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../models/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'avatar' => $user['avatar']
                    ];
                    $_SESSION['flash'] = "¡Bienvenido de nuevo, {$user['name']}!";
                    
                    header('Location: /');
                    exit;
                } else {
                    $error = "Email o contraseña incorrectos";
                }
            } catch (PDOException $e) {
                $error = "Error al intentar iniciar sesión";
            }
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $errors = [];
            
            if (empty($name)) {
                $errors[] = "El nombre es requerido";
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email inválido";
            }
            
            if (strlen($password) < 6) {
                $errors[] = "La contraseña debe tener al menos 6 caracteres";
            }
            
            if ($password !== $confirmPassword) {
                $errors[] = "Las contraseñas no coinciden";
            }

            if (empty($errors)) {
                try {
                    // Verificar si el email ya existe
                    $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    if ($stmt->fetch()) {
                        $errors[] = "Este email ya está registrado";
                    } else {
                        // Crear el usuario
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $this->db->prepare("
                            INSERT INTO users (name, email, password) 
                            VALUES (?, ?, ?)
                        ");
                        $stmt->execute([$name, $email, $hashedPassword]);
                        
                        // Iniciar sesión automáticamente
                        $userId = $this->db->lastInsertId();
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['user'] = [
                            'id' => $userId,
                            'name' => $name,
                            'email' => $email,
                            'avatar' => 'default-avatar.png'
                        ];
                        $_SESSION['flash'] = "¡Bienvenido a Food System, {$name}!";
                        
                        header('Location: /');
                        exit;
                    }
                } catch (PDOException $e) {
                    $errors[] = "Error al crear la cuenta";
                }
            }
        }

        require __DIR__ . '/../views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }
}