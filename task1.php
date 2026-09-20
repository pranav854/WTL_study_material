<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Material Manager</title>
</head>
<body>
    <header>
        <h1>Study Material Manager</h1>
    </header>

    <section>
        <h2>Upload Material</h2>
        <form>
            <label>Title:</label>
            <input type="text" name="title">

            <label>Subject Name:</label>
            <input type="text" name="subject_name">

            <label>Semester:</label>
            <select name="semester">
                <option value="">Select Semester</option>
                <option>Semester 1</option><option>Semester 2</option>
                <option>Semester 3</option><option>Semester 4</option>
                <option>Semester 5</option><option>Semester 6</option>
                <option>Semester 7</option><option>Semester 8</option>
            </select>

            <label>Material Type:</label>
            <select name="material_type">
                <option value="">Select Type</option>
                <option>Notes</option><option>Question Paper</option>
                <option>Assignment</option><option>Study Guide</option>
            </select>

            <label>Uploaded By:</label>
            <input type="text" name="uploaded_by">

            <label>File:</label>
            <input type="file" name="file">

            <button type="submit">Submit</button>
        </form>
    </section>

    <section>
        <h2>Material List</h2>
        <table border="1">
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
                <tr>
                    <td>Example</td><td>WTL</td><td>Semester 5</td>
                    <td>Notes</td><td>Student</td><td>sample.pdf</td>
                    <td><button>Edit</button> <button>Delete</button></td>
                </tr>
            </tbody>
        </table>
    </section>

    <div hidden>
        <h2>Edit Material</h2>
        <form>
            <!-- Same fields as upload form -->
            <input type="text" name="title">
            <input type="text" name="subject_name">
            <select name="semester"><option>Select Semester</option></select>
            <select name="material_type"><option>Select Type</option></select>
            <input type="text" name="uploaded_by">
            <input type="file" name="file">
            <button type="submit">Save Changes</button>
        </form>
    </div>

    <div hidden>
        <h2>Delete Material</h2>
        <p>Are you sure?</p>
        <button>Yes</button>
        <button>No</button>
    </div>
</body>
</html>