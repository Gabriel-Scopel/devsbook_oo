<?php
require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/UserDaoMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

$userDao = new UserDaoMysql($pdo);

$name = filter_input(INPUT_POST, 'name');
$email = filter_input(INPUT_POST, 'email');
$city= filter_input(INPUT_POST, 'city');
$work = filter_input(INPUT_POST, 'work');
$password = filter_input(INPUT_POST, 'password');
$password_confirmation = filter_input(INPUT_POST, 'password_confirmation');

if($name && $email){
    $userInfo->name = $name;
    $userInfo->city = $city;
    $userInfo->work = $work;
    if($userInfo->$email != $email){
        if($userDao->findByEmail($email) === false){
            $userInfo->email = $email;
        }else{
            $_SEESSION['flash'] = 'email já existente.';
            header("location: ".$base."/configuracoes.php");
            exit;
        }
    }
}

header("location: ".$base."/configuracoes.php");
exit;