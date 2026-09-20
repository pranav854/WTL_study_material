<?php
require_once "db.php";
require_once "functions.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "create") {
        $title = trim($_POST["title"] ?? "");
        $subject_name = trim($_POST["subject_name"] ?? "");
        $semester = trim($_POST["semester"] ?? "");
        $material_type = trim($_POST["material_type"] ?? "");
        $uploaded_by = trim($_POST["uploaded_by"] ?? "");

        $validation = validate_material($title, $subject_name, $semester, $material_type, $uploaded_by);
        if ($validation !== "") {
            $message = $validation;
            $message_type = "error";
        } elseif (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
            $message = "Please upload a PDF file.";
            $message_type = "error";
        } else {
            $upload = save_pdf($_FILES["file"], $title);
            if (!$upload["success"]) {
                $message = $upload["message"];
                $message_type = "error";
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO materials (title, subject_name, semester, material_type, uploaded_by, file_path)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->bind_param("ssssss", $title, $subject_name, $semester, $material_type, $uploaded_by, $upload["file_path"]);

                if ($stmt->execute()) {
                    $message = "Material uploaded successfully.";
                    $message_type = "success";
                } else {
                    if (is_file($upload["absolute_path"])) unlink($upload["absolute_path"]);
                    $message = "Database error while saving material.";
                    $message_type = "error";
                }
                $stmt->close();
            }
        }
    }

    if ($action === "update") {
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
        $title = trim($_POST["title"] ?? "");
        $subject_name = trim($_POST["subject_name"] ?? "");
        $semester = trim($_POST["semester"] ?? "");
        $material_type = trim($_POST["material_type"] ?? "");
        $uploaded_by = trim($_POST["uploaded_by"] ?? "");

        if (!$id) {
            $message = "Invalid material ID.";
            $message_type = "error";
        } else {
            $validation = validate_material($title, $subject_name, $semester, $material_type, $uploaded_by);
            if ($validation !== "") {
                $message = $validation;
                $message_type = "error";
            } else {
                $old_file = "";
                $stmt = $conn->prepare("SELECT file_path FROM materials WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $old_file = $row["file_path"];
                } else {
                    $message = "Material not found.";
                    $message_type = "error";
                }
                $stmt->close();

                if ($message_type !== "error") {
                    $new_file_path = $old_file;
                    $new_absolute = "";

                    if (isset($_FILES["file"]) && $_FILES["file"]["error"] !== UPLOAD_ERR_NO_FILE) {
                        if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
                            $message = "There was a problem uploading the new PDF.";
                            $message_type = "error";
                        } else {
                            $upload = save_pdf($_FILES["file"], $title);
                            if (!$upload["success"]) {
                                $message = $upload["message"];
                                $message_type = "error";
                            } else {
                                $new_file_path = $upload["file_path"];
                                $new_absolute = $upload["absolute_path"];
                            }
                        }
                    }

                    if ($message_type !== "error") {
                        $stmt = $conn->prepare(
                            "UPDATE materials
                             SET title = ?, subject_name = ?, semester = ?, material_type = ?, uploaded_by = ?, file_path = ?
                             WHERE id = ?"
                        );
                        $stmt->bind_param(
                            "ssssssi",
                            $title, $subject_name, $semester, $material_type, $uploaded_by, $new_file_path, $id
                        );

                        if ($stmt->execute()) {
                            if ($new_absolute !== "" && $old_file !== "") {
                                $old_absolute = __DIR__ . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $old_file);
                                if (is_file($old_absolute)) unlink($old_absolute);
                            }
                            $message = "Material updated successfully.";
                            $message_type = "success";
                        } else {
                            if ($new_absolute !== "" && is_file($new_absolute)) unlink($new_absolute);
                            $message = "Database error while updating material.";
                            $message_type = "error";
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }

    if ($action === "delete") {
        $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

        if (!$id) {
            $message = "Invalid material ID.";
            $message_type = "error";
        } else {
            $file_path = "";
            $stmt = $conn->prepare("SELECT file_path FROM materials WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $file_path = $row["file_path"];
            }
            $stmt->close();

            $stmt = $conn->prepare("DELETE FROM materials WHERE id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                if ($file_path !== "") {
                    $absolute = __DIR__ . DIRECTORY_SEPARATOR . str_replace("/", DIRECTORY_SEPARATOR, $file_path);
                    if (is_file($absolute)) unlink($absolute);
                }
                $message = "Material deleted successfully.";
                $message_type = "success";
            } else {
                $message = "Material could not be deleted.";
                $message_type = "error";
            }
            $stmt->close();
        }
    }
}

$result = $conn->query("SELECT id, title, subject_name, semester, material_type, uploaded_by, file_path FROM materials ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Material Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header class="topbar">
        <h1>Study Material Manager</h1>
        <p>Upload and manage academic materials</p>
    </header>

    <?php if ($message !== ""): ?>
        <div class="message <?= htmlspecialchars($message_type) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <section class="card">
        <h2>Upload Material</h2>
        <form id="materialForm" method="POST" enctype="multipart/form-data" action="index.php">
            <input type="hidden" name="action" value="create">

            <label>Title</label>
            <input type="text" name="title" required>

            <label>Subject Name</label>
            <input type="text" name="subject_name" required>

            <label>Semester</label>
            <select name="semester" required>
                <option value="">Select Semester</option>
                <option>Semester 1</option><option>Semester 2</option>
                <option>Semester 3</option><option>Semester 4</option>
                <option>Semester 5</option><option>Semester 6</option>
                <option>Semester 7</option><option>Semester 8</option>
            </select>

            <label>Material Type</label>
            <select name="material_type" required>
                <option value="">Select Type</option>
                <option>Notes</option><option>Question Paper</option>
                <option>Assignment</option><option>Study Guide</option>
            </select>

            <label>Uploaded By</label>
            <input type="text" name="uploaded_by" required>

            <label>File (PDF only)</label>
            <input type="file" name="file" accept=".pdf,application/pdf" required>

            <button type="submit">Submit</button>
        </form>
    </section>

    <section class="card">
        <div class="table-heading">
            <h2>Material List</h2>
            <!-- Extra feature: search/filter -->
            <input type="search" id="searchBox" placeholder="Search materials...">
        </div>

        <table id="materialTable">
            <thead>
            <tr>
                <th>Title</th>
                <th>Subject</th>
                <th>Semester</th>
                <th>Type</th>
                <th>Uploaded By</th>
                <th>File</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["title"]) ?></td>
                    <td><?= htmlspecialchars($row["subject_name"]) ?></td>
                    <td><?= htmlspecialchars($row["semester"]) ?></td>
                    <td><?= htmlspecialchars($row["material_type"]) ?></td>
                    <td><?= htmlspecialchars($row["uploaded_by"]) ?></td>
                    <td><a href="<?= htmlspecialchars($row["file_path"]) ?>" target="_blank">View PDF</a></td>
                    <td class="actions">
                        <button type="button" class="edit-btn"
                            data-id="<?= (int)$row["id"] ?>"
                            data-title="<?= htmlspecialchars($row["title"], ENT_QUOTES) ?>"
                            data-subject="<?= htmlspecialchars($row["subject_name"], ENT_QUOTES) ?>"
                            data-semester="<?= htmlspecialchars($row["semester"], ENT_QUOTES) ?>"
                            data-type="<?= htmlspecialchars($row["material_type"], ENT_QUOTES) ?>"
                            data-uploaded="<?= htmlspecialchars($row["uploaded_by"], ENT_QUOTES) ?>">
                            Edit
                        </button>

                        <button type="button" class="delete-btn" data-id="<?= (int)$row["id"] ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

<!-- Edit popup -->
<div id="editModal" class="modal hidden">
    <div class="modal-box">
        <button type="button" class="close" id="closeEdit">&times;</button>
        <h2>Edit Material</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="editId">

            <label>Title</label>
            <input type="text" name="title" id="editTitle" required>

            <label>Subject Name</label>
            <input type="text" name="subject_name" id="editSubject" required>

            <label>Semester</label>
            <select name="semester" id="editSemester" required>
                <option value="">Select Semester</option>
                <option>Semester 1</option><option>Semester 2</option>
                <option>Semester 3</option><option>Semester 4</option>
                <option>Semester 5</option><option>Semester 6</option>
                <option>Semester 7</option><option>Semester 8</option>
            </select>

            <label>Material Type</label>
            <select name="material_type" id="editType" required>
                <option value="">Select Type</option>
                <option>Notes</option><option>Question Paper</option>
                <option>Assignment</option><option>Study Guide</option>
            </select>

            <label>Uploaded By</label>
            <input type="text" name="uploaded_by" id="editUploaded" required>

            <label>Replace File (optional, PDF only)</label>
            <input type="file" name="file" accept=".pdf,application/pdf">

            <button type="submit">Save Changes</button>
        </form>
    </div>
</div>

<!-- Delete confirmation popup -->
<div id="deleteModal" class="modal hidden">
    <div class="modal-box small">
        <h2>Delete Material</h2>
        <p>Are you sure?</p>
        <form method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deleteId">
            <button type="submit">Yes, Delete</button>
            <button type="button" id="cancelDelete">Cancel</button>
        </form>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
