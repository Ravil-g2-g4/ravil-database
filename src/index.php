<?php

declare(strict_types=1);

// ФУНКЦИИ
function dbSource(): string {

    require_once 'vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    return $_ENV['DB_SOURCE'];
}

function readDataBase(string $dbSource, ?object $mysql = NULL): void {

    if ($dbSource === 'json') {

        $json = file_get_contents('database.json');
        $dataBase = json_decode($json, true);

        print_r($dataBase);
    }

    elseif ($dbSource === 'mysql') {

        $mysql = new mysqli(hostname: "8092a0bbc26b", username: "root",password: "root",database: "test",port: 3306);

        $sql = 'SELECT * FROM users;';
        $result = $mysql->query($sql);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

            print_r($row);
        }


    }
}

function writingDataBase(string $dbSource): void {

    if ($dbSource === 'json') {

        // получаем данные о новом пользователе
        $name = readline('Имя нового пользователя: ');
        $surname = readline('Фамилия нового пользователя: ');
        $email = readline('Почта нового пользователя: ');

        // убираем случайные пробелы
        $name = trim($name);
        $surname = trim($surname);
        $email = trim($email);

        // определяем id нового пользователя
        $json = file_get_contents('database.json');
        $dataBase = json_decode($json, true);
        $id = 'id_' . array_key_last($dataBase) + 1;

        // подготавливаем и записываем данные в json
        $newUser = ['name' => $name, 'surname' => $surname, 'email' => $email, 'id' => $id];
        $json = file_get_contents('database.json');
        $dataBase = json_decode($json, true);
        $dataBase[] = $newUser;

        file_put_contents('database.json', json_encode($dataBase, JSON_PRETTY_PRINT));
    }

    elseif ($dbSource === 'mysql') {

        $mysql = new mysqli(hostname: "8092a0bbc26b", username: "root",password: "root",database: "test",port: 3306);

        // определяем id нового пользователя
        $sql = 'SELECT * FROM users WHERE id = (SELECT MAX(id) FROM users);';
        $result = $mysql->query($sql);
        $result = $result->fetch_array(MYSQLI_ASSOC);
        $id = $result['id'] + 1;

        // получаем данные о новом пользователе
        $name = readline('Имя нового пользователя: ');
        $surname = readline('Фамилия нового пользователя: ');
        $email = readline('Почта нового пользователя: ');

        // убираем случайные пробелы
        $name = trim($name);
        $surname = trim($surname);
        $email = trim($email);


        // делаем запись через подготовленный запрос
        $sql = 'INSERT users(id, name, surname, email) VALUES(?, ?, ?, ?)';
        $stmt = $mysql->prepare($sql);
        $stmt->bind_param('isss',$id, $name, $surname, $email);
        $stmt->execute();
    }
}

function deleteUserDataBase(string $dbSource): void {

    if ($dbSource === 'json') {

        $json = file_get_contents('database.json');
        $dataBase = json_decode($json, true);
        $flag = false;


        while ($flag === false) {

            $choiceDelete = readline('Вы знаете id или по почту? (id/email): ');
            $choiceDelete = trim($choiceDelete);

            if ($choiceDelete === 'id') {

                // удаляем по id
                $id = readline('Введите id пользователя, которого хотите удалить: ');
                $id = trim($id);
                unset($dataBase[$id]);

                $flag = true;

            } elseif ($choiceDelete === 'email') {

                // удаляем по email
                $email = readline('Введите почту пользователя: ');
                $email = trim($email);

                $index = array_search($email, array_column($dataBase, 'email', 'id'));
                $index = substr($index, 3);
                unset($dataBase[$index]);

                $flag = true;
            } else {

                echo 'Вы ввели некорректный ответ на вопрос. Попробуйте повторить попытку.';
            }
        }

        file_put_contents('database.json', json_encode($dataBase, JSON_PRETTY_PRINT));
    }

    elseif ($dbSource === 'mysql') {

        $mysql = new mysqli(hostname: "8092a0bbc26b", username: "root",password: "root",database: "test",port: 3306);

        $flag = false;
        while ($flag === false) {

            $choiceDelete = readline('Вы знаете id или по почту? (id/email): ');
            $choiceDelete = trim($choiceDelete);

            if ($choiceDelete === 'id') {

                // удаляем по id
                $id = readline('Введите id пользователя, которого хотите удалить: ');
                $id = trim($id);

                $sql = 'DELETE FROM users WHERE id = ?';
                $stmt = $mysql->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();

                $flag = true;

            } elseif ($choiceDelete === 'email') {

                // удаляем по email
                $email = readline('Введите почту пользователя: ');
                $email = trim($email);

                $sql = 'DELETE FROM users WHERE email = ?';
                $stmt = $mysql->prepare($sql);
                $stmt->bind_param('s', $email);
                $stmt->execute();

                $flag = true;
            } else {

                echo 'Вы ввели некорректный ответ на вопрос. Попробуйте повторить попытку.';
            }
        }
    }
}


// ОСНОВНАЯ ПРОГРАММА

$dbSource = dbSource();

if ($dbSource === 'mysql') {

    // подключаемся к БД
    $mysql = new mysqli(hostname: "8092a0bbc26b", username: "root",password: "root",database: "test",port: 3306);

    // проверяем подключение
    if ($mysql->connect_error) {
        echo 'Ошибка подключения: ' . $mysql->connect_error . PHP_EOL;
    }
    else {
        echo 'Подключение успешно установлено!' . PHP_EOL;
    }
}


$flag = true;
while ($flag === true) {

    $goodChoice = false;
    while ($goodChoice === false) {
        echo <<<EOT
Что Вы хотите сделать с базой данных о пользователях?
Выберите номер действия:
1. Показать список пользователей;
2. Добавить пользователя;
3. Удалить пользователя.

EOT;

        $action = readline('Ваш выбор: ');
        $action = trim($action);

        switch ($action) {
            case '1':
                readDataBase($dbSource);
                $goodChoice = true;
                break;
            case '2':
                writingDataBase($dbSource);
                $goodChoice = true;
                break;
            case '3':
                deleteUserDataBase($dbSource);
                $goodChoice = true;
                break;
            default:
                $goodChoice = false;
                echo PHP_EOL . 'Вы ввели некорректный ответ на вопрос. Попробуйте повторить попытку.' . PHP_EOL . PHP_EOL;
                break;
        }
    }

    $check = readline('Вы закончили работу с базой данных? (y/n): ');
    $check = trim($check);
    if ($check === 'y') {
        $flag = false;
    }
}

