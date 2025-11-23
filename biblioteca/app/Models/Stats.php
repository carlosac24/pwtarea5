<?php

class Stats extends Model
{
    public function getCounts()
    {
        return [
            'total_users' => (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'total_books' => (int) $this->db->query('SELECT COUNT(*) FROM books')->fetchColumn(),
            'available_books' => (int) $this->db->query('SELECT COALESCE(SUM(quantity),0) FROM books')->fetchColumn(),
            'active_loans' => (int) $this->db->query('SELECT COUNT(*) FROM transactions WHERE date_of_return IS NULL')->fetchColumn(),
        ];
    }
}
