<?php
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['role'], ['user', 'admin'])) {

    header("Location: login.php");
    exit();
}

$baseDir = 'uploads/';
$folder = $_GET['folder'] ?? '';
$folder = str_replace('..', '', $folder); // prevent directory traversal
$currentDir = 'uploads/' . $folder;


if (!is_dir($currentDir)) {
    die("Folder not found.");
}

$items = array_diff(scandir($currentDir), ['.', '..']);

function getIcon($file) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $icons = [
        'pdf' => '📄', 'doc' => '📝', 'docx' => '📝',
        'ppt' => '📊', 'pptx' => '📊',
        'jpg' => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️',
        'mp3' => '🎵', 'mp4' => '🎞️',
        'zip' => '🗜️', 'txt' => '📃'
    ];
    return $icons[$ext] ?? '📁';
}
$userRole = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($folder) ?> - Folder View</title>
    <link rel="stylesheet" href="css/style.css?v=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        nav {
            background-color: #0b3d91;
            padding: 10px;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        .container {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
			color: #020604;
			font-weight: bold;
			text-transform: uppercase;
			font-family: 'Times New Roman', Times, serif;
			font-size: 20px;
        }
        .icon {
            font-size: 20px;
        }
		footer {
            background-color: #002147;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<nav>
    <a href="index.php">Home</a>
	<?php if ($userRole === 'admin'): ?>
		<a href="upload.php">Upload</a>
		<a href="browse.php">Browse Files</a>
		<a href="create_folder.php">Create Folder</a>
	<?php endif; ?>
    <a href="php/logout.php">Logout</a>
</nav>

<div class="container">
    <h2>FOLDER NAME: <?= htmlspecialchars($folder) ?></h2>
	<h3>UPLOAD, DRAG & DROP, CREATE SUBFOLDERS, ACCESS FILES</h3>

	<div style="display: flex; gap: 80px; align-items: flex-start; margin-bottom: 30px; flex-wrap: wrap;">

		<!-- Upload Form -->
		<div class="upload-form-container" style="border: 1px solid #ccc; padding: 10px; border-radius: 8px;">
			<form id="uploadForm" enctype="multipart/form-data">
				<input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
				<label><strong>Upload File</strong></label><br>
				<input type="file" name="file[]" multiple><br>
				<button type="submit">UPLOAD</button>
			</form>
		</div>

		<!-- Drag and Drop Area -->
		<div id="dropArea" style="border: 2px dashed #888; padding: 60px; border-radius: 8px; text-align: center; height: 230px; width: 320px;">
			<strong>Drag & Drop Files Here</strong><br><br>
			<p style="font-size: 14px; color: #444;">Or just drop files into this box</p>
		</div>

		<!-- Create Subfolder -->
		<div style="border: 1px solid #ccc; padding: 10px; border-radius: 8px; height: 230px; width: 320px;">
			<form id="uploadForm" action="php/create_subfolder.php" method="post">
				<input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
				<label><strong>Create Subfolder</strong></label><br>
				<input type="text" name="new_folder" placeholder="Subfolder name" required><br>
				<button type="submit">CREATE 📁</button>
			</form>
		</div>

	</div>

	<br>


    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Show subfolders first
        foreach ($items as $item) {
            $itemPath = $currentDir . '/' . $item;
            if (is_dir($itemPath)) {
                echo "<tr>";
                echo "<td class='icon'>📁</td>";
                echo "<td><a href='folder_view.php?folder=" . urlencode($folder . '/' . $item) . "'>" . htmlspecialchars($item) . "</a></td>";
                echo "<td>
					<a href='php/delete_item.php?folder=" . urlencode($folder) . "&item=" . urlencode($item) . "' onclick='return confirm(\"Delete this item?\");'>🗑️</a>
					" . (!is_dir($itemPath) ? "<a href='$itemPath' download>⬇️</a>" : "") . "
					<form action='php/rename_item.php' method='post' style='display:inline;'>
						<input type='hidden' name='folder' value='" . htmlspecialchars($folder) . "'>
						<input type='hidden' name='old_name' value='" . htmlspecialchars($item) . "'>
						<input type='text' name='new_name' placeholder='Rename' required style='width:100px;'>
						<button type='submit'>✏️</button>
					</form>
				</td>";

                echo "</tr>";
            }
        }

        // Show files
        foreach ($items as $item) {
            $itemPath = $currentDir . '/' . $item;
            if (!is_dir($itemPath)) {
                echo "<tr>";
                echo "<td class='icon'>" . getIcon($item) . "</td>";
                echo "<td>" . htmlspecialchars($item) . "</td>";
                // For both folders and files
				echo "<td>
					<a href='php/delete_item.php?folder=" . urlencode($folder) . "&item=" . urlencode($item) . "' onclick='return confirm(\"Delete this item?\");'>🗑️</a>
					" . (!is_dir($itemPath) ? "<a href='$itemPath' download>⬇️</a>" : "") . "
					<form action='php/rename_item.php' method='post' style='display:inline;'>
						<input type='hidden' name='folder' value='" . htmlspecialchars($folder) . "'>
						<input type='hidden' name='old_name' value='" . htmlspecialchars($item) . "'>
						<input type='text' name='new_name' placeholder='Rename' required style='width:100px;'>
						<button type='submit'>✏️</button>
					</form>
				</td>";

                echo "</tr>";
            }
        }
        ?>
        </tbody>
    </table>
</div>
<script>
const dropArea = document.getElementById('dropArea');
const uploadForm = document.getElementById('uploadForm');
const folderValue = document.querySelector('input[name="folder"]').value;

// 🔁 Handle drag-and-drop upload
dropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropArea.style.backgroundColor = '#e3f2fd';
});

dropArea.addEventListener('dragleave', () => {
    dropArea.style.backgroundColor = '';
});

dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.style.backgroundColor = '';

    const files = e.dataTransfer.files;
    const formData = new FormData();
    formData.append('folder', folderValue);
    for (let i = 0; i < files.length; i++) {
        formData.append('file[]', files[i]);
    }

    uploadFiles(formData);
});

// ✅ Handle standard form submit (upload button)
uploadForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const files = uploadForm.querySelector('input[type="file"]').files;
    if (files.length === 0) {
        alert("Please select a file to upload.");
        return;
    }

    const formData = new FormData();
    formData.append('folder', folderValue);
    for (let i = 0; i < files.length; i++) {
        formData.append('file[]', files[i]);
    }

    uploadFiles(formData);
});

// 📤 Shared upload handler
function uploadFiles(formData) {
    fetch('php/upload_to_folder.php', {
        method: 'POST',
        body: formData
    }).then(response => response.text())
      .then(result => {
          alert(result);
          location.reload();
      }).catch(error => {
          alert("Upload failed!");
          console.error(error);
      });
}
</script>
<!-- Footer -->


</body>
<footer>
    &copy; 2025 NIAT | NIAT CLOUD - Secure File Sharing
</footer>
</html>
