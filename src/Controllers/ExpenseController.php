<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../Models/Expense.php';

$exp = new Expense();
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    if (isset($_GET['stats'])) {
        echo json_encode([
            'expenses' => $exp->getAll(),
            'byCategory' => $exp->getTotalByCategory(),
            'monthlyTotal' => $exp->getMonthlyTotal()
        ]);
    } else {
        echo json_encode($exp->getAll());
    }
}

elseif ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $desc = $data['description'] ?? '';
    $amount = $data['amount'] ?? '';
    $cat = $data['category'] ?? '';

    if ($desc && $amount && $cat) {
        $ok = $exp->create($desc, (float)$amount, $cat);
        http_response_code(201);
        echo json_encode(['ok' => $ok]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Champs manquants (description, montant ou catégorie)']);
    }
}

elseif ($method == 'DELETE') {
    $input = file_get_contents('php://input');
    parse_str($input, $data);
    $id = $data['id'] ?? 0;

    if ($id) {
        $ok = $exp->delete((int)$id);
        echo json_encode(['ok' => $ok]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'ID manquant pour la suppression']);
    }
}

else {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
}
