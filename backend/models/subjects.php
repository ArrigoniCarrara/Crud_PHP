<?php
function getAllSubjects($conn)
{
    $sql = "SELECT * FROM subjects";
    return $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
}

function getSubjectById($conn, $id)
{
    $sql = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

//Guia refactoring Nro 3 ejercicio 3a:
//Valido que no se intente agregar una materia con el mismo nombre que otra

function getSubjectByName($conn, $name) // Funcion que busca una materia por su nombre
{
    $sql = "SELECT id FROM subjects WHERE name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc(); // Retorna el ID de la materia si existe
}

// EJ 3c) Refactoring Nro3: nueva funcion que verifica si un estudiante esta involucrado en alguna relacion students_subjects
function isSubjectInvolvedInStudents($conn, $subject_id)
{
    $sql = "SELECT COUNT(*) FROM students_subjects WHERE subject_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    return $row[0] > 0; // Retorna true si hay relaciones
}


function createSubject($conn, $name)
{
    $sql = "INSERT INTO subjects (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    return [
        'inserted' => $stmt->affected_rows,
        'id' => $conn->insert_id
    ];
}

function updateSubject($conn, $id, $name)
{
    $sql = "UPDATE subjects SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $id);
    $stmt->execute();
    return ['updated' => $stmt->affected_rows];
}

function deleteSubject($conn, $id)
{
    $sql = "DELETE FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return ['deleted' => $stmt->affected_rows];
}
?>
