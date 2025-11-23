<?php

class User extends Model
{
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT users.*, roles.name AS role_name FROM users INNER JOIN roles ON users.role_id = roles.id WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function getAll()
    {
        return $this->db->query('SELECT users.*, roles.name as role_name FROM users JOIN roles ON users.role_id = roles.id ORDER BY username')->fetchAll();
    }

    public function getRoles()
    {
        return $this->db->query('SELECT * FROM roles ORDER BY id')->fetchAll();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, :role_id)');
        $stmt->execute($data);
    }

    public function update($id, $data)
    {
        $data['id'] = $id;
        $sql = 'UPDATE users SET username = :username, email = :email, role_id = :role_id';
        if (isset($data['password'])) {
            $sql .= ', password = :password';
        }
        $sql .= ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
