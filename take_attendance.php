<?php
$file = "students.json";

// === 1. Load students ===
if (!file_exists($file)) {
    die("<h2>❌ No students found. Add students first.</h2>");
}

$students = json_decode(file_get_contents($file), true);

// === 2. If form submitted: save attendance ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $date = date("Y-m-d");
    $attendance_file = "attendance_" . $date . ".json";

    // Already exists?
    if (file_exists($attendance_file)) {
        die("<h2>⚠ Attendance for today has already been taken.</h2>");
    }

    $attendance_data = [];

    foreach ($students as $stu) {
        $id = $stu["student_id"];
        $status = $_POST["attendance_$id"] ?? "absent";

        $attendance_data[] = [
            "student_id" => $id,
            "status" => $status
        ];
    }

    file_put_contents($attendance_file, json_encode($attendance_data, JSON_PRETTY_PRINT));

    echo "<h2>✅ Attendance saved for $date</h2>";
    echo "<a href='take_attendance.php'>Back</a>";
    exit;
}
?>

<!-- === 3. Attendance Form === -->
<h2>Take Attendance</h2>

<form method="POST">
<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Group</th>
        <th>Status</th>
    </tr>

    <?php foreach ($students as $stu): ?>
        <tr>
            <td><?= $stu["student_id"] ?></td>
            <td><?= $stu["name"] ?></td>
            <td><?= $stu["group"] ?></td>
            <td>
                <label>
                    <input type="radio" name="attendance_<?= $stu["student_id"] ?>" value="present" checked>
                    Present
                </label>

                <label>
                    <input type="radio" name="attendance_<?= $stu["student_id"] ?>" value="absent">
                    Absent
                </label>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<br>
<button type="submit">Save Attendance</button>
</form>