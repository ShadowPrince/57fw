<?php
namespace Uac\Manager;

class UserManager extends \Orm\Manager {
    /**
     *  Create user with username, password, email
     *  @param string $username 
     *  @param string $raw_password 
     *  @param string $email
     *  @return \Auth\Entity\User
     */
    public function credentialsCreate($username, $raw_password, $email) {
        $user = new \Uac\Model\User();
        $user->username = $username;
        $user->password = $this->encryptPassword($raw_password);
        $user->email = $email;
        return $user;
    }

    /**
     * Login user trought credentials
     * @param string $username
     * @param string $password
     * @throw \sfException
     * @return \Auth\Entity\User
     */
    public function credentialsLogin($username, $password) {
        $user = $this->find()
            ->filter('username =', $username)
            ->filter('password =', $this->encryptPassword($password))
            ->current();


        $user->auth_token = $this->generateToken($user);
        $user->auth_token_expire = (new \DateTime())->add(new \DateInterval('P14D'));
        $this->save($user);

        return $user;
    }

    /**
     * Login user trought User instance
     * @param \Auth\Entity\User $user
     * @return \Auth\Entity\User
     */
    public function instanceLogin($user) {
        return $this->credentialsLogin($user->username, $user->password);
    }

    /**
     * Login user troght cookie token
     * @param string $auth_token
     * @throw \sfException
     * @return \Auth\Entity\User
     */
    public function cookieLogin($auth_token) {
        $user = $this->find()
            ->filter('auth_token =', $auth_token)
            ->current();

        $user->auth_token = (new \DateTime())->add(new \DateInterval('P14D'));
        return $user;
    }

    /**
     * Logout user (change auth token)
     * @param \Auth\Entity\User $user
     */
    public function logout($user) {
        $user->auth_token = $this->generateToken($user);
    }

    /**
     * Generate token for cookie
     * @param \Auth\Entity\User $user
     * @return string
     */
    private function generateToken($user) {
        $stoken = $this->e->app('uac')->getConfig('secret_token');
        return md5(
            $stoken . '/' .
            $user->username . '/' . 
            microtime() . '/' . 
            rand(0, strlen($stoken)*100)
        );
    }
    
    /**
     * Encrypt raw password
     * @param strng $raw_password
     * @return string
     */
    private function encryptPassword($raw_password) {
        return $raw_password;
    }
}
