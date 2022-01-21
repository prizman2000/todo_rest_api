<?php

require __DIR__ . '../vendor/autoload.php';

class Client
{
    private $url;
    private $token;

    public function __construct()
    {
        $this->url = getenv('URL');
    }

    public function register($login, $password): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->post($this->set_path('/auth/register'), array(
           'login' => $login,
           'password' => $password
        ), true);

        return $curl->response;
    }

    public function login($login, $password): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post($this->set_path('/api/login'), array(
            'login' => $login,
            'password' => $password
        ), true);
        $this->token = $curl->response;

        return $curl->response;
    }

    public function add_post($title, $description): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Authorisation', 'Bearer ' . $this->token);
        $curl->post($this->set_path('/api/post'), array(
            'title' => $title,
            'description' => $description
        ), true);

        return $curl->response;
    }

    public function update_post($id, $title, $description): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Authorisation', 'Bearer ' . $this->token);
        $curl->put($this->set_path('/api/post/' . $id), array(
            'title' => $title,
            'description' => $description
        ), true);

        return $curl->response;
    }

    public function get_posts(): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Authorisation', 'Bearer ' . $this->token);
        $curl->get($this->set_path('/api/post'));

        return $curl->response;
    }

    public function get_post($id): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Authorisation', 'Bearer ' . $this->token);
        $curl->get($this->set_path('/api/post/' . $id));

        return $curl->response;
    }

    public function delete_post($id): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setHeader('Authorisation', 'Bearer ' . $this->token);
        $curl->delete($this->set_path('/api/post/' . $id));

        return $curl->response;
    }

    public function add_file($filePath): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->post($this->set_path('/file'), array(
            'file' => '@'.$filePath
        ), true);

        return $curl->response;
    }

    public function get_all_files(): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->get($this->set_path('/file'));

        return $curl->response;
    }

    public function delete_file($fileName): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->delete($this->set_path('/file/'.$fileName));

        return $curl->response;
    }

    public function download_file($fileName): false|string|null
    {
        $curl = new Curl\Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $curl->get($this->set_path('/file/'.$fileName));

        return $curl->response;
    }

    private function set_path($path): string
    {
        return $this->url + $path;
    }
}