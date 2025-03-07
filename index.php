<?php

declare(strict_types=1);
function readDataBase(): void {

    $json = file_get_contents('database.json');
    $dataBase = json_decode($json, true);

    print_r($dataBase);
}

function writingDataBase(): void {

    $name = readline('Имя нового пользователя: ');
    $surname = readline('Фамилия нового пользователя: ');
    $email = readline('Почта нового пользователя: ');

    $json = file_get_contents('database.json');
    $dataBase = json_decode($json, true);
    $id = 'id_' . array_key_last($dataBase) + 1;

    $newUser = ['name' => $name, 'surname' => $surname, 'email' => $email, 'id' => $id];
    $json = file_get_contents('database.json');
    $dataBase = json_decode($json, true);
    $dataBase[] = $newUser;

    file_put_contents('database.json', json_encode($dataBase, JSON_PRETTY_PRINT));
}

function deleteUserDataBase(): void {

    $json = file_get_contents('database.json');
    $dataBase = json_decode($json, true);
    $flag = false;


    while ($flag === false) {

        $choiceDelete = readline('Вы знаете id или по почту? (id/email): ');

        if ($choiceDelete === 'id') {

            $id = readline('Введите id пользователя, которого хотите удалить: ');
            unset($dataBase[$id]);

            $flag = true;


        } elseif ($choiceDelete === 'email') {

            $email = readline('Введите почту пользователя: ');

            $index = array_search($email, array_column($dataBase, 'email', 'id'));
            $index = substr($index, 3);
            unset($dataBase[$index]);

            $flag = true;
            }

        else {

            echo 'Вы ввели некорректный ответ на вопрос. Попробуйте повторить попытку.';
        }
    }

    file_put_contents('database.json', json_encode($dataBase, JSON_PRETTY_PRINT));
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

        switch ($action) {
            case '1':
                readDataBase();
                $goodChoice = true;
                break;
            case '2':
                writingDataBase();
                $goodChoice = true;
                break;
            case '3':
                deleteUserDataBase();
                $goodChoice = true;
                break;
            default:
                $goodChoice = false;
                echo PHP_EOL . 'Вы ввели некорректный ответ на вопрос. Попробуйте повторить попытку.' . PHP_EOL . PHP_EOL;
                break;
        }
    }

    $check = readline('Вы закончили работу с базой данных? (y/n): ');
    if ($check === 'y') {
        $flag = false;
    }
}

