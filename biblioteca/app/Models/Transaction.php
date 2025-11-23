<?php

class Transaction extends Model
{
    public function issue($userId, $bookId)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT quantity, (
                SELECT COUNT(*) FROM transactions WHERE book_id = books.id AND date_of_return IS NULL
            ) AS loans FROM books WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $bookId]);
            $book = $stmt->fetch();
            if (!$book) {
                throw new RuntimeException('Libro no encontrado.');
            }
            if ((int) $book['quantity'] <= (int) $book['loans']) {
                throw new RuntimeException('No hay ejemplares disponibles.');
            }

            $stmt = $this->db->prepare('INSERT INTO transactions (user_id, book_id, date_of_issue) VALUES (:user_id, :book_id, CURDATE())');
            $stmt->execute([
                'user_id' => $userId,
                'book_id' => $bookId,
            ]);
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function returnBook($transactionId, $userId, $isStaff)
    {
        $stmt = $this->db->prepare('SELECT * FROM transactions WHERE id = :id');
        $stmt->execute(['id' => $transactionId]);
        $transaction = $stmt->fetch();
        if (!$transaction) {
            throw new RuntimeException('Préstamo no encontrado.');
        }

        if (!$isStaff && (int) $transaction['user_id'] !== (int) $userId) {
            throw new RuntimeException('No autorizado.');
        }
        if ($transaction['date_of_return']) {
            throw new RuntimeException('El préstamo ya fue cerrado.');
        }

        $stmt = $this->db->prepare('UPDATE transactions SET date_of_return = CURDATE() WHERE id = :id');
        $stmt->execute(['id' => $transactionId]);
    }

    public function getAll($userId = null)
    {
        $sql = 'SELECT transactions.*, users.username, roles.name AS role_name, books.title FROM transactions
            INNER JOIN users ON transactions.user_id = users.id
            INNER JOIN roles ON users.role_id = roles.id
            INNER JOIN books ON transactions.book_id = books.id';

        $params = [];
        if ($userId) {
            $sql .= ' WHERE users.id = :user_id';
            $params['user_id'] = $userId;
        }
        $sql .= ' ORDER BY transactions.date_of_issue DESC, transactions.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function hasActiveLoan($userId, $bookId)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM transactions WHERE user_id = :user AND book_id = :book AND date_of_return IS NULL');
        $stmt->execute(['user' => $userId, 'book' => $bookId]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
