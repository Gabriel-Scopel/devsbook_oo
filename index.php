<?php
require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/PostDaoMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$activeMenu = 'home';

$postDao = new PostDaoMysql($pdo);
$feed = $postDao->getHomeFeed($userInfo->id);



require 'partials/header.php';
require 'partials/menu.php';
?>
<section class="feed mt-10">
    <div class="row">
           <?php require 'partials/feed-editor.php'; ?>
           <?php foreach($feed as $item): ?>
                <?php require 'partials/feed-item.php'; ?>
            <?php endforeach; ?>
            </div>
       
            <div class="column side pl-5">
                <div class="box banners">
                            <div class="box-header">
                                <div class="box-header-text">Patrocinios</div>
                                <div class="box-header-buttons">
                                    
                                </div>
                            </div>
                            <div style="display: flex;" class="box-body">
                                <a style=" margin: 5px;" href=""><img width="30px" src="media/avatars/default.jpg" /></a>
                                <a  style=" margin: 5px;" href=""><img width="30px" src="media/avatars/default.jpg"/></a>
                            </div>
                        </div>
                        <div class="box">
                            <div class="box-body m-10">
                                Criado com ❤️ por B7Web
                            </div>
                        </div>
                </div>
    </div>   
</section>
<?php
require 'partials/footer.php';
?> 
