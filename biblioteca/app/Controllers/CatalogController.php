<?php

class CatalogController extends Controller
{
    public function index()
    {
        Auth::requireLogin();
        $user = Auth::user();
        $canBorrow = Auth::hasRole('Reader');
        $transactionModel = new Transaction();

        $message = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
            if (!$canBorrow) {
                $error = 'Solo los lectores pueden solicitar préstamos.';
            } else {
                try {
                    if ($transactionModel->hasActiveLoan($user['id'], $_POST['book_id'])) {
                        throw new RuntimeException('Ya tienes un préstamo activo de este libro.');
                    }
                    $transactionModel->issue($user['id'], $_POST['book_id']);
                    $message = 'Préstamo registrado con éxito.';
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        $books = $this->getBooksWithAvailability();

        $this->view('catalog/index', [
            'books' => $books,
            'canBorrow' => $canBorrow,
            'message' => $message,
            'error' => $error
        ]);
    }

    private function getBooksWithAvailability()
    {
        $db = Database::connect();
        return $db->query('SELECT books.*, 
            (SELECT COUNT(*) FROM transactions WHERE book_id = books.id AND date_of_return IS NULL) as loans 
            FROM books ORDER BY title')->fetchAll();
    }
}
