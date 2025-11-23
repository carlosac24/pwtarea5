<?php

class TransactionController extends Controller
{
    public function index()
    {
        Auth::requireLogin();
        $user = Auth::user();
        $isStaff = Auth::hasRole('Administrator', 'Librarian');

        $transactionModel = new Transaction();
        $transactions = $transactionModel->getAll($isStaff ? null : $user['id']);

        $usersList = [];
        if ($isStaff) {
            $userModel = new User();
            $usersList = $userModel->getAll();
        }
        $bookModel = new Book();
        $books = $bookModel->getAll();

        $this->view('transactions/index', [
            'transactions' => $transactions,
            'usersList' => $usersList,
            'books' => $books,
            'isStaff' => $isStaff,
            'user' => $user
        ]);
    }

    public function store()
    {
        Auth::requireLogin();
        $user = Auth::user();
        $isStaff = Auth::hasRole('Administrator', 'Librarian');
        $action = $_POST['action'] ?? '';
        $transactionModel = new Transaction();
        $message = '';
        $error = '';

        try {
            if ($action === 'issue') {
                if (!$isStaff) {
                    throw new RuntimeException('No autorizado.');
                }
                $transactionModel->issue($_POST['user_id'], $_POST['book_id']);
                $message = 'Préstamo registrado.';
            } elseif ($action === 'return') {
                $transactionModel->returnBook($_POST['transaction_id'], $user['id'], $isStaff);
                $message = 'Devolución registrada.';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $transactions = $transactionModel->getAll($isStaff ? null : $user['id']);
        $usersList = $isStaff ? (new User())->getAll() : [];
        $books = (new Book())->getAll();

        $this->view('transactions/index', [
            'transactions' => $transactions,
            'usersList' => $usersList,
            'books' => $books,
            'isStaff' => $isStaff,
            'user' => $user,
            'message' => $message,
            'error' => $error
        ]);
    }
}
