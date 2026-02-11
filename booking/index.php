<?php
require 'includes/db.php'; // include the PDO DB connection

// Fetch facilities using PDO
try {
    $stmt = $pdo->query("SELECT id, name FROM facilities ORDER BY name");
    $facilities = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NIAT Booking Portal</title>
  <link href="css/tailwind.min.css" rel="stylesheet" />
  <script defer src="js/booking.js"></script>
	  <!-- Pikaday CSS -->
	<link rel="stylesheet" href="css/pikaday.css" />

	<!-- Pikaday JS -->
	<script src="js/pikaday.js"></script>

  <style>
    body {
      background: linear-gradient(to bottom, #112240, #0a192f);
    }
    .card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .slot-circle {
      width: 16px;
      height: 16px;
      border-radius: 50%;
      display: inline-block;
    }
  </style>
</head>
<body class="text-blue-900 font-sans min-h-screen flex flex-col">
<!-- Admin Login Button (Top-right corner) -->
<div class="absolute top-10 right-4">
  <a href="admin/admin_login.php" class="bg-blue-600 text-white py-2 px-4 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300">
    Admin Login
  </a>
</div>


  <header class="bg-blue-900 text-white p-6 text-center shadow-md flex items-center justify-between">
		<div class="flex items-center">
			<!-- Logo aligned to the left -->
			<img src="assets/images/unit-logo.png" alt="Unit Logo" class="h-16 mr-4" />
			<!-- Title and description to the right of the logo -->
			<div>
				<h1 class="text-3xl font-bold" style="font-family: 'Times New Roman', Times, serif;" >NIAT FACILITY BOOKING SYSTEM</h1>
				<p class="text-sm mt-1">Online Booking for Kalam Auditorium & Examination Hall</p>
			</div>
		</div>
	</header>


  <main class="flex-1 container mx-auto px-4 py-8 grid md:grid-cols-2 gap-8">
    <!-- Booking Form -->
    <section class="card">
      <h2 class="text-xl font-bold mb-4 text-center text-blue-900" style="font-family: 'Times New Roman', Times, serif;">BOOK YOUR SLOT</h2>
      <form id="booking-form" method="POST" class="space-y-4">

        <div> 
		  <label for="facility" class="block font-semibold text-gray-700">Select Facility:</label>
		  <select id="facility" name="facility" required class="w-full px-3 py-2 rounded border border-gray-300">
			<option value="">-- Select Facility --</option>
			<?php foreach ($facilities as $fac): ?>
			  <option value="<?= $fac['id'] ?>"><?= htmlspecialchars($fac['name']) ?></option>
			<?php endforeach; ?>
		  </select>
		</div>

		<div>
		  <label for="date" class="block font-semibold text-gray-700">Select Date:</label>
		  <input type="text" id="start_date" name="start_date" autocomplete="off" required class="w-full px-3 py-2 rounded border border-gray-300" />

		</div>
		
		<div>
		  <label for="end_date" class="block font-semibold text-gray-700">End Date:</label>
		  <input type="text" id="end_date" name="end_date" autocomplete="off" required class="w-full px-3 py-2 rounded border border-gray-300" />
		</div>


		<div>
		  <label for="name" class="block font-semibold text-gray-700">Your Name:</label>
		  <input type="text" id="name" name="name" required class="w-full px-3 py-2 rounded border border-gray-300" placeholder="Enter your name" />
		</div>

		<div>
		  <label for="unit" class="block font-semibold text-gray-700">Your Unit/Dept:</label>
		  <input type="text" id="unit" name="unit" required class="w-full px-3 py-2 rounded border border-gray-300" placeholder="Enter unit or department" />
		</div>

		<!-- Existing Slot Dropdown -->
		<div>
		  <label for="slot" class="block font-semibold text-gray-700">Select Slot:</label>
		  <select id="slot" name="slot" class="w-full px-3 py-2 rounded border border-gray-300">
			<option value="">-- Select Slot --</option>
			<option value="08:00-11:00">08:00 - 11:00</option>
			<option value="11:00-14:00">11:00 - 14:00</option>
			<option value="14:00-16:00">14:00 - 16:00</option>
			<option value="16:00-18:00">16:00 - 18:00</option>
			<option value="08:00-18:00">Full Day (08:00 - 18:00)</option>
			<option value="custom">Custom Time</option>
		  </select>
		</div>

		<!-- Custom Time Slot Inputs (initially hidden) -->
		<div id="custom-time-fields" class="hidden mt-2 space-y-2">
		  <label class="block font-semibold text-gray-700">Custom Time Slot:</label>
		  <div class="flex gap-4">
			<div class="flex-1">
			  <label for="start_time" class="block text-sm">From:</label>
			  <input type="time" id="start_time" name="start_time" class="w-full px-3 py-2 rounded border border-gray-300" />
			</div>
			<div class="flex-1">
			  <label for="end_time" class="block text-sm">To:</label>
			  <input type="time" id="end_time" name="end_time" class="w-full px-3 py-2 rounded border border-gray-300" />
			</div>
		  </div>
		</div>


        <div class="flex gap-10 mt-6">
          <button type="submit" class="bg-cyan-400 hover:bg-cyan-600 text-black font-semibold px-10 py-2 rounded">Book Slot</button>
          <button type="button" id="reset-button" class="bg-red-500 hover:bg-red-700 text-white font-semibold px-10 py-2 rounded">Reset</button>
        </div>
      </form>
    </section>

    <!-- Slot Status -->
    <section class="card">
      <h2 class="text-xl font-bold mb-4 text-center text-blue-900" style="font-family: 'Times New Roman', Times, serif;">SLOT AVAILABILITY STATUS</h2>
      <div id="slot-status" class="grid grid-cols-2 gap-10 text-md text-center text-white font-semibold">

        
      </div>
      <div class="mt-7 text-md">
        <div><span class="slot-circle bg-green-500 mr-1"></span> Available</div>
		<div><span class="slot-circle bg-red-500 mr-1"></span> Booked</div>
		<div><span class="slot-circle bg-orange-400 mr-1"></span> Blocked</div>
		<div><span class="slot-circle bg-pink-400 mr-1"></span> Custom Booked</div>


		<!-- Default message area -->
		<p id="slot-message" class="text-blue-600 mt-4 text-md">Select facility and date to view slot availability.</p>
      </div>
	  
    </section>
  </main>

  <footer class="bg-blue-950 text-center text-white py-3 text-sm">
    © 2025 NIAT | Secure Facility Booking System
  </footer>
	<script defer>
	  document.addEventListener('DOMContentLoaded', () => {
		const resetButton = document.getElementById('reset-button');
		const form = document.getElementById('booking-form');
		const slotStatusDiv = document.getElementById('slot-status');
		const slotMessage = document.getElementById('slot-message');

		// Event listener for reset button
		resetButton.addEventListener('click', () => {
		  // Reset form fields
		  form.reset();
		  
		  // Reset the slot availability section
		  slotStatusDiv.innerHTML = '';
		  slotMessage.textContent = 'Select facility and date to view slot availability.';
		});
	  });
	  
	  // Show/hide custom time slot fields
		document.addEventListener('DOMContentLoaded', () => {
		  const slot = document.getElementById('slot');
			const timeFields = document.getElementById('custom-time-fields'); // wrap start_time & end_time in a div

			slot.addEventListener('change', () => {
			  if (slot.value === 'custom') {
				timeFields.classList.remove('hidden');
			  } else {
				timeFields.classList.add('hidden');
			  }
			});

		});
		
		const pickerStart = new Pikaday({
		  field: document.getElementById('start_date'),
		  format: 'YYYY-MM-DD', // Ensures proper database-friendly format
		  toString(date, format) {
			const day = date.getDate().toString().padStart(2, '0');
			const month = (date.getMonth() + 1).toString().padStart(2, '0');
			const year = date.getFullYear();
			return `${year}-${month}-${day}`;
		  },
		  minDate: new Date()
		});

		const pickerEnd = new Pikaday({
		  field: document.getElementById('end_date'),
		  format: 'YYYY-MM-DD',
		  toString(date, format) {
			const day = date.getDate().toString().padStart(2, '0');
			const month = (date.getMonth() + 1).toString().padStart(2, '0');
			const year = date.getFullYear();
			return `${year}-${month}-${day}`;
		  },
		  minDate: new Date()
		});



	</script>
</body>


</html>
