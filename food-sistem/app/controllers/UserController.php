<?php

class UserController {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../models/Database.php';
        $this->db = Database::getInstance()->getConnection();
    }

    public function profile() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash'] = 'Debes iniciar sesión para ver tu perfil';
            header('Location: /');
            exit;
        }

        $user = null;
        $orders = [];

        try {
            // Obtener información del usuario
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception('Usuario no encontrado');
            }

            // Obtener pedidos recientes
            $stmt = $this->db->prepare("
                SELECT o.*, r.name as restaurant_name
                FROM orders o
                JOIN restaurants r ON o.restaurant_id = r.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $orders = $stmt->fetchAll();

            // Si hay un POST, actualizar el perfil
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $address = $_POST['address'] ?? '';
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                $updates = [];
                $params = [];

                if ($name && $name !== $user['name']) {
                    $updates[] = "name = ?";
                    $params[] = $name;
                }

                if ($email && $email !== $user['email']) {
                    // Verificar que el email no esté en uso
                    $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                    $stmt->execute([$email, $user['id']]);
                    if ($stmt->fetch()) {
                        throw new Exception('El email ya está en uso');
                    }
                    $updates[] = "email = ?";
                    $params[] = $email;
                }

                if ($phone !== $user['phone']) {
                    $updates[] = "phone = ?";
                    $params[] = $phone;
                }

                if ($address !== $user['address']) {
                    $updates[] = "address = ?";
                    $params[] = $address;
                }

                // Cambio de contraseña
                if ($currentPassword && $newPassword) {
                    if (!password_verify($currentPassword, $user['password'])) {
                        throw new Exception('La contraseña actual es incorrecta');
                    }

                    if (strlen($newPassword) < 6) {
                        throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
                    }

                    if ($newPassword !== $confirmPassword) {
                        throw new Exception('Las contraseñas no coinciden');
                    }

                    $updates[] = "password = ?";
                    $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
                }

                // Procesar imagen de perfil
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                    if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
                        throw new Exception('Tipo de archivo no permitido');
                    }

                    $maxSize = 5 * 1024 * 1024; // 5MB
                    if ($_FILES['avatar']['size'] > $maxSize) {
                        throw new Exception('La imagen es demasiado grande');
                    }

                    $filename = uniqid() . '_' . $_FILES['avatar']['name'];
                    $uploadPath = __DIR__ . '/../../public/uploads/avatars/' . $filename;

                    if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                        throw new Exception('Error al subir la imagen');
                    }

                    $updates[] = "avatar = ?";
                    $params[] = $filename;

                    // Eliminar avatar anterior si no es el default
                    if ($user['avatar'] !== 'default-avatar.png') {
                        $oldAvatar = __DIR__ . '/../../public/uploads/avatars/' . $user['avatar'];
                        if (file_exists($oldAvatar)) {
                            unlink($oldAvatar);
                        }
                    }
                }

                if (!empty($updates)) {
                    $params[] = $user['id'];
                    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute($params);

                    $_SESSION['flash'] = 'Perfil actualizado correctamente';
                    header('Location: /profile');
                    exit;
                }
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        require __DIR__ . '/../views/users/profile.php';
    }
}