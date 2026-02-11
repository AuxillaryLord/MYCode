document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('booking-form');
  const facility = document.getElementById('facility');
  const startDate = document.getElementById('start_date');
  const endDate = document.getElementById('end_date');
  const startTime = document.getElementById('start_time');
  const endTime = document.getElementById('end_time');
  const name = document.getElementById('name');
  const unit = document.getElementById('unit');
  const slot = document.getElementById('slot');
  const timeFields = document.getElementById('custom-time-fields');
  const slotStatus = document.getElementById('slot-status');
  const slotMessage = document.getElementById('slot-message');
  
  
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
	
  // Validate required elements
  if (!form || !facility || !startDate || !endDate || !startTime || !endTime || !name || !unit || !slot || !timeFields) {
    console.error('❌ Missing one or more required form elements.');
    return;
  }

  // Toggle custom time fields visibility
  slot.addEventListener('change', () => {
    if (slot.value === 'custom') {
      timeFields.classList.remove('hidden');
    } else {
      timeFields.classList.add('hidden');
    }
  });

  // Initialize user-selected slots array
  let userSelectedSlots = [];

  // Function to add a custom slot to the userSelectedSlots array
  function addCustomSlot(start, end) {
    userSelectedSlots.push({ start, end });
  }

  // Form submission handler
  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    // Basic Validation
    if (
      !facility.value ||
      !startDate.value ||
      !endDate.value ||
      !name.value.trim() ||
      !unit.value.trim() ||
      (slot.value === 'custom' && (!startTime.value || !endTime.value)) ||
      (slot.value !== 'custom' && !slot.value)
    ) {
      alert('Please fill all fields.');
      return;
    }

    // Time Validation (Only if 'custom' slot is selected)
    if (slot.value === 'custom') {
      if (startDate.value > endDate.value) {
        alert('End date must be after start date.');
        return;
      }
      if (startDate.value === endDate.value && startTime.value >= endTime.value) {
        alert('End time must be after start time.');
        return;
      }
    }

    const formData = new FormData();
    formData.append('facility_id', facility.value);
    formData.append('start_date', startDate.value);
    formData.append('end_date', endDate.value);
    formData.append('name', name.value.trim());
    formData.append('unit', unit.value.trim());
    formData.append('slot', slot.value);

    // Append custom times only if slot is "custom"
    if (slot.value === 'custom') {
      formData.append('start_time', startTime.value);
      formData.append('end_time', endTime.value);
      addCustomSlot(startTime.value, endTime.value);  // Add to user-selected slots
    }

    try {
      const response = await fetch('includes/book_slot.php', {
        method: 'POST',
        body: formData
      });
      const result = await response.json();
      alert(result.message);
      if (result.status === 'success') {
        form.reset();
        userSelectedSlots = [];  // Reset after successful booking
        fetchSlotStatus(facility.value, startDate.value, endDate.value);
      }
    } catch (error) {
      console.error('Booking error:', error);
      alert('Something went wrong. Try again later.');
    }
  });

  // Fetch slot availability
  async function fetchSlotStatus(facilityId, fromDate, toDate) {
    slotStatus.innerHTML = '';
    slotMessage.textContent = '';

    // Ensure the facility and date range are provided
    if (!facilityId || !fromDate || !toDate) {
      slotMessage.textContent = 'Select facility and date range to view availability.';
      return;
    }

    const start = new Date(fromDate);
    const end = new Date(toDate);

    // Check if end date is before the start date
    if (end < start) {
      slotMessage.textContent = 'End date cannot be earlier than start date.';
      return;
    }

    // Create the list of dates between the selected range
    const dateList = [];
    for (let d = new Date(start.getTime()); d <= end; d.setDate(d.getDate() + 1)) {
	  dateList.push(new Date(d).toISOString().split('T')[0]);
	}


    let allHtml = '';
    const allSlots = [
      "08:00-11:00",
      "11:00-14:00",
      "14:00-16:00",
      "16:00-18:00",
      "08:00-18:00" // Full day slot
    ];

    // Iterate over each date in the selected range
    for (const date of dateList) {
      const response = await fetch(`includes/get_slots.php?facility_id=${facilityId}&date=${date}`);
      const data = await response.json();
	  
	
		// Check if data is an array before using forEach
		if (!Array.isArray(data)) {
		  console.error('Invalid data format:', data);
		  slotMessage.textContent = 'Error fetching slot data.';
		  return;
		}

      // Create a lookup for slot status
      const statusMap = {};
      data.forEach(item => {
        statusMap[item.slot] = item.status;
      });

      // Here, we should add the logic to handle user-selected custom slots
      userSelectedSlots.forEach(({ start, end }) => {
        const customSlot = `${start}-${end}`;
        statusMap[customSlot] = 'booked'; // Mark custom slots as booked
      });

      // Check for full-day status (08:00-18:00) and apply that to the rest of the slots
      const fullDayStatus = statusMap["08:00-18:00"];
      if (fullDayStatus === "booked" || fullDayStatus === "blocked") {
        allSlots.forEach(slot => {
          if (!statusMap[slot]) {
            statusMap[slot] = fullDayStatus;
          }
        });
      }

      // Function to format the date
      function formatDate(isoDateStr) {
		  const dateObj = new Date(isoDateStr);
		  const day = String(dateObj.getDate()).padStart(2, '0');
		  const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		  const month = monthNames[dateObj.getMonth()];
		  const year = String(dateObj.getFullYear()).slice(-2);
		  return `${day}-${month}-${year}`;
		}

		// Add HTML for the current date and slots
		// Add HTML for the current date and slots
		allHtml += `
		  <div class="my-8 px-2 py-6 rounded-lg border border-gray-200 bg-white shadow-sm lg:flex lg:items-start lg:gap-10">
			<div class="mb-6 lg:mb-0 lg:w-1/4 xl:w-1/5 flex items-center">
			  <h3 class="font-semibold text-gray-800 text-lg whitespace-nowrap"> ${formatDate(date)}:</h3>
			</div>
			<div class="lg:w-3/4 xl:w-4/5">
			  <div class="flex flex-wrap gap-3 justify-start">
		`;




		// Generate HTML for each slot
		allSlots.forEach((slotText, index) => {
		  const status = statusMap[slotText] || 'available';

		  let bgColor = 'bg-green-500'; // default: available
		  if (status === 'booked') bgColor = 'bg-red-500';
		  else if (status === 'blocked') bgColor = 'bg-gray-500';
		  else if (status === 'partial') bgColor = 'bg-yellow-500';

		  const classes = index === 4 ? 'col-span-full xl:col-span-2 justify-self-center w-full sm:w-3/4 lg:w-1/2' : '';
		  allHtml += `
			<div class="rounded-xl px-4 py-3 text-center shadow-md text-sm lg:text-base font-medium ${classes} ${bgColor} text-white hover:scale-105 transition transform duration-200">
			  ${slotText}
			</div>
		  `;
		});

		let customHtml = '';

		data.forEach(item => {
		  if (!allSlots.includes(item.slot) && item.status === 'custom') {
			customHtml += `
			  <div class="rounded-xl p-3 text-center shadow-md bg-yellow-500 text-white my-2">
				Custom Booked: ${item.slot}
			  </div>
			`;
		  }
		});

		// Close the grid and container
		allHtml += `
			  </div> <!-- End of slot grid -->
			</div> <!-- End of .lg:w-3/4 -->
		  </div> <!-- End of .lg:flex -->
		`;

		if (customHtml) {
		  allHtml += `
			<div class="bg-gray-100 p-5 rounded-md border border-gray-300 mt-5 text-lg lg:text-base">
			  <h4 class="text-lg font-semibold text-gray-700">Custom Booked Slots:</h4>
			  ${customHtml}
			</div>
		  `;
		}


		allHtml += `</div></div>`; // Close .lg:w-3/4 and .lg:flex



    }

    // Add the HTML to the DOM
    slotStatus.innerHTML = allHtml;
  }

  // Event listeners to auto-refresh slot availability
  facility.addEventListener('change', () => {
    fetchSlotStatus(facility.value, startDate.value, endDate.value);
  });

  startDate.addEventListener('change', () => {
    fetchSlotStatus(facility.value, startDate.value, endDate.value);
  });

  endDate.addEventListener('change', () => {
    fetchSlotStatus(facility.value, startDate.value, endDate.value);
  });
});
