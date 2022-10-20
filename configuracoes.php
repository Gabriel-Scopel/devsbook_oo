<?php
require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/UserDaoMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$activeMenu = 'configuracoes';

$userDao = new UserDaoMysql($pdo);

require 'partials/header.php';
require 'partials/menu.php';
?>

<section class="feed mt-10">
    
    <h1>Configurações</h1>
    
    <?php if (!empty($_SESSION['flash'])) : ?>
        <div  style="padding:5px; font-size:15px; margin-top:10px; background-color: red; width:250px">
        <?= $_SESSION['flash']; ?>
        <?php $_SESSION['flash'] = ''; ?>
        </div>
    <?php endif; ?>
    
    
    <form method="POST" style="margin-top: 10px;" class="config-form" enctype="multipart/form-data" action="configuracoes_action.php">
        <label style="padding: 10px 0; display:block;" for="">
            Novo Avatar:
            <input type="file" name="avatar"><br>
            <img style="padding: 10px;" width="100px" src="<?= $base; ?>/media/avatars/<?= $userInfo->avatar; ?>" alt="">
        </label>
        <label style="padding: 10px 0; display:block;" for="">
            Nova Capa:
            <input type="file" name="cover"> <br>
            <img style="padding: 10px;" width="300px" src="<?= $base; ?>/media/covers/<?= $userInfo->cover; ?>" alt="">
        </label>
        <hr>
        <label style="padding: 10px 0; display:block;" for="">
            Nome Completo:<br>
            <input type="text" name="name" value="<?= $userInfo->name; ?>">
        </label>
        <label style="padding: 10px 0; display:block; " for="">
            E-mail:<br>
            <input type="text" name="email" value="<?= $userInfo->email; ?>">
        </label>
        <label style="padding: 10px 0; display:block;" for="">
            Data de Nascimento:<br>
            <input id="birthdate" type="text" name="birthdate" value="<?= date('d/m/Y', strtotime($userInfo->birthdate)); ?>">
        </label>
        <label style="padding: 10px 0; display:block;" for="">
            Cidade:<br>
            <input type="text" name="city" value="<?= $userInfo->city; ?>">
        </label>
        <label style="padding: 10px 0; display:block;" for="">
            Trabalho:<br>
            <input type="text" name="work" value="<?= $userInfo->work; ?>">
        </label>
        <hr>
        <label style="padding: 10px 0; display:block;" for="">
            Nova Senha:<br>
            <input type="password" name="password">
        </label>
        <label style="padding: 10px 0; display:block;" for="">
            Confirmar Nova Senha:<br>
            <input type="password" name="password_confirmation">
        </label>
        <button style="border: 0; padding: 10px 20px; background-color:#4a76a8; border-radius: 10px; color: #fff; font-size:15px;" class="button">Salvar</button>
    </form>
</section>

<script src="https://unpkg.com/imask"></script>
<script>
IMask(
    document.getElementById("birthdate"),
    {mask:'00/00/0000'}
);
</script>
<?php
require 'partials/footer.php';
?>