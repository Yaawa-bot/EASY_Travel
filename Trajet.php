<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 5 TRAJETS FIXES POUR SIMULER LA RÉPONSE 
$trajets = [
    1 => [
        'id_trajet' => 1,
        'ville_depart' => 'Abidjan',
        'destination' => 'Yamoussoukro', 
        'date_depart' => '2026-03-01',
        'heure_depart' => '07:45',
        'duree' => '3h',
        'numero_bus' => 'BUS-001'
    ],
    2 => [
        'id_trajet' => 2,
        'ville_depart' => 'Abidjan', 
        'destination' => 'Bouaké',
        'date_depart' => '2026-03-02',
        'heure_depart' => '09:00',
        'duree' => '2h',
        'numero_bus' => 'BUS-002'
    ],
    3 => [
        'id_trajet' => 3,
        'ville_depart' => 'Abidjan',
        'destination' => 'San Pedro',
        'date_depart' => '2026-03-03',
        'heure_depart' => '14:00',
        'duree' => '1h30',
        'numero_bus' => 'BUS-003'
    ],
    4 => [
        'id_trajet' => 4,
        'ville_depart' => 'Abidjan',
        'destination' => 'Daloa',
        'date_depart' => '2026-03-04',
        'heure_depart' => '09:30',
        'duree' => '1h45',
        'numero_bus' => 'BUS-004'
    ],
    5 => [
        'id_trajet' => 5,
        'ville_depart' => 'Abidjan',
        'destination' => 'Korhogo',
        'date_depart' => '2026-03-05',
        'heure_depart' => '12:00',
        'duree' => '2h30',
        'numero_bus' => 'BUS-005'
    ]
];

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
