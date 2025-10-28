<?php

class JsonDataManager
{
    private string $filePath;
    private array $users;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        if (!file_exists($filePath)) {
            file_put_contents($filePath, json_encode([]));
        }
        $this->users = json_decode(file_get_contents($filePath), true) ?? [];
    }

    // Get all users
    public function getAllUsers(): array
    {
        return $this->users;
    }

    // Save all users to JSON file
    public function saveAllUsers(array $users): bool
    {
        $this->users = $users;
        return file_put_contents($this->filePath, json_encode($users, JSON_PRETTY_PRINT)) !== false;
    }

    // Add new user
    public function addUser(string $name, string $email, string $password): bool
    {
        if ($this->getUserByEmail($email)) {
            return false; // email already exists
        }

        $this->users[] = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'tickets' => []
        ];

        return $this->saveAllUsers($this->users);
    }

    // Validate login
    public function validateLogin(string $email, string $password): ?array
    {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    // Get user by email
    public function getUserByEmail(string $email): ?array
    {
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }

    public function updateUserTickets(string $email, array $tickets) {
    $users = $this->getAllUsers();
    foreach ($users as &$user) {
        if ($user['email'] === $email) {
            $user['tickets'] = $tickets;
            break;
        }
    }
    file_put_contents($this->filePath, json_encode($users, JSON_PRETTY_PRINT));
}

    // Update a user's tickets
    public function setUserTickets(string $email, array $tickets): bool
    {
        foreach ($this->users as &$user) {
            if ($user['email'] === $email) {
                $user['tickets'] = $tickets;
                return $this->saveAllUsers($this->users);
            }
        }
        return false;
    }
}
