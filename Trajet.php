<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');



$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));

if ($segments[0] === 'api' && $segments[1] === 'trajets') {
    
    // GET /api/trajets → Les 5 trajets
    if ($method === 'GET' && count($segments) === 2) {
        echo json_encode(array_values($trajets));
        exit;
    }
    
    // GET /api/trajets/3 → Détail trajet 3
    elseif ($method === 'GET' && count($segments) === 3) {
        $id = (int)$segments[2];
        if (isset($trajets[$id])) {
            echo json_encode($trajets[$id]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Trajet non trouvé']);
        }
        exit;
    }
    
    // POST /api/trajets → Simule création (retourne trajet 1)
    elseif ($method === 'POST' && count($segments) === 2) {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'responsable_agence') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
        echo json_encode(['id' => 1, 'message' => 'Trajet créé (mode démo)']);
        exit;
    }
    
    // PUT /api/trajets/2 → Simule modification
    elseif ($method === 'PUT' && count($segments) === 3) {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'responsable_agence') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
        $id = (int)$segments[2];
        if (isset($trajets[$id])) {
            echo json_encode(['success' => true, 'message' => "Trajet $id modifié (mode démo)"]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Trajet non trouvé']);
        }
        exit;
    }
    
    // DELETE /api/trajets/4 → Simule suppression
    elseif ($method === 'DELETE' && count($segments) === 3) {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'responsable_agence') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
        $id = (int)$segments[2];
        if (isset($trajets[$id])) {
            echo json_encode(['success' => true, 'message' => "Trajet $id supprimé (mode démo)"]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Trajet non trouvé']);
        }
        exit;
    }
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint non trouvé']);
?>
