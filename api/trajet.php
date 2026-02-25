<?php

// use __DIR__ to construct the path relative to this file, avoiding issues when
// the script is called from a different working directory
require_once __DIR__ . '/../db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');



$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// remove any project folder or base path before the API route
$segments = explode('/', trim($path, '/'));

// if the URI contains "api", drop everything up through it
$apiPos = array_search('api', $segments, true);
if ($apiPos !== false) {
    // segments after "api" are what we care about
    $segments = array_slice($segments, $apiPos + 1);
}

// normalize the resource name: strip any ".php" extension so that
// calling the script directly still works (e.g. /api/trajet.php)
$resource = isset($segments[0]) ? preg_replace('/\.php$/', '', $segments[0]) : '';

// now $resource should equal "trajets" for our endpoints
if ($resource === 'trajets') {
    
    // GET /api/trajets → TOUS les trajets depuis la BD
    // after slicing to the segment following "api" the array contains
    // ['trajets'] or ['trajets', '3']
    if ($method === 'GET' && count($segments) === 1) {
        try {
            $stmt = $pdo->query("SELECT * FROM trajets ORDER BY id_trajet");
            $trajets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(array_values($trajets));  // array_values pour réindexer
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
        }
        exit;
    }
    
    // GET /api/trajets/3 → Détail trajet 3 depuis la BD
    elseif ($method === 'GET' && count($segments) === 2) {
        $id = (int)$segments[1];
        try {
            $stmt = $pdo->prepare("SELECT * FROM trajets WHERE id_trajet = ?");
            $stmt->execute([$id]);
            $trajet = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
            exit;
        }
        
        if ($trajet) {
            echo json_encode($trajet);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Trajet non trouvé']);
        }
        exit;
    }
    
    // POST /api/trajets → CRÉATION REELLE dans la BD
    elseif ($method === 'POST' && count($segments) === 1) {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'responsable_agence') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        try {
            $stmt = $pdo->prepare("INSERT INTO trajets (ville_depart, destination, date_depart, heure_depart, duree, numero_bus) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $input['ville_depart'] ?? '',
                $input['destination'] ?? '',
                $input['date_depart'] ?? '0000-00-00',
                $input['heure_depart'] ?? '00:00:00',
                $input['duree'] ?? 0,
                $input['numero_bus'] ?? 0
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
            exit;
        }
        
        if ($result) {
            $newId = $pdo->lastInsertId();
            echo json_encode(['id' => $newId, 'message' => 'Trajet créé avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur création trajet']);
        }
        exit;
    }
    
    // PUT /api/trajets/2 → MODIFICATION REELLE dans la BD
    elseif ($method === 'PUT' && count($segments) === 2) {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'responsable_agence') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
        
        $id = (int)$segments[2];
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            $stmt = $pdo->prepare("UPDATE trajets SET ville_depart=?, destination=?, date_depart=?, heure_depart=?, duree=?, numero_bus=? WHERE id_trajet=?");
            $result = $stmt->execute([
                $input['ville_depart'] ?? '',
                $input['destination'] ?? '',
                $input['date_depart'] ?? '0000-00-00',
                $input['heure_depart'] ?? '00:00:00',
                $input['duree'] ?? 0,
                $input['numero_bus'] ?? 0,
                $id
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
            exit;
        }
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => "Trajet $id modifié"]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Trajet non trouvé']);
        }
        exit;
    }
    
    // DELETE /api/trajets/4 → SUPPRESSION REELLE dans la BD
    elseif ($method === 'DELETE' && count($segments) === 2) {
        session_start();
        if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'responsable_agence') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
        
        $id = (int)$segments[2];
        try {
            $stmt = $pdo->prepare("DELETE FROM trajets WHERE id_trajet = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error', 'details' => $e->getMessage()]);
            exit;
        }
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => "Trajet $id supprimé"]);
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
