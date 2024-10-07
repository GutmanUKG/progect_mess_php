<?php
require_once __DIR__ . '/incs/db.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/incs/functions.php';
require_once __DIR__ . '/incs/Pagination.php';
session_start();
$title = "Home";
/** @var PDO $db */
if(isset($_POST['send_message'])){
    $data = load(['message']);
    $v = new \Valitron\Validator($data);
    $v->rules([
        'required'=> ['message']
    ]);
    if($v->validate()){
       if(save_message($data)){
          redirect('index.php');
       }
    }else{
        $_SESSION['errors'] = get_errors($v->errors());
    }
}

if(isset($_POST['edit-message'])){
    $data = load(['mess', 'id', 'page']);

   // $page = isset($data['page']) ? "?page=" . (int) $data['page'] : "";
    $v = new \Valitron\Validator($data);
    $v->rules([
        'required'=> ['mess', 'id'],
        'integer'=> ['id', 'page']
    ]);
    if($v->validate()){
        if(edit_message($data)){
            redirect("index.php?page={$data['page']}");
        }
    }else{
        $_SESSION['errors'] = get_errors($v->errors());
    }
}



if(isset($_GET['do']) && $_GET['do'] == 'toggle-status'){
    $id = $_GET['id'] ?? 0;
    $status = isset($_GET['status']) ? (int) $_GET['status'] : 0;
    toggle_message_status($status, $id);
    $page = isset($_GET['page']) ? "?page=" . (int) $_GET['[page'] : "";
    redirect("index.php{$page}#{$id}");
}

$page = $_GET['page'] ?? 1;
$per_page = 5;
$total = get_count_mess();
$pagination = new Pagination((int) $page, $per_page, $total);
$start = $pagination->getStart();

$mess = get_messages($start, $per_page);
//dump($mess);
require_once __DIR__ . '/views/index.tpl.php';
?>

