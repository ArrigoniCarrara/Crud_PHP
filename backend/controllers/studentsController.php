<?php
require_once("./models/students.php");
require_once("./models/studentsSubjects.php"); 

function handleGet($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['id']))
    {
        $student = getStudentById($conn, $input['id']);
        echo json_encode($student);
    }
    else
    {
        $students = getAllStudents($conn);
        echo json_encode($students);
    }
}

function handlePost($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = createStudent($conn, $input['fullname'], $input['email'], $input['age']);
    if ($result['inserted'] > 0)
    {
        echo json_encode(["message" => "Estudiante agregado correctamente"]);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo agregar"]);
    }
}

function handlePut($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    $result = updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age']);
    if ($result['updated'] > 0)
    {
        echo json_encode(["message" => "Actualizado correctamente"]);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn)
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "El ID del estudiante es requerido para eliminar."]);
        return;
    }

    $student_id = $input['id'];

    // No permitir eliminar si el estudiante tiene relaciones en students_subjects
    if (isStudentInvolvedInSubjects($conn, $student_id)) { // Uso de la nueva función
        http_response_code(409); 
        echo json_encode(["error" => "No se puede eliminar el estudiante porque está asignado a una o más materias."]);
        return;
    }

    $result = deleteStudent($conn, $student_id);
    if ($result['deleted'] > 0)
    {
        echo json_encode(["message" => "Estudiante eliminado correctamente"]);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>