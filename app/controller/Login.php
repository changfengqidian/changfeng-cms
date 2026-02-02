<?php
namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Db;
use think\facade\Session;
use think\facade\Request;

class Login extends BaseController
{
    /**
     * Show Login Page
     */
    public function index()
    {
        // Auto-check/init database for convenience (Development logic)
        $this->ensureUsersTable();
        
        if (Session::has('admin_id')) {
            return redirect((string)url('admin/index'));
        }
        return View::fetch('login/index');
    }

    /**
     * Handle Login
     */
    public function dologin()
    {
        if (!Request::isPost()) {
            return json(['code' => 0, 'msg' => 'Invalid Request']);
        }

        $username = Request::param('username');
        $password = Request::param('password');

        if (empty($username) || empty($password)) {
            return json(['code' => 0, 'msg' => 'Username and password are required']);
        }

        // Simple auth logic
        $user = Db::name('users')->where('username', $username)->find();

        if ($user && password_verify($password, $user['password'])) {
            // Set Session
            Session::set('admin_id', $user['id']);
            Session::set('admin_name', $user['username']);
            
            // Optional: Issue JWT here if needed for API later
            // $payload = [...];
            // $jwt = JWT::encode($payload, env('JWT_KEY', 'secret'), 'HS256');

            return json(['code' => 1, 'msg' => 'Login Success', 'url' => (string)url('admin/index')]);
        }

        return json(['code' => 0, 'msg' => 'Invalid credentials']);
    }

    /**
     * Logout
     */
    public function logout()
    {
        Session::clear();
        return redirect((string)url('login/index'));
    }

    /**
     * Internal Helper: Ensure users table exists with admin/123456
     * In production, use Migrations!
     */
    private function ensureUsersTable()
    {
        try {
            $exist = Db::query("SHOW TABLES LIKE 'users'");
            if (empty($exist)) {
                $sql = "CREATE TABLE `users` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `username` varchar(50) NOT NULL,
                  `password` varchar(255) NOT NULL,
                  `create_time` int(11) DEFAULT NULL,
                  `update_time` int(11) DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `username` (`username`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
                Db::execute($sql);

                // Create default admin: admin / 123456
                $pass = password_hash('123456', PASSWORD_DEFAULT);
                Db::name('users')->insert([
                    'username' => 'admin',
                    'password' => $pass,
                    'create_time' => time(),
                ]);
            }
        } catch (\Exception $e) {
            // Logic to handle DB connection error silently or log it
            // trace($e->getMessage());
        }
    }
}
