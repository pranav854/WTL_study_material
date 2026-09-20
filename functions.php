<?php
function validate_material($title, $subject_name, $semester, $material_type, $uploaded_by) {
    $allowed_semesters = [
        "Semester 1", "Semester 2", "Semester 3", "Semester 4",
        "Semester 5", "Semester 6", "Semester 7", "Semester 8"
    ];

    $allowed_types = ["Notes", "Question Paper", "Assignment", "Study Guide"];

    if ($title === "" || $subject_name === "" || $uploaded_by === "") {
        return "Title, Subject Name and Uploaded By are required.";
    }

    if (!in_array($semester, $allowed_semesters, true)) {
        return "Please select a valid semester.";
    }

    if (!in_array($material_type, $allowed_types, true)) {
        return "Please select a valid material type.";
    }

    return "";
}

function save_pdf($file, $title) {
    if (!isset($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"])) {
        return ["success" => false, "message" => "Invalid uploaded file."];
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        return ["success" => false, "message" => "File upload failed."];
    }

    if ($file["size"] > 10 * 1024 * 1024) {
        return ["success" => false, "message" => "PDF must be 10 MB or smaller."];
    }

    $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file["tmp_name"]);

    if ($extension !== "pdf" || $mime !== "application/pdf") {
        return ["success" => false, "message" => "Only PDF files are allowed."];
    }

    $upload_dir = __DIR__ . DIRECTORY_SEPARATOR . "uploads";
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
        return ["success" => false, "message" => "Could not create uploads folder."];
    }

    $safe_title = preg_replace("/[^A-Za-z0-9_-]/", "_", $title);
    $unique_name = $safe_title . "_" . date("Ymd_His") . "_" . bin2hex(random_bytes(4)) . ".pdf";
    $absolute_path = $upload_dir . DIRECTORY_SEPARATOR . $unique_name;

    if (!move_uploaded_file($file["tmp_name"], $absolute_path)) {
        return ["success" => false, "message" => "Could not save uploaded file."];
    }

    return [
        "success" => true,
        "file_path" => "uploads/" . $unique_name,
        "absolute_path" => $absolute_path
    ];
}
?>