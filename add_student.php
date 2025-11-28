<?php
// --- CONFIGURATION ---
$file_path = 'students.json';
$errors = [];
$success_message = '';

// --- 1. CHECK IF FORM WAS SUBMITTED ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 2. GET & VALIDATE FORM DATA ---
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $group = trim($_POST['group']);

    if (empty($student_id)) {
        $errors[] = "Student ID is required.";
    }
    if (!ctype_digit($student_id)) {
        $errors[] = "Student ID must contain only numbers.";
    }
    if (empty($name)) {
        $errors[] = "Student Name is required.";
    }
    // A simple regex to allow letters, spaces, hyphens, and apostrophes in names
    if (!preg_match("/^[a-zA-Z\s'-]+$/", $name)) {
        $errors[] = "Name contains invalid characters.";
    }
    if (empty($group)) {
        $errors[] = "Group is required.";
    }

    // --- 3. LOAD EXISTING STUDENTS (if validation is so far successful) ---
    if (empty($errors)) {
        $students = [];
        if (file_exists($file_path) && is_readable($file_path)) {
            $json_data = file_get_contents($file_path);
            $students = json_decode($json_data, true);

            // Handle bad JSON or empty file
            if ($students === null) {
                $students = [];
            }
        }

        // --- 2.5. FINAL VALIDATION: Check for duplicate ID ---
        foreach ($students as $student) {
            if ($student['student_id'] == $student_id) {
                $errors[] = "A student with ID '$student_id' already exists.";
                break;
            }
        }
    }

    // --- 4. & 5. ADD NEW STUDENT AND SAVE (if all validation passed) ---
    if (empty($errors)) {
        
        // Create a new student entry
        // We also add empty attendance/participation keys to match your app's structure
        $new_student = [
            'student_id' => $student_id,
            'name' => $name,
            'group' => $group,
            'attendance' => (object)[], // Use an object {} not an array []
            'participation' => (object)[]
        ];

        // Add the new student to the array
        $students[] = $new_student;

        // Encode the full array back to JSON
        // JSON_PRETTY_PRINT makes the file easier to read
        $json_data = json_encode($students, JSON_PRETTY_PRINT);

        // Save the data back to the file
        if (file_put_contents($file_path, $json_data)) {
            // 6. Set confirmation message
            $success_message = "Success! Student '$name' (ID: $student_id) has been added to the list.";
        } else {
            $errors[] = "Error: Could not write to '$file_path'. Please check file and folder permissions.";
        }
    }
    // If we are here, $errors[] or $success_message will be set.
} else {
    // Handle cases where someone just browses to add_student.php
    $errors[] = "No form data submitted. Please use the 'Add Student' form.";
}
?>

