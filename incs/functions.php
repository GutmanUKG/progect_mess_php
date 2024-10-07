<?php

function dump(array|object $data): void
{
    echo "<pre>" . print_r($data, 1) . "</pre>";
}

function load(array $fillable, $post = true): array
{
    $load_data = $post ? $_POST : $_GET;
    $data = [];
    foreach ($fillable as $field) {
        if (isset($load_data[$field])) {
            $data[$field] = trim($load_data[$field]);
        } else {
            $data[$field] = '';
        }
    }
    return $data;
}

function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES);
}

function old(string $name, $post = true): string
{
    $load_data = $post ? $_POST : $_GET;
    return isset($load_data[$name]) ? h($load_data[$name]) : '';
}

function redirect(string $url = ''): never
{
    header("Location: {$url}");
    die;
}

function get_errors(array $errors): string
{
    $html = '<ul class="list-unstyled">';
    foreach ($errors as $error_group) {
        foreach ($error_group as $error) {
            $html .= "<li>{$error}</li>";
        }
    }
    $html .= '</ul>';
    return $html;
}

function register(array $data): bool
{
    global $db;
    $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetchColumn()) {
        $_SESSION['errors'] = 'Этот email уже занят';
        return false;
    }

    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmt->execute($data);
    $_SESSION['success'] = 'Регистрация прошла успешно';
    return true;
}

function login (array $data):bool
{
    global $db;
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if($row = $stmt->fetch()){
       if(!password_verify($data['password'], $row['password'])){
           $_SESSION['errors'] = 'Не верный пароль';
           return false;
       }
    }else{
        $_SESSION['errors'] = 'Не верное мыло';
        return false;
    }

    foreach ($row as $key=>$value){
        if($key != 'password'){
            $_SESSION['user'][$key] = $value;
        }
    }
    $_SESSION['success'] = 'Авторизация успешна';
    return true;
}

function save_message(array $data):bool
{
    global $db;
    if(!check_auth()){
        $_SESSION['errors'] = "Вы не авторизованны";
        return false;
    }
    $stmt = $db->prepare("INSERT INTO messages (user_id, mess) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user']['id'], $data['message']]);
    $_SESSION['success'] = "Сообщение отправленно на модерацию";
    return true;
}

function edit_message(array $data):bool
{
    global $db;
    if(!check_admin()){
        $_SESSION['errors'] = "Вам запрещенно редактировать сообщения";
        return false;
    }
    $stmt = $db->prepare("UPDATE  messages SET mess = ? WHERE id = ?");
    $stmt->execute([$data['mess'] , $data['id']]);
    $_SESSION['success'] = "Сообщение измененно";
    return true;
}


function get_messages(int $start, int $per_page):array
{
    global $db;
    $where = '';
    if(!check_admin()){
        $where .= 'WHERE status = 1';
    }
    $stmt = $db->prepare("SELECT messages.*, DATE_FORMAT(messages.date_at , '%d.%m.%Y %H:%i') 
    AS date_format, users.name AS user_name FROM messages JOIN users ON users.id = messages.user_id {$where} ORDER BY id DESC LIMIT $start,$per_page");
    $stmt->execute();
    return $stmt->fetchAll();
}
function check_auth():bool
{
    if(isset($_SESSION['user'])){
        return true;
    }
    return false;
}

function check_admin ():bool
{
    if(isset($_SESSION['user']) && $_SESSION['user']['role'] == 2){
        return true;
    }
    return false;
}

function get_count_mess():int
{
    global $db;
    $where = '';
    if(!check_admin()){
        $where .= 'WHERE status = 1';
    }
    $res = $db->query("SELECT COUNT(*) FROM messages {$where}");
    return $res->fetchColumn();

}

function toggle_message_status(int $status , int $id):bool
{
    global $db;
    if(!check_admin()){
        $_SESSION['errors'] = 'Ты не админ';
        return false;
    }
    $status = $status ? 1 : 0;
    $stmt = $db->prepare("UPDATE messages SET status = ? WHERE id = ?");
    return $stmt->execute([$status,$id]);
}
