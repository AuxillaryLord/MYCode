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
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
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
	<link rel="stylesheet" href="../css/flatpickr.min.css">
	<!-- Just before </body> -->
	<script src="../js/flatpickr.min.js"></script>
	


    <script>
        // Function to approve a booking request
        function approveRequest(requestId) {
            if (confirm('Are you sure you want to approve this request?')) {
                fetch('approve_reject_request.php', {
                    method: 'POST',
                    body: JSON.stringify({ id: requestId, action: 'approve' }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Request approved successfully.');
                        location.reload(); // Reload to show updated list
                    } else {
                        alert('Failed to approve the request.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the request.');
                });
            }
        }

        // Function to reject a booking request
        function rejectRequest(requestId) {
            if (confirm('Are you sure you want to reject this request?')) {
                fetch('approve_reject_request.php', {
                    method: 'POST',
                    body: JSON.stringify({ id: requestId, action: 'reject' }),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Request rejected successfully.');
                        location.reload(); // Reload to show updated list
                    } else {
                        alert('Failed to reject the request.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while rejecting the request.');
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
    <style>
        
        .container {
            width: 80%;
            margin: 0 auto;
            padding-top: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
			
        }
		
        .request-card {
            background-color: #003366; /* Dark Navy */
            border-radius: 10px;
            margin: 10px 0;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .request-card span {
            font-size: 16px;
        }
        .request-card button {
            background-color: #006F9A; /* Navy button color */
            border: none;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            margin-right: 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .request-card button:hover {
            background-color: #005A7A; /* Slightly darker shade */
        }
        .btn-reject {
            background-color: #D32F2F; /* Red for reject */
        }
        .btn-reject:hover {
            background-color: #C2185B;
        }
        .btn-approve:hover {
            background-color: #0277BD; /* Darker approve color */
        }
		.btn-logout {
			background-color: #D32F2F; /* Red for logout */
			border: none;
			color: white;
			font-size: 16px;
			padding: 10px 20px;
			border-radius: 5px;
			cursor: pointer;
			margin-top: 10px;
		}
		.btn-logout:hover {
			background-color: #C2185B;
		}
		.logout-form {
			position: fixed;
			top: 20px; /* Adjust top distance */
			right: 20px; /* Adjust right distance */
		}
		.overview-table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
			background-color: #fff;
			color: #000;
		}

		.overview-table th, .overview-table td {
			padding: 10px;
			border: 1px solid #ddd;
			text-align: center;
		}

		.overview-table th {
			background-color: #003366;
			color: white;
		}
		
		.section-title {
			
			font-size: 26px;
			
		}
		.btn-submit {
			background-color: #0277BD; /* Darker approve color */
			border: none;
			color: white;
			font-size: 16px;
			padding: 10px 20px;
			border-radius: 5px;
			cursor: pointer;
			margin-top: 10px;
		}
		.btn-submit:hover {
			background-color: #C2185B;
		}
		.header {
		  display: flex;
		  align-items: center;
		  justify-content: space-between;
		  background-color: #003366; /* darker navy */
		  padding: 20px 30px;
		  border-radius: 8px;
		  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
		  margin-bottom: 30px;
		  flex-wrap: wrap;
		}

		.unit-logo {
		  height: 60px;
		  width: auto;
		}

		.header-center {
		  text-align: center;
		  flex: 1;
		}

		.header-center h1 {
		  font-size: 28px;
		  font-family: 'arial black', sans-serif;
		  margin: 0;
		  color: #ffffff;
		  font-weight: bold;
		  
		}

		.header-center p {
		  font-size: 17px;
		  font-family: 'Times New roman', sans-serif;
		  color: #f79256;
		  margin-top: 5px;
		}

		.header-right .btn-logout {
		  background-color: #ff4d4d;
		  color: #fff;
		  border: none;
		  padding: 10px 18px;
		  border-radius: 6px;
		  font-size: 16px;
		  cursor: pointer;
		  transition: background-color 0.3s ease;
		}

		.header-right .btn-logout:hover {
		  background-color: #e60000;
		}

		body {
		  font-family: 'Segoe UI', sans-serif;
		  background-color: #002147; /* Navy blue */
		  color: #fff;
		  margin: 0;
		  padding: 20px;
		}



    </style>
</head>
<body>

<div class="container">
    <div class="header">
		  <div class="header-left">
			<img src="../assets/images/unit-logo.png" alt="Unit Logo" class="unit-logo">
		  </div>
		  <div class="header-center">
			<h1>WELCOME TO ADMIN PANEL</h1>
			<p>Approve or Reject Booking Requests</p>
		  </div>
		  <div class="header-right">
			<form action="logout.php" method="POST">
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
				<span>
					<b><?php echo strtoupper($request['requester_name']); ?></b> from 
					<b><?php echo strtoupper($request['unit']); ?></b>, requested a booking for 
					<strong><?php echo strtoupper($request['facility_name']); ?></strong> from 
					<strong><?php echo formatDate($request['start_date']); ?></strong> to 
					<strong><?php echo formatDate($request['end_date']); ?></strong> between 
					<strong><?php echo formatTime($request['start_time']); ?></strong> and 
					<strong><?php echo formatTime($request['end_time']); ?></strong>, using the 
					<strong><?php echo strtoupper($request['slot']); ?></strong> slot. Request made on 
					<strong><?php echo formatDate($request['created_at']); ?></strong>
				</span><br><br>

				<div style="margin-top: 5px;">
					<button class="btn-approve" onclick="approveRequest(<?php echo $request['id']; ?>)">Approve</button>
					<button class="btn-reject" onclick="rejectRequest(<?php echo $request['id']; ?>)">Reject</button>
				</div>
			</div>
		<?php endforeach; ?>
	<?php else: ?>
		<p>No pending requests at the moment.</p>
	<?php endif; ?>

	
<!-- Add these divs further below where you want the sections to appear -->


<div id="slotBlocking" class="section mt-6 hidden">
     <div class="request-card">
		<h2 class="section-title text-center">Block Slot for Users</h2><br>
		<form action="actions/block_slot.php" method="POST" class="form-grid text-black">
			
			<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
				<label style="color: #ffffff; font-size: 20px; font-weight: 500; min-width: 120px;">Facility:</label>
				<select name="facility_id" required class="w-full border border-gray-300 rounded-md px-3 py-4 bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">>
					<?php
						$stmt = $pdo->query("SELECT id, name FROM facilities");
						while ($f = $stmt->fetch()):
					?>
						<option value="<?= $f['id']; ?>"><?= $f['name']; ?></option>
					<?php endwhile; ?>
				</select><br><br>
			</div>
			
			<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
			  <label for="block_date" style="color: #ffffff; font-size: 20px; font-weight: 500; min-width: 120px;">Date:</label>
			  
			  <div style="position: relative; max-width: 300px; flex: 1;">
				<input type="date" id="block_date" name="block_date" placeholder="Select a date" required
       style="width: 100%; padding: 10px 40px 10px 10px; font-size: 16px; border-radius: 6px; border: 1px solid #ccc; cursor: pointer; background-color: white;">

				
				
			  </div>
			</div>

			
			<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
				<label for="slot" class="block" style="color: #ffffff; font-size: 20px; font-weight: 500; min-width: 120px;">Select Slot:</label>
				  <select name="slot" required class="w-full border border-gray-300 rounded-md px-3 py-4 bg-white focus:outline-none focus:ring-2 focus:ring-blue-400">
					<option value="08:00-11:00">08:00 - 11:00</option>
					<option value="11:00-14:00">11:00 - 14:00</option>
					<option value="14:00-16:00">14:00 - 16:00</option>
					<option value="16:00-18:00">16:00 - 18:00</option>
					<option value="08:00-18:00">Full Day (08:00 - 18:00)</option>
				  </select><br><br>
			  </div>
			
			
			<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
			  <label for="reason" style="color: #ffffff; font-size: 20px; font-weight: 500; min-width: 120px;">Reason:</label>
			  <input type="text" id="reason" name="reason" placeholder="Reason for blocking" required 
					 style="flex: 1; padding: 10px; border-radius: 6px; border: 1px solid #ccc; font-size: 16px;">
			</div>


			<div style="text-align: center; margin-top: 20px;">
			  <input type="submit" value="Block Slot" class="btn-submit" 
					 style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">
			  <input type="reset" value="Reset" 
					 style="padding: 10px 20px; font-size: 16px; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
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
			<label for="filter_status" class="text-white mr-2">Filter by Status:</label>
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
