my project will undergo zap scan so security wise can check my codings is fully protected and will not lead to any vulnerability. i will share my code one by one and you please review it and let me knoe for any necessary changes. index.php:- \<?php
require 'includes/db.php'; // include the PDO DB connection

// Fetch facilities using PDO
try {
\$stmt = \$pdo->query("SELECT id, name FROM facilities ORDER BY name");
\$facilities = \$stmt->fetchAll();
} catch (PDOException \$e) {
echo "Error: " . \$e->getMessage();
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

```
<!-- Pikaday JS -->
<script src="js/pikaday.js"></script>
```

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

  <header class="bg-blue-900 text-white px-10 py-8 flex items-center justify-between">
		<div class="flex items-center">
			<!-- Logo aligned to the left -->
			<img src="assets/images/unit-logo.png" alt="Unit Logo" class="h-16 mr-4" />
			<!-- Title and description to the right of the logo -->
			<div>
				<h1 class="text-3xl lg:text-4xl font-bold">NIAT FACILITY BOOKING SYSTEM</h1>
				<p class="text-sm lg:text-lg mt-1">Online Booking for Kalam Auditorium & Examination Hall</p>

```
		</div>
	</div>
</header>
```

<<main class="max-w-screen-xl mx-auto px-10 py-10 grid md:grid-cols-2 gap-12 xl:gap-20">

```
<!-- Booking Form -->
<section class="card w-full max-w-2xl mx-auto">
  <h2 class="text-xl font-bold mb-4 text-center text-blue-900" style="font-family: 'Times New Roman', Times, serif;">BOOK YOUR SLOT</h2>
  <form id="booking-form" method="POST" class="space-y-4">

    <div> 
	  <label for="facility" class="block text-sm lg:text-base font-semibold">Select Facility:</label>
	  <select id="facility" name="facility" required class="w-full px-3 py-2 rounded border border-gray-300">
		<option value="">-- Select Facility --</option>
		<?php foreach ($facilities as $fac): ?>
		  <option value="<?= $fac['id'] ?>"><?= htmlspecialchars($fac['name']) ?></option>
		<?php endforeach; ?>
	  </select>
	</div>
	<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
		<div>
		  <label for="date" class="block text-sm lg:text-base font-semibold">Select Date:</label>
		  <input type="text" id="start_date" name="start_date" autocomplete="off" placeholder="Click here for Start Date" required class="w-full px-3 py-2 rounded border border-gray-300 text-sm lg:text-base px-4 py-3" />

		</div>
		
		<div>
		  <label for="end_date" class="block text-sm lg:text-base font-semibold">End Date:</label>
		  <input type="text" id="end_date" name="end_date" autocomplete="off" placeholder="Click here for End Date" required class="w-full px-3 py-2 rounded border border-gray-300 text-sm lg:text-base px-4 py-3" />
		</div>

	</div>
	<div>
	  <label for="name" class="block text-sm lg:text-base font-semibold">Your Name:</label>
	  <input type="text" id="name" name="name" required class="w-full px-3 py-2 rounded border border-gray-300 text-sm lg:text-base px-4 py-3" placeholder="Enter your name" />
	</div>

	<div>
	  <label for="unit" class="block text-sm lg:text-base font-semibold">Your Unit/Dept:</label>
	  <input type="text" id="unit" name="unit" required class="w-full px-3 py-2 rounded border border-gray-300 text-sm lg:text-base px-4 py-3" placeholder="Enter unit or department" />
	</div>

	<!-- Existing Slot Dropdown -->
	<div>
	  <label for="slot" class="block text-sm lg:text-base font-semibold">Select Slot:</label>
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
	  <label class="block text-sm lg:text-base font-semibold">Custom Time Slot:</label>
	  <div class="flex gap-4">
		<div class="flex-1">
		  <label for="start_time" class="block text-sm lg:text-base font-semibold">From:</label>
		  <input type="time" id="start_time" name="start_time" class="w-full px-3 py-2 rounded border border-gray-300 text-sm lg:text-base px-4 py-3" />
		</div>
		<div class="flex-1">
		  <label for="end_time" class="block text-sm lg:text-base font-semibold">To:</label>
		  <input type="time" id="end_time" name="end_time" class="w-full px-3 py-2 rounded border border-gray-300 text-sm lg:text-base px-4 py-3" />
		</div>
	  </div>
	</div>


    <div class="flex gap-10 mt-6">
      <button type="submit" class="bg-cyan-400 hover:bg-cyan-600 text-black font-semibold rounded px-12 py-3 text-base lg:text-lg">Book Slot</button>
      <button type="button" id="reset-button" class="bg-red-500 hover:bg-red-700 text-white font-semibold rounded px-12 py-3 text-base lg:text-lg">Reset</button>
    </div>
  </form>
</section>

<!-- Slot Status -->
<section class="card w-full max-w-2xl mx-auto">
  <h2 class="text-xl font-bold mb-4 text-center text-blue-900" style="font-family: 'Times New Roman', Times, serif;">SLOT AVAILABILITY STATUS</h2>
  <div id="slot-status" class="space-y-6 text-sm sm:text-base text-white">

    
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
```

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

```
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
```

</body>

</html>
