<?php

class Book extends Model
{
    public function getAll()
    {
        return $this->db->query('SELECT * FROM books ORDER BY title')->fetchAll();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare('INSERT INTO books (title, author, year, genre, quantity) VALUES (:title, :author, :year, :genre, :quantity)');
        $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare('UPDATE books SET title = :title, author = :author, year = :year, genre = :genre, quantity = :quantity WHERE id = :id');
        $stmt->execute($data);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
