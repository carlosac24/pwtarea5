<?php

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            $this->redirect('home');
        }
        $this->view('auth/login');
    }

    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = '';

        if ($email === '' || $password === '') {
            $error = 'Por favor ingresa correo y contraseña.';
        } else {
            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                Auth::login([
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role_id' => $user['role_id'],
                    'role' => $user['role_name'],
                ]);
                $this->redirect('home');
            } else {
                $error = 'Credenciales inválidas.';
            }
        }

        $this->view('auth/login', ['error' => $error]);
    }

    public function logout()
    {
        Auth::logout();
        $this->redirect('login');
    }
}
