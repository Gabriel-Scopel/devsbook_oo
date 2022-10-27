<?php
require_once 'models/Post.php';
require_once 'dao/UserRelationDaoMysql.php';
require_once 'dao/UserDaoMysql.php';
require_once 'dao/PostLikeDaoMysql.php';
require_once 'dao/PostCommentDaoMysql.php';

class PostDaoMysql implements PostDAO{
    private $pdo;

    public function __construct(PDO $driver){
        $this->pdo = $driver;
    }

    public function insert(Post $p){
        $sql = $this->pdo->prepare('INSERT INTO posts (
            id_user, type, created_at, body
            )VALUES(
            :id_user, :type, :created_at, :body
            )');

            $sql->bindValue(':id_user', $p->id_user);
            $sql->bindValue(':type', $p->type);
            $sql->bindValue(':created_at', $p->created_at);
            $sql->bindValue(':body', $p->body);
            $sql->execute();
    }
    public function getUserFeed($id_user){
        $array = [];
        
        $sql = $this->pdo->prepare("SELECT * FROM posts WHERE id_user
         = :id_user ORDER BY created_at DESC");
         $sql->bindValue('id_user', $id_user);
         $sql->execute();
         if($sql->rowCount()>0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $array = $this->_postListToObject($data, $id_user);
         }
         return $array;
    }

    public function getHomeFeed($id_user){
        $array = [];
        $urDao = new UserRelationDaoMysql($this->pdo);
        $userList = $urDao->getFollowing($id_user);
        $userList[] = $id_user;

        $sql = $this->pdo->query("SELECT * FROM posts WHERE id_user
         IN (".implode(',', $userList).") ORDER BY created_at DESC");
         if($sql->rowCount()>0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $array = $this->_postListToObject($data, $id_user);
         }
         return $array;
    }
    public function getPhotosFrom($id_User){
        $array = [];
        $sql = $this->pdo->prepare("SELECT * FROM posts WHERE id_user = :id_user AND type = 'photo' ORDER BY created_at DESC");
        $sql->bindValue(':id_user', $id_User);
        $sql->execute();
        if($sql->rowCount()>0){
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $array = $this->_postListToObject($data, $id_User);
        }       
        return $array;
    }
    private function _postListToObject($post_list, $id_user){
        $posts = [];
        $userDao = new UserDaoMysql($this->pdo);
        $postLikeDao = new PostLikeDaoMysql($this->pdo);
        $PostCommentDao = new PostCommentDaoMysql($this->pdo);
        foreach($post_list as $post_item){
            $newPost = new Post();
            $newPost->id = $post_item['id'];
            $newPost->type = $post_item['type'];
            $newPost->created_at = $post_item['created_at'];
            $newPost->body = $post_item['body'];
            $newPost->mine = false;
            if($post_item['id_user'] == $id_user){
                $newPost->mine = true;
            }
            $newPost->user = $userDao->findById($post_item['id_user']);
            $newPost->likeCount = $postLikeDao->getLikeCount($newPost->id);
            $newPost->liked = $postLikeDao->isLiked($newPost->id, $id_user);
            $newPost->comments = $PostCommentDao->getComments($newPost->id);
            $posts[] = $newPost;

        }
        return $posts;
    }
    public function delete($id, $id_user) {
        $postLikeDao = new PostLikeDaoMysql($this->pdo);
        $postCommentDao = new PostCommentDaoMysql($this->pdo);

        // 1. verificar se o post existe (o tipo)
        $sql = $this->pdo->prepare("SELECT * FROM posts 
        WHERE id = :id AND id_user = :id_user");
        $sql->bindValue(':id', $id);
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        if($sql->rowCount() > 0) {
            $post = $sql->fetch(PDO::FETCH_ASSOC);

            // 2. deletar os likes e comments
            $postLikeDao->deleteFromPost($id);
            $postCommentDao->deleteFromPost($id);

            // 3. deletar a eventual foto (type == photo)
            if($post['type'] === 'photo') {
                $img = 'media/uploads/'.$post['body'];
                if(file_exists($img)) {
                    unlink($img);
                }
            }

            // 4. deletar o post
            $sql = $this->pdo->prepare("DELETE FROM posts 
            WHERE id = :id AND id_user = :id_user");
            $sql->bindValue(':id', $id);
            $sql->bindValue(':id_user', $id_user);
            $sql->execute();

        }
    }
}