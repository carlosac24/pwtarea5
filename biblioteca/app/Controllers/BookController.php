<?php

class BookController extends Controller
{
    public function index()
    {
        Auth::requireLogin();
        if (!Auth::hasRole('Administrator', 'Librarian')) {
            die('Unauthorized');
        }

        $bookModel = new Book();
        $books = $bookModel->getAll();
        $this->view('books/index', ['books' => $books]);
    }

    public function store()
    {
        Auth::requireLogin();
        if (!Auth::hasRole('Administrator', 'Librarian')) {
            die('Unauthorized');
        }

        $action = $_POST['action'] ?? '';
        $bookModel = new Book();
        $message = '';
        $error = '';

        try {
            if ($action === 'create') {
                $title = trim($_POST['title'] ?? '');
                $author = trim($_POST['author'] ?? '');
                $quantity = (int) ($_POST['quantity'] ?? 0);

                if ($title === '' || $author === '' || $quantity <= 0) {
                    throw new RuntimeException('Título, autor y cantidad son obligatorios.');
                }

                $bookModel->create([
                    'title' => $title,
                    'author' => $author,
                    'year' => $_POST['year'] !== '' ? (int) $_POST['year'] : null,
                    'genre' => trim($_POST['genre'] ?? '') !== '' ? trim($_POST['genre']) : null,
                    'quantity' => $quantity
                ]);
                $message = 'Libro agregado correctamente.';
            } elseif ($action === 'update') {
                $id = (int) ($_POST['id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $author = trim($_POST['author'] ?? '');
                $quantity = (int) ($_POST['quantity'] ?? 0);

                if ($id === 0) {
                    throw new RuntimeException('Libro inválido.');
                }
                if ($title === '' || $author === '' || $quantity <= 0) {
                    throw new RuntimeException('Título, autor y cantidad son obligatorios.');
                }

                $bookModel->update($id, [
                    'title' => $title,
                    'author' => $author,
                    'year' => $_POST['year'] !== '' ? (int) $_POST['year'] : null,
                    'genre' => trim($_POST['genre'] ?? '') !== '' ? trim($_POST['genre']) : null,
                    'quantity' => $quantity
                ]);
                $message = 'Libro actualizado.';
            } elseif ($action === 'delete') {
                $id = (int) ($_POST['id'] ?? 0);
                if ($id === 0) {
                    throw new RuntimeException('Libro inválido.');
                }
                $bookModel->delete($id);
                $message = 'Libro eliminado.';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $books = $bookModel->getAll();
        $this->view('books/index', ['books' => $books, 'message' => $message, 'error' => $error]);
    }
}
