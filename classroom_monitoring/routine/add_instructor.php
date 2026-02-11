<?php
include '../db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $designation = $_POST['designation'];
    $contact = $_POST['contact'];

    try {
        $stmt = $pdo->prepare("INSERT INTO instructors (name, email, designation, contact) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $designation, $contact]);
        $message = "✅ Instructor added successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Instructor</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200 p-8">
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Add Instructor</h2>

    <?php if ($message): ?>
        <p class="mb-4 text-blue-700 font-semibold"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label class="block mb-2">Name:</label>
        <input type="text" name="name" required class="w-full mb-4 px-3 py-2 border rounded" />

        <label class="block mb-2">Persnal No.:</label>
        <input type="text" name="email" required class="w-full mb-4 px-3 py-2 border rounded" />

        <label class="block mb-2">Designation/ Subject:</label>
        <input type="text" name="designation" required class="w-full mb-4 px-3 py-2 border rounded" />

        <label class="block mb-2">Contact:</label>
        <input type="text" name="contact" required class="w-full mb-4 px-3 py-2 border rounded" /><br>
		
		<div class="flex justify-between  ">

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Instructor</button>
		<button type="reset" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-red-700">Reset</button>
		</div><br>
        <a href="admin_dashboard.php" class="ml-4 text-sm text-gray-700 underline">← Back</a>
    </form>
</div>
</body>
</html>
