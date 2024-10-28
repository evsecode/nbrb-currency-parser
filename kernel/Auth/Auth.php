<?php

namespace App\Kernel\Auth;

use App\Kernel\Config\ConfigInterface;
use App\Kernel\Database\DatabaseInterface;
use App\Kernel\Session\SessionInterface;

class Auth implements AuthInterface
{
    public function __construct(
        private DatabaseInterface $db,
        private SessionInterface $session,
        private ConfigInterface $config,
    ) {

    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->db->first($this->table(), [
            $this->email() => $email,
        ]);
        if (! $user) {
            return false;
        }

        if (! password_verify($password, $user[$this->password()])) {

            return false;
        }

        $this->session->set($this->sessionFilled(), $user['id']);

        return true;
    }

    public function logout(): void
    {
        $this->session->remove($this->sessionFilled());
    }

    public function check(): bool
    {
        return $this->session->has($this->sessionFilled());
    }

    public function user(): ?User
    {
        if (! $this->check()) {

            return null;
        }

        $user = $this->db->first($this->table(), [
            'id' => $this->session->get($this->sessionFilled()),
        ]);

        if ($user) {

            return new User(
                $user['id'],
                $user[$this->email()],
                $user[$this->name()],
                $user[$this->password()],
                $user['is_admin']
            );
        }

        return null;
    }

    public function table(): string
    {
        return $this->config->get('auth.table', 'users');
    }

    public function name(): string
    {
        return $this->config->get('auth.name', 'name');
    }

    public function email(): string
    {
        return $this->config->get('auth.email', 'email');
    }

    public function password(): string
    {
        return $this->config->get('auth.password', 'password');
    }

    public function sessionFilled(): string
    {
        return $this->config->get('auth.session_filled', 'user_id');
    }
}
