<?php
require_once("./models/subjects.php");

function handleGet($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);
    if (isset($input['id']))
    {
        $subject = getSubjectById($conn, $input['id']);
        echo json_encode($subject);
    }
    else
    {
        $subjects = getAllSubjects($conn);
        echo json_encode($subjects);
    }
}

function handlePost($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['name']) || empty(trim($input['name']))) {
        http_response_code(400); 
        echo json_encode(["error" => "El nombre de la materia es requerido."]);
        return;
    }

    $name = trim($input['name']);

    // Ej 3a) Refactoring Nro 3: 
    // Validación para evitar nombres duplicados antes de crear
    $existingSubject = getSubjectByName($conn, $name); // Se usa la nueva función

    if ($existingSubject) {
        http_response_code(409); // Conflicto (indica que el nombre de la materia ya esta "usado")
        echo json_encode(["error" => "Ya existe una materia con este nombre."]);
        return;
    }

    $result = createSubject($conn, $name);
    if ($result['inserted'] > 0)
    {
        echo json_encode(["message" => "Materia creada correctamente."]);
    }
    else
    {
        http_response_code(500); 
        echo json_encode(["error" => "No se pudo crear la materia."]);
    }
}

function handlePut($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    
    if (!isset($input['id'], $input['name']) || empty(trim($input['name']))) {
        http_response_code(400); 
        echo json_encode(["error" => "ID y nombre de materia son requeridos."]);
        return;
    }

    $id = $input['id'];
    $name = trim($input['name']);

    
    $currentSubject = getSubjectById($conn, $id);
    if (!$currentSubject) {
        http_response_code(404); 
        echo json_encode(["error" => "Materia no encontrada para actualizar."]);
        return;
    }

    $result = updateSubject($conn, $id, $name);
    if ($result['updated'] > 0)
    {
        echo json_encode(["message" => "Materia actualizada correctamente."]);
    }
    else
    {
        http_response_code(500); 
        echo json_encode(["error" => "No se pudo actualizar la materia."]);
    }
}

function handleDelete($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'])) {
        http_response_code(400); 
        echo json_encode(["error" => "El ID de la materia es requerido para eliminar."]);
        return;
    }

    $subject_id = $input['id'];

    // No permitir eliminar si la materia tiene relaciones en students_subjects
    if (isSubjectInvolvedInStudents($conn, $subject_id)) { // Uso de la nueva función
        http_response_code(409); 
        echo json_encode(["error" => "No se puede eliminar la materia porque está asignada a uno o más estudiantes."]);
        return;
    }

    $result = deleteSubject($conn, $subject_id);
    if ($result['deleted'] > 0)
    {
        echo json_encode(["message" => "Materia eliminada correctamente."]);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar la materia."]);
    }
}
?>