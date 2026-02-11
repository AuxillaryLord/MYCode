<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter_status'])) {
    $_SESSION['filter_status'] = $_POST['filter_status'];
	$_SESSION['show_section'] = 'bookingOverview';
    header("Location: admin_panel.php");
    exit;
}


// Include database connection
require_once '../includes/db.php';

// Check if the admin is logged in
// Allow access if role is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../niatcloud/login.php");
    exit();
}


// Fetch booking requests with facility names from the database
$query = "
    SELECT b.id, 
           b.start_date, 
           b.end_date, 
           b.start_time, 
           b.end_time, 
           b.slot, 
           b.name AS requester_name, 
           b.unit, 
           b.status, 
           b.created_at, 
           f.name AS facility_name
    FROM bookings b
    JOIN facilities f ON b.facility_id = f.id
    WHERE b.status = 'pending'
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$requests = $stmt->fetchAll();

// Format Date function for displaying
// Format Date function for displaying (dd-mmm-yy)
function formatDate($date) {
    return date("d-M-y", strtotime($date));
}


// Format Time function for displaying
function formatTime($time) {
    return date("H:i", strtotime($time));
}

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Approve Requests</title>
    <link href="../css/tailwind.min.css" rel="stylesheet">
	<!-- Inside <head> -->
	
	


    <script>
        // Function to approve a booking request
        function handleRequest(requestId, action) {
			if (confirm(`Are you sure you want to ${action} this request?`)) {
				fetch('approve_reject_request.php', {
					method: 'POST',
					body: JSON.stringify({ id: requestId, action: action }),
					headers: {
						'Content-Type': 'application/json'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.status === 'success') {
						alert(`Request ${action}d successfully.`);
						location.reload(); // Reload to show updated list
					} else {
						alert(`Failed to ${action} the request.`);
					}
				})
				.catch(error => {
					console.error('Error:', error);
					alert('An error occurred while processing the request.');
				});
			}
		}

		function showSection(id) {
			document.querySelectorAll('.section').forEach(div => div.classList.add('hidden'));
			document.getElementById(id).classList.remove('hidden');
			
			// Store the section in localStorage
			localStorage.setItem('openSection', id);

			
			
		}

		
    </script>
    <link rel="stylesheet" href="../css/admin_panel.css" />
</head>
<body class="text-white-900 font-sans min-h-screen flex flex-col">


	<div class="container flex-grow">

		<div class="header">
			  <div class="header-left">
				<img src="../assets/images/unit-logo.png" alt="Unit Logo" class="unit-logo">
			  </div>
			  <div class="header-center">
				<h1>WELCOME TO ADMIN PANEL</h1>
				<p>Approve or Reject Booking Requests</p>
			  </div>
			  <div class="header-right">
				<form action="../../classroom_monitoring/routine/logout_trg.php" method="POST">
				  <button type="submit" class="btn-logout">Logout</button>
				</form>
			  </div>
			</div>

		<!-- Add below the Logout button in your admin_panel.php -->
	<div class="container mt-4">
		<div class="flex justify-center gap-10 space-x-10">
			
			<button onclick="showSection('slotBlocking')" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800">Block Slots</button>
			<button onclick="showSection('blockedslotview')" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800">View Blocked Slots</button>
			<button onclick="showSection('bookingOverview')" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800">Booking Overview</button>
		</div>
	</div><br><br>


	<!-- Loop through booking requests and display them -->
		<?php if (count($requests) > 0): ?>
			<?php foreach ($requests as $request): ?>
				<div class="request-card">
					<span class="request-details">
						<b><?php echo strtoupper($request['requester_name']); ?></b> from 
						<b><?php echo strtoupper($request['unit']); ?></b> has requested a booking for the
						<strong><?php echo strtoupper($request['facility_name']); ?></strong> from 
						<strong><?php echo formatDate($request['start_date']); ?></strong> to 
						<strong><?php echo formatDate($request['end_date']); ?></strong>, between 
						<strong><?php echo formatTime($request['start_time']); ?></strong> and 
						<strong><?php echo formatTime($request['end_time']); ?></strong>, using the 
						<strong><?php echo strtoupper($request['slot']); ?></strong> slot. The request was made on 
						<strong><?php echo formatDate($request['created_at']); ?></strong>.
					</span>
					<br><br>

					<div style="margin-top: 5px;">
						<button class="btn-approve" onclick="handleRequest(<?php echo $request['id']; ?>, 'approve')">Approve</button>
						<button class="btn-reject" onclick="handleRequest(<?php echo $request['id']; ?>, 'reject')">Reject</button>

					</div>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<p>No pending requests at the moment.</p>
		<?php endif; ?>

		
	<!-- Add these divs further below where you want the sections to appear -->


	<div id="slotBlocking" class="section mt-6 hidden">
		<div class="request-card bg-[#003366] p-6 rounded-lg shadow-lg">
			<h2 class="section-title text-center text-[#f79256] text-2xl font-bold mb-6">Block Slot for Users</h2>
			<form action="actions/block_slot.php" method="POST" class="space-y-6">
				
				<div class="flex items-center gap-4">
					<label for="facility_id" class="text-black text-xl font-semibold w-1/3">Facility:</label>
					<select name="facility_id" id="facility_id" required class="w-full p-3 rounded-md border border-[#f79256] text-[#003366] bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
						<?php
							$stmt = $pdo->query("SELECT id, name FROM facilities");
							while ($f = $stmt->fetch()):
						?>
						<option value="<?= $f['id']; ?>"><?= $f['name']; ?></option>
						<?php endwhile; ?>
					</select>
				</div><br>
				
				<div class="flex items-center gap-4">
					<label for="block_date" class="text-black text-xl font-semibold w-1/3">Date:</label>
					<input type="date" id="block_date" name="block_date" required class="w-full p-3 rounded-md border border-[#f79256] text-[#003366] bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
				</div><br>

				<div class="flex items-center gap-4">
					<label for="slot" class="text-black text-xl font-semibold w-1/3">Select Slot:</label>
					<select name="slot" id="slot" required class="w-full p-3 rounded-md border border-[#f79256] text-[#003366] bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
						<option value="08:00-11:00">08:00 - 11:00</option>
						<option value="11:00-14:00">11:00 - 14:00</option>
						<option value="14:00-16:00">14:00 - 16:00</option>
						<option value="16:00-18:00">16:00 - 18:00</option>
						<option value="08:00-18:00">Full Day (08:00 - 18:00)</option>
					</select>
				</div><br>

				<div class="flex items-center gap-4">
					<label for="reason" class="text-black text-xl font-semibold w-1/3">Reason:</label>
					<input type="text" id="reason" name="reason" placeholder="Reason for blocking" required class="w-full p-3 rounded-md border border-[#f79256] text-[#003366] bg-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
				</div><br>

				<div class="text-center mt-6">
					<input type="submit" value="Block Slot" class="btn-submit px-6 py-3 text-white bg-[#007bff] rounded-md font-semibold hover:bg-[#005f8a] cursor-pointer transition duration-300">
					<input type="reset" value="Reset" class="btn-reset px-6 py-3 text-white bg-[#6c757d] rounded-md font-semibold hover:bg-[#5a6268] cursor-pointer transition duration-300">
				</div>

			</form>
		</div>
	</div>


	<!-- Blocked Slots Display Section -->
	<div id="blockedslotview" class="section mt-10">
	  <div class="request-card">
		<h2 class="section-title text-center">Blocked Slots Overview</h2><br>

		<div style="overflow-x:auto;">
		  <table class="min-w-full border border-gray-300 rounded-md bg-white text-black">
			<thead style="background-color: #f2f2f2;">
			  <tr>
				<th class="py-2 px-4 border-b text-left">Facility</th>
				<th class="py-2 px-4 border-b text-left">Date</th>
				
				<th class="py-2 px-4 border-b text-left">Slot</th>
				<th class="py-2 px-4 border-b text-left">Reason</th>
				<th class="py-2 px-4 border-b text-left">Blocked on</th>
			  </tr>
			</thead>
			<tbody>
			  <?php
				$stmt = $pdo->query("SELECT bs.block_date, bs.start_time, bs.end_time, bs.slot, bs.reason, bs.created_at, f.name AS facility_name
									 FROM blocked_slots bs
									 JOIN facilities f ON bs.facility_id = f.id
									 ORDER BY bs.block_date DESC, bs.start_time ASC");
				while ($row = $stmt->fetch()):
			  ?>
				<tr>
				  <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['facility_name']) ?></td>
				  <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['block_date']) ?></td>
				  
				  <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['slot']) ?></td>
				  <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['reason']) ?></td>
				  <td class="py-2 px-4 border-b"><?= htmlspecialchars($row['created_at']) ?></td>
				</tr>
			  <?php endwhile; ?>
			</tbody>
		  </table>
		</div>
	  </div>
	</div>



	<div id="bookingOverview" class="section mt-6 <?= isset($_GET['filter_status']) ? '' : 'hidden' ?>">


		<div class="request-card">
			<h2 class="section-title text-center">Booking Overview</h2>

			<form method="POST" class="mb-4 text-black">
				<label for="filter_status" class="text-black mr-2">Filter by Status:</label>
				<select name="filter_status" id="filter_status" onchange="this.form.submit()" class="p-2 rounded">
					<option value="pending" <?= ($_SESSION['filter_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
					<option value="approved" <?= ($_SESSION['filter_status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
					<option value="rejected" <?= ($_SESSION['filter_status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
				</select>
			</form>

			<table class="overview-table">
				<thead>
					<tr>
						<th>Requester</th>
						<th>Unit</th>
						<th>Facility</th>
						<th>Booking Period</th>
						<th>Slot</th>
						<th>Status</th>
						<th>Requested On</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$filter = $_SESSION['filter_status'] ?? 'pending'; // Default to 'pending'
						
						$query = "SELECT b.*, f.name AS facility_name FROM bookings b JOIN facilities f ON b.facility_id = f.id";
						if ($filter) {
							$query .= " WHERE b.status = ?";
							$stmt = $pdo->prepare($query);
							$stmt->execute([$filter]);
						} else {
							$stmt = $pdo->query($query);
						}
						$allBookings = $stmt->fetchAll();

						foreach ($allBookings as $booking):
					?>
					<tr>
						<td><?= htmlspecialchars($booking['name']) ?></td>
						<td><?= htmlspecialchars($booking['unit']) ?></td>
						<td><?= htmlspecialchars($booking['facility_name']) ?></td>
						<td>
							<?= formatDate($booking['start_date']) ?> to <?= formatDate($booking['end_date']) ?>, 
							<?= formatTime($booking['start_time']) ?> to <?= formatTime($booking['end_time']) ?>
						</td>
						<td><?= $booking['slot'] ?></td>
						<td><?= ucfirst($booking['status']) ?></td>
						<td><?= formatDate($booking['created_at']) ?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		

	</div>

	

		

	</div>
<footer class=" text-center text-white py-3 text-sm flex-shrink-0">

	© 2025 NIAT | Secure Facility Booking System
  </footer>


<script>
    // On page load, open the last section from localStorage
    window.addEventListener('DOMContentLoaded', () => {
        const openSection = localStorage.getItem('openSection');
        if (openSection) {
            showSection(openSection);
        }
    });
	
	setTimeout(() => {
    const popup = document.getElementById('popupMessage');
    if (popup) popup.style.display = 'none';
  }, 4000); // Hides after 4 seconds
  
  // Remove ?message=... from the URL after displaying it
  if (window.location.search.includes('message=')) {
    const url = new URL(window.location);
    url.searchParams.delete('message');
    window.history.replaceState({}, document.title, url.pathname);
  }
 
  // Set the min attribute to today's date
  document.addEventListener("DOMContentLoaded", function () {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("block_date").setAttribute("min", today);
  });
</script>


<?php if (isset($_GET['message'])): ?>
<div id="popupMessage" style="
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background-color: #d4edda;
  color: #155724;
  padding: 20px 30px;
  border-radius: 12px;
  box-shadow: 0 0 15px rgba(0,0,0,0.3);
  font-size: 18px;
  z-index: 9999;
  text-align: center;
">
  <?= htmlspecialchars($_GET['message']) ?>
</div>
<?php endif; ?>


</body>
</html>
