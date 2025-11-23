<?php

class UserController extends Controller
{
    public function index()
    {
        Auth::requireLogin();
        if (!Auth::hasRole('Administrator')) {
            die('Unauthorized');
        }

        $userModel = new User();
        $users = $userModel->getAll();
        $roles = $userModel->getRoles();
        $this->view('users/index', ['users' => $users, 'roles' => $roles]);
    }

    public function store()
    {
        Auth::requireLogin();
        if (!Auth::hasRole('Administrator')) {
            die('Unauthorized');
        }

        $action = $_POST['action'] ?? '';
        $userModel = new User();
        $message = '';
        $error = '';

        try {
            if ($action === 'create') {
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $roleId = (int) ($_POST['role_id'] ?? 0);

                if ($username === '' || $email === '' || $password === '' || $roleId === 0) {
                    throw new RuntimeException('Todos los campos son obligatorios.');
                }

                $userModel->create([
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                    'role_id' => $roleId,
                ]);
                $message = 'Usuario creado correctamente.';
            } elseif ($action === 'update') {
                $id = (int) ($_POST['id'] ?? 0);
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $roleId = (int) ($_POST['role_id'] ?? 0);

                if ($id === 0) {
                    throw new RuntimeException('Usuario inválido.');
                }
                if ($username === '' || $email === '' || $roleId === 0) {
                    throw new RuntimeException('Nombre, correo y rol son obligatorios.');
                }

                $data = [
                    'username' => $username,
                    'email' => $email,
                    'role_id' => $roleId
                ];
                if (!empty($_POST['password'])) {
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }
                $userModel->update($id, $data);
                $message = 'Usuario actualizado.';
            } elseif ($action === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                if ($id === 0) {
                    throw new RuntimeException('Usuario inválido.');
                }
                $userModel->delete($id);
                $message = 'Usuario eliminado.';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $users = $userModel->getAll();
        $roles = $userModel->getRoles();
        $this->view('users/index', ['users' => $users, 'roles' => $roles, 'message' => $message, 'error' => $error]);
    }
}
