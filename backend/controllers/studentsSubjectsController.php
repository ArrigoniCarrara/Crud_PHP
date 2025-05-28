<?php
require_once("./models/studentsSubjects.php");

function handleGet($conn)
{
    $studentsSubjects = getAllSubjectsStudents($conn);
    echo json_encode($studentsSubjects);
}

function handlePost($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['student_id'], $input['subject_id'], $input['approved'])) {
        http_response_code(400); 
        echo json_encode(["error" => "Datos incompletos para la asignación."]);
        return;
    }

    $student_id = $input['student_id'];
    $subject_id = $input['subject_id'];
    $approved = $input['approved'];

    // // EJ 3a) de refactoring Nro 3  Validación para evitar relaciones estudiante-materia duplicadas
    $existingRelation = checkStudentSubjectExists($conn, $student_id, $subject_id); // Uso de la nueva función
    if ($existingRelation) { //
        http_response_code(409); 
        echo json_encode(["error" => "Esta relación estudiante-materia ya existe."]); //
        return; //
    }

    $result = assignSubjectToStudent($conn, $student_id, $subject_id, $approved);
    if ($result['inserted'] > 0)
    {
        echo json_encode(["message" => "Asignación realizada correctamente."]);
    }
    else
    {
        http_response_code(500); 
        echo json_encode(["error" => "Error al asignar la materia al estudiante."]);
    }
}

function handlePut($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'], $input['student_id'], $input['subject_id'], $input['approved']))
    {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos para la actualización."]);
        return;
    }

    $id = $input['id'];
    $student_id = $input['student_id'];
    $subject_id = $input['subject_id'];
    $approved = $input['approved'];

    $result = updateStudentSubject($conn, $id, $student_id, $subject_id, $approved);
    if ($result['updated'] > 0)
    {
        echo json_encode(["message" => "Actualización correcta."]);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar la relación."]);
    }
}

function handleDelete($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "El ID de la relación es requerido para eliminar."]);
        return;
    }

    $result = removeStudentSubject($conn, $input['id']);
    if ($result['deleted'] > 0)
    {
        echo json_encode(["message" => "Relación eliminada correctamente."]);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
