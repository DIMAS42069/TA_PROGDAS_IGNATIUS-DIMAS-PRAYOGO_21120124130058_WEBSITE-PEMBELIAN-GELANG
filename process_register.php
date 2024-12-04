<?php
session_start();

class User {
    public $fullname;
    public $email;
    public $password;

    public function __construct($fullname, $email, $password) {
        $this->fullname = $fullname;
        $this->email = $email;
        $this->password = $password;
    }
}

class UserManager {
    private $data_file = 'users.json';
    private $users = [];

    public function __construct() {
        if (file_exists($this->data_file)) {
            $this->users = json_decode(file_get_contents($this->data_file), true);
        }
    }

    public function emailExists($email) {
        foreach ($this->users as $user) {
            if ($user['email'] === $email) {
                return true;
            }
        }
        return false;
    }

    public function registerUser ($fullname, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $this->users[] = [
            'fullname' => $fullname,
            'email' => $email,
            'password' => $hashed_password,
        ];
        file_put_contents($this->data_file, json_encode($this->users, JSON_PRETTY_PRINT));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        $userManager = new UserManager();

        if ($userManager->emailExists($email)) {
            $error = "Email sudah terdaftar!";
            include 'register.php';
            exit();
        }

        $userManager->registerUser ($fullname, $email, $password);
        header("Location: login.php");
        exit();
    } else {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    }
}
?>
