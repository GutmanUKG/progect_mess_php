<?php require_once __DIR__ . '/incs/header.tpl.php';
require_once __DIR__ . '/../incs/functions.php';
$title = "Home";

?>

    <div class="container mt-5">
        <div class="row">

            <div class="col-12 mb-4">

                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['errors'];
                        unset($_SESSION['errors']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

            </div>
            <?php if(check_auth()):?>
            <form action="" method="post" class="mb-2">
                <div class="form-floating">
                <textarea class="form-control" placeholder="Leave a comment here" id="send-message" name="message"
                          style="height: 100px"></textarea>
                    <label for="send-message">Comments</label>
                </div>

                <button type="submit" class="btn btn-primary mt-3" name="send_message">Send</button>
            </form>

            <div class="col-12">
                <hr>
            </div>

        </div>
        <?php endif;?>
        <div class="row">

            <div class="col-12">
                <?php if(!empty($mess)):?>
                    <?php foreach ($mess as $m):?>
                        <div class="card mb-3 <?php if(!$m['status']) echo 'border-danger'?>" id="<?= $m['id']?>">
                            <div class="card-body">

                                <div class="d-flex justify-content-between">
                                    <h5 class="card-title"><?=$m['user_name']?></h5>
                                    <p class="message-created"><?=$m['date_format']?></p>
                                </div>
                                <div class="card-text">
                                   <?=nl2br(h($m['mess']))?>
                                </div>
                                <?php if(check_admin()):?>
                                    <div class="card-actions mt-2">
                                    <p>
                                        <?php if($m['status'] == 1):?>
                                            <a href="?page=<?=$page?>&do=toggle-status&status=0&id=<?=$m['id']?>">
                                                Disable</a> |
                                        <?php else:?>
                                            <a href="?page=<?=$page?>&do=toggle-status&status=1&id=<?=$m['id']?>">
                                                Approve</a> |
                                        <?php endif;?>
                                        <a data-bs-toggle="collapse" href="#collapse-<?= $m['id']?>">Edit</a>
                                    </p>

                                    <div class="collapse" id="collapse-<?= $m['id']?>">
                                        <form action="" method="post">
                                            <div class="form-floating">
                                        <textarea class="form-control"
                                                  placeholder="Leave a comment here"
                                                  name="mess"
                                                  id="message-<?= $m['id']?>"
                                                  style="height: 100px"><?=h($m['mess'])?>
                                        </textarea>
                                                <label for="message-<?= $m['id']?>">Comments</label>
                                            </div>
                                            <input type="hidden" name="id" value="<?=$m['id']?>">
                                            <input type="hidden" name="page" value="<?=$_GET['page'] ?? 1?>">
                                            <button type="submit" class="btn btn-primary mt-3" name="edit-message">Save</button>
                                        </form>
                                    </div>
                                </div>
                                <?php endif;?>
                            </div>
                        </div>
                    <?php endforeach;?>

                    <div class="row">
                        <div class="col-12">
                            <?=$pagination?>
                        </div>
                    </div>
                    <?php else:?>
                    Сообщения не найдены =(
                <?php endif;?>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/incs/footer.tpl.php'; ?>