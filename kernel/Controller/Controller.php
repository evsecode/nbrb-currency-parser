<?php

namespace App\Kernel\Controller;

use App\Kernel\Auth\AuthInterface;
use App\Kernel\Cache\CacheInterface;
use App\Kernel\Database\DatabaseInterface;
use App\Kernel\Http\RedirectInterface;
use App\Kernel\Http\RequestInterface;
use App\Kernel\Session\SessionInterface;
use App\Kernel\View\ViewInterface;
use App\Services\NBRBApiClient;

abstract class Controller
{
    private ViewInterface $view;

    private RequestInterface $request;

    private RedirectInterface $redirect;

    private SessionInterface $session;

    private DatabaseInterface $database;

    private AuthInterface $auth;

    private NBRBApiClient $apiClient;

    private CacheInterface $cache;

    public function cache(): CacheInterface
    {
        return $this->cache;
    }

    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    public function apiClient(): NBRBApiClient
    {
        return $this->apiClient;
    }

    public function setApiClient(NBRBApiClient $apiClient): void
    {
        $this->apiClient = $apiClient;
    }

    public function auth(): AuthInterface
    {
        return $this->auth;
    }

    public function setAuth(AuthInterface $auth): void
    {
        $this->auth = $auth;
    }

    public function session(): SessionInterface
    {
        return $this->session;
    }

    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    public function db(): DatabaseInterface
    {
        return $this->database;
    }

    public function setDatabase(DatabaseInterface $database): void
    {
        $this->database = $database;
    }

    public function setRedirect(RedirectInterface $redirect): void
    {
        $this->redirect = $redirect;
    }

    public function redirect(string $url): RedirectInterface
    {
        return $this->redirect->to($url);
    }

    public function request(): RequestInterface
    {
        return $this->request;
    }

    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    public function view(string $name, array $data = []): void
    {
        $this->view->page($name, $data);
    }

    public function setView(ViewInterface $view): void
    {
        $this->view = $view;
    }
}
