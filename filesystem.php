<?php


$file_name = './files/users.csv';

ini_set('auto_detect_line_endings', TRUE);
$f = fopen($file_name, 'r');

$host = 'localhost';
$db   = 'ifoa_filesystem';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);

while ($data = fgetcsv($f, null, "\t")) { // istruzione che applicata ad ogni riga riversa nella var data il suo contentuto 
    $stmt = $pdo->prepare('INSERT INTO users (username, mail, password) VALUES(:username, :mail, :password)');

    $stmt->execute([
        'username' => $data[1],
        'mail' => $data[2],
        'password' => $data[3],
    ]);
}

fclose($f);
ini_set('auto_detect_line_endings', FALSE);

// crea un file csv

$user_db = false;

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id;");

    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
    ]);

    $user_db = $stmt->fetch();
};

$stmt = $pdo->query('SELECT * FROM users');

$users = $stmt->fetchAll();

print_r($users);

$file_name_new = 'files/users-new.csv';
$file_handle = fopen($file_name_new, 'w');

fputcsv($file_handle, array_keys($users[0]), "\t");

foreach ($users as $index => $row) {
    // if ($index === 0) fputcsv($file_handle, array_keys($row), "\t");
    fputcsv($file_handle, $row, "\t");
}

fclose($file_handle);
