@extends('layouts.app')

@section('title', 'Meeting Room')
@section('page-title', 'BOOKING MEETING ROOM')
@section('breadcrumb-item', 'Facility Booking')
@section('breadcrumb-active', 'Meeting Room')

@section('content')
<div class="bg-white rounded-xl shadow-md w-full p-6 relative animate-fadeIn mb-4">
    <!-- Recent Booking -->
    <div class="flex justify-between items-center mb-3">
  <h3 class="text-xl font-semibold text-gray-700">Recent Booking</h3>

  <a href="{{ route('facility.booking-room.history') }}"
     class="flex items-center gap-2 bg-green-500 text-white text-sm font-medium px-5 py-3 rounded-full shadow-md 
            hover:bg-green-700 hover:shadow-lg hover:scale-105 hover:shadow-green-400/40 
            transition-all duration-300">
    <i data-feather="calendar" class="w-5 h-5"></i>
    <span>Booking History</span>
  </a>
</div>


 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
         <!-- On Going -->
         <div class="bg-gray-50 p-4 rounded-lg shadow">
            <h4 class="font-semibold text-gray-800 mb-2">On Going (Today)</h4>
            <ul id="ongoingList" class="space-y-2 text-sm text-gray-600">
               <!-- Akan diisi jQuery -->
            </ul>
         </div>
         <!-- Upcoming -->
         <div class="bg-gray-50 p-4 rounded-lg shadow">
            <h4 class="font-semibold text-gray-800 mb-2">Upcoming</h4>
            <ul id="upcomingList" class="space-y-2 text-sm text-gray-600">
               <!-- Akan diisi jQuery -->
            </ul>
         </div>
      </div>
</div>
 <div class="bg-white rounded-xl shadow-2xl w-full p-6 relative animate-fadeIn">
    
   <!-- Header -->
   <div class="flex justify-between bg-green-500 items-center mb-4 border-b rounded-lg px-3 py-2">
      <div class="flex items-center gap-3"> 
         <i data-feather="home" class="w-6 h-6 text-white"></i> 
         <h2 class="text-2xl font-semibold text-white">
            Booking Meeting Room
            <span id="selectedDateText" class="ml-3 text-sm font-normal italic"></span>
         </h2>
      </div>
      <div class="flex items-center gap-2">
         <input type="date" id="datePicker" class="rounded-md px-2 py-1">
         <button id="openExportModalBtn" 
        class="openExportModalBtn bg-white text-green-600 px-3 py-1 rounded-md shadow hover:bg-gray-100 transition flex items-center gap-1">
        <i data-feather="download" class="w-4 h-4"></i>
        <span>Export</span>
    </button>
      </div>
   </div>
   

   <!-- Table -->
   <div class="overflow-x-auto">
  <table id="scheduleTable" class="w-full text-left table-spacing border-collapse border-separate border-spacing-y-2">
   <thead></thead>
   <tbody id="scheduleBody"></tbody>
</table>

</div>

</div>





<!-- Modal Booking -->
<div id="bookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg w-[700px] p-6">
    <h2 class="text-xl font-bold mb-4">Booking Room</h2>

 <!-- Info Room -->
<div class="bg-gray-50 rounded-lg p-4 space-y-3 text-sm max-h-[60vh] overflow-y-auto">
  <div class="flex items-center gap-3">
    <span class="text-green-600"><i class="far fa-calendar-alt"></i></span>
    <p><span class="font-semibold">Booking Date:</span> <span  class="text-gray-800" id="modalDate"></span></p>
  </div>

  <div class="flex items-center gap-3">
    <span class="text-green-600"><i class="far fa-clock"></i></span>
    <p><span class="font-semibold">Booking Time:</span> <span class="text-gray-800" id="modalTime"></span></p>
  </div>

  <div class="flex items-center gap-3">
    <span class="text-green-600"><i class="fas fa-door-open"></i></span>
    <p><span class="font-semibold">Room:</span> <span  class="text-gray-800" id="modalRoom"></span></p>
  </div>

  <div class="flex items-center gap-3">
    <span class="text-green-600"><i class="fas fa-user"></i></span>
    <p><span class="font-semibold">User:</span> <span  class="text-gray-800">{{ Auth::user()->name }}</span></p>
  </div>

  <div class="flex items-center gap-3">
    <span class="text-green-600"><i class="fas fa-users"></i></span>
    <p><span class="font-semibold">Capacity:</span>
      <span id="modalCapacity" class="ml-1 text-gray-800">0</span>
    </p>
  </div>

  <div class="flex items-start gap-3">
    <span class="text-green-600 mt-1"><i class="fas fa-cogs"></i></span>
    <div>
      <span class="font-semibold block">Equipment:</span>
      <ul id="modalEquipment"class="list-disc list-inside text-sm text-gray-700">
        <!-- Chip style dinamis -->
        <!-- <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Projector</span> -->
</ul>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <span class="text-green-600"><i class="fas fa-map-marker-alt"></i></span>
    <p><span class="font-semibold">Location:</span> <span  class="text-gray-800" id="modalLocation"></span></p>
</div>

    <!-- Purpose -->
    <div class="mb-3">
      <label class="block text-sm font-medium">Purpose</label>
      <select id="modalPurpose" class="w-full border rounded px-3 py-2">
        <option value="">-- Select Purpose --</option>
        <option value="Internal Meeting">Internal Meeting</option>
        <option value="Customer/Vendor Meeting">Customer/Vendor Meeting</option>
        <option value="Presentation">Presentation</option>
        <option value="Training">Training</option>
        <option value="Recruitment">Recruitment</option>
        <option value="Internal Audit">Internal Audit</option>
        <option value="Internal External">External Audit</option>
        <option value="Other">Other</option>
      </select>
    </div>

    <!-- Purpose Description -->
    <div class="mb-3">
      <label class="block text-sm font-medium">Purpose Description</label>
      <textarea id="modalPurposeDescription" rows="3" class="w-full border rounded px-3 py-2"></textarea>
    </div>
</div>

    <!-- Action -->
    <div class="flex justify-end gap-2 mt-4 border-t border-gray-400 pt-4">
      <button id="closeModal" class="w-24 px-4 py-2 bg-gray-400 hover:bg-gray-600 text-white rounded">Cancel</button>
      <button id="saveBooking" class="w-24 px-4 py-2 bg-green-600 hover:bg-green-800 text-white rounded">Book Now</button>
    </div>
</div>
</div>

<!-- Modal Export -->
<div id="exportModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
  <div id="exportModalContent" 
       class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 transform transition-all duration-300 scale-90 opacity-0">
      
      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <i data-feather="file" class="w-5 h-5 text-green-600"></i>
              Export Booking Report
          </h2>
          <button id="closeExportModalBtn" class="text-gray-400 hover:text-gray-600">
              <i data-feather="x" class="w-5 h-5"></i>
          </button>
      </div>

      <!-- Body -->
      <div class="mb-6">
          <input type="text" id="dateRangePicker"
                 class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                 placeholder="Choose date range..." />
      </div>

      <!-- Footer -->
      <div class="flex justify-end">
          <button id="generateExcelBtn"
              class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700 transition">
              <i data-feather="file-text" class="w-5 h-5"></i>
              Generate Excel
          </button>
      </div>
  </div>
</div>


{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#category-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#category-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

.table-spacing {
  border-collapse: separate !important;
  border-spacing: 12px 6px; /* horizontal | vertical */
}



/* 🔍 Search input styling */
.dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    margin-left: 10px;
}

/* Non-Tailwind CSS */
#category-table td,
#category-table th {
    white-space: nowrap;
}


/* 🧾 Export Button styling (inherit from JS config) */
.dt-buttons {
    position: relative;
    z-index: 1;
    margin-left: 10px;
}


/* Ukuran tombol collection (export) */
.dt-button.buttons-collection {
    font-size: 0.875rem; /* text-sm */
    padding: 0.4rem 1rem;
}

.dt-button-down-arrow {
    display: none !important;
}

div.dt-button-collection {
    top: 100% !important;
    margin-top: 0.5rem !important; /* Jarak dari tombol */
    bottom: auto !important;
    left: auto !important;
    right: auto !important;
    z-index: 9999 !important;
}


/* Dropdown Export agar tampil di bawah */
div.dt-button-collection {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    margin-top: 0.5rem;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    z-index: 10000;
}

/* Item dropdown */
div.dt-button-collection .dt-button {
    color: #1f2937;
    padding: 0.5rem 1rem;
    text-align: left;
    width: 100%;
}

div.dt-button-collection .dt-button:hover {
    background-color: #dfe0e0ff;
}


/* 🧭 Spacing */
#category-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#category-table th, #category-table td {
    border: none !important;
}

.select2-container {
    width: 100% !important;
}


 .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 4px 10px !important;
        border: 1px solid #d1d5db !important; /* gray-300 */
        border-radius: 0.375rem !important; /* rounded-md */
        font-size: 1rem !important; /* text-base */
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        top: 1px;
    }

</style>
<script>
   window.currentUserRoles = @json(Auth::user()->roles->pluck('name')); 

   $(document).ready(function () {
    // Init Flatpickr
    $("#dateRangePicker").flatpickr({
        mode: "range",
        dateFormat: "Y-m-d"
    });

    // Open modal
    $("#openExportModalBtn").on("click", function () {
         console.log("Klik Export"); // debug
        $("#exportModal").removeClass("hidden");
        setTimeout(() => {
            $("#exportModalContent")
                .removeClass("scale-90 opacity-0")
                .addClass("scale-100 opacity-100");
        }, 50);
    });

    // Close modal
    $("#closeExportModalBtn").on("click", function () {
        $("#exportModalContent")
            .addClass("scale-90 opacity-0")
            .removeClass("scale-100 opacity-100");
        setTimeout(() => $("#exportModal").addClass("hidden"), 200);
    });

    $("#generateExcelBtn").on("click", function () {
    let range = $("#dateRangePicker").val();
    if (!range) {
        Swal.fire({
            icon: "warning",
            title: "Oops!",
            text: "Please select a date range first."
        });
        return;
    }

    // Redirect ke route export dengan query string
    let exportUrl = "{{ route('facility.booking-room.export') }}" + "?date_range=" + encodeURIComponent(range);
    window.location.href = exportUrl;
});

});

  $(document).ready(function () {

    
    const today = new Date().toISOString().split("T")[0];
    $("#datePicker").val(today);

    loadSchedule(today);

    $("#datePicker").on("change", function () {
        const selectedDate = $(this).val();
        loadSchedule(selectedDate);
    });

    function loadSchedule(date) {
        $.ajax({
            url: "{{ route('facility.booking-room.schedule') }}",
            method: "GET",
            data: { date: date },
            success: function (response) {
                console.log(response); // cek JSON
                window.rooms = response.rooms;
                renderSchedule(response.date, response.rooms, response.schedule);
            }
        });
    }

     // Fetch booking dari backend
    function loadBookings() {
        $.ajax({
            url: '/facility/booking-room/user-bookings',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                renderBookings(res);
            },
            error: function(err) {
                console.error(err);
                alert('Gagal mengambil data booking');
            }
        });
    }
    
$(document).on("click", "#saveBooking", function () {
    const bookingDate = $("#modalDate").text();
    const time = $("#modalTime").text(); // format "08:00 - 09:00 WIB"
    const [start_time, end_time] = time.replace(" WIB", "").split(" - ");
    const roomName = $("#modalRoom").text();
    const purpose = $("#modalPurpose").val();
    const description = $("#modalPurposeDescription").val();

    const selectedRoom = window.rooms.find(r => r.name === roomName);

    $.ajax({
        url: "{{ route('facility.booking-room.store') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            booking_date: bookingDate,
            start_time: start_time,
            end_time: end_time,
            purpose: purpose,
            description: description,
            room_id: selectedRoom.id,
        },
       success: function (res) {
    if (res.success) {
        Swal.fire({
            icon: "success",
            title: "Booking Succesfull",
            text: res.message,
            showConfirmButton: false,
            timer: 2000
        }).then(() => {
            $("#bookingModal").addClass("hidden");
            // reload full page
            loadSchedule($("#datePicker").val());
            loadBookings();
        });
    }
}

    });
});
function renderSchedule(date, rooms, schedule) {
    let html = "";
    const now = new Date(); // waktu sekarang

    $.each(schedule, function (time, roomStatuses) {
        html += `<tr><td class="border px-3 py-2 text-center">${time} WIB</td>`;

        $.each(rooms, function (i, room) {
            const roomData = roomStatuses[room.name];
            let cellHtml = "";

            if (roomData.status === "Booked") {
                cellHtml = `
                    <div class="w-full bg-red-500 text-white rounded px-3 py-1 relative booked-cell"
                         title="Booked by ${roomData.booked_by}\nPurpose: ${roomData.purpose || ''}\n${roomData.description || ''}">
                        Booked by ${roomData.booked_by}
                    </div>
                `;
            } else if (roomData.status === "Waiting Approval") {
    cellHtml = `
        <div class="w-full bg-yellow-500 text-white rounded px-3 py-1 text-center"
             title="Waiting for approval\nPurpose: ${roomData.purpose || ''}\n${roomData.description || ''}">
            Waiting for Approval
        </div>
    `;      
          } else {
                // Hitung waktu slot
                const [start, end] = time.replace(" WIB", "").split(" - ");
                const slotStart = new Date(`${date}T${start}:00`);
                const slotEnd   = new Date(`${date}T${end}:00`);
                const todayStr  = now.toISOString().split("T")[0];

                let isExpired = false;

                if (date < todayStr) {
                    // hari kemarin
                    isExpired = true;
                } else if (date === todayStr) {
                    if (slotEnd <= now) {
                        // slot sudah lewat (jam selesai < sekarang)
                        isExpired = true;
                    }
                }

                if (isExpired) {
                    cellHtml = `
                        <div class="w-full bg-gray-300 text-gray-600 rounded px-3 py-1 cursor-not-allowed text-center">
                            Expired
                        </div>
                    `;
                } else {
                    cellHtml = `
                        <button class="book-btn w-full bg-green-600 text-white rounded px-3 py-1 hover:bg-green-700 transition"
                                data-date="${date}"
                                data-time="${time}"
                                data-room="${room.name}">
                            Available
                        </button>
                    `;
                }
            }

            html += `<td class="text-center px-2 py-1">${cellHtml}</td>`;
        });

        html += `</tr>`;
    });

    $("#scheduleBody").html(html);

    // Update head
    let head = `<tr class="bg-white"><th class="border px-3 py-2 text-center">Time</th>`;
    $.each(rooms, function (i, room) {
        head += `<th class="border px-3 py-2 text-center">${room.name}</th>`;
    });
    head += `</tr>`;
    $("thead").html(head);

    $("#selectedDateText").text(`(${date})`);
}




// Delegasi event karena button dibuat dinamis
$(document).on("click", ".book-btn", function () {
    const date = $(this).data("date");
    const time = $(this).data("time");
    const room = $(this).data("room");

    // Ambil detail room dari array rooms yang sudah dikirim server
    const selectedRoom = window.rooms.find(r => r.name === room);

    $("#modalDate").text(date);
    $("#modalTime").text(time);
    $("#modalRoom").text(room);

    $("#modalCapacity").html(`<i class="fas fa-user"></i> ${selectedRoom.capacity}`);

    // Equipment list
    let eqList = "";
    if (selectedRoom.equipment && selectedRoom.equipment.length > 0) {
        selectedRoom.equipment.forEach(eq => {
            eqList += `<li>${eq}</li>`;
        });
    } else {
        eqList = "<li>-</li>";
    }
    $("#modalEquipment").html(eqList);

    $("#modalLocation").text(selectedRoom.location);

    $("#bookingModal").removeClass("hidden");
});

$(document).on("click", "#closeModal", function () {
    $("#bookingModal").addClass("hidden");
});

   function renderBookings(bookings) {
    const now = new Date();
    $("#ongoingList").empty();
    $("#upcomingList").empty();

    bookings.forEach(b => {
        const bookingDate = new Date(b.booking_date);
        const startTime = b.start_time.substring(0, 5);
        const endTime   = b.end_time.substring(0, 5);

        const bookingStart = new Date(`${b.booking_date}T${startTime}`);
        const bookingEnd   = new Date(`${b.booking_date}T${endTime}`);

        // Format tanggal untuk Upcoming
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        const formattedDate = bookingDate.toLocaleDateString('id-ID', options);

        // --- Tombol aksi ---
        let actionButtons = `
            <button class="cancelBtn w-24 text-xs bg-red-500 p-2 text-white font-medium hover:bg-red-700 rounded-md" 
                    data-id="${b.id}">
                Cancel
            </button>
        `;

        // Jika masih Waiting dan role Admin GA → tambah tombol Approve
        if (b.status === "Waiting Approval" && window.currentUserRoles.includes("Admin GA")) {
            actionButtons += `
                <button class="approveBtn w-24 text-xs bg-green-600 p-2 text-white font-medium hover:bg-green-700 rounded-md" 
                        data-id="${b.id}">
                    Approve
                </button>
            `;
        }

        
       // --- Card booking ---
let li = `
<li class="p-4 bg-white rounded-xl shadow hover:shadow-lg transition flex flex-col gap-2">
  <div class="flex items-start space-x-3">
      <!-- Konten (Header + Body) -->
      <div class="flex flex-col gap-1">
          <!-- Header -->
          <div class="flex items-center gap-2 text-indigo-600 font-semibold">
              <i data-feather="clock" class="w-4 h-4"></i>
              <span>
                  ${bookingDate.toDateString() === now.toDateString() 
                      ? `${startTime} - ${endTime}` 
                      : `${formattedDate} | ${startTime} - ${endTime}`} WIB
              </span>
          </div>

          <!-- Body -->
          <div class="flex items-center gap-2 text-gray-700">
              <i data-feather="map-pin" class="w-4 h-4"></i>
              <span class="font-medium">${b.room.name}</span> 
              <span class="text-gray-500">Booked by <span class="underline">${b.creator.name}</span> for ${b.purpose}</span>
          </div>
      </div>
  </div>

  <!-- Footer -->
  <div class="flex items-center justify-start gap-2 pt-2">
      ${actionButtons}
  </div>
</li>
`;



        if (bookingDate.toDateString() === now.toDateString() && bookingEnd >= now) {
            $("#ongoingList").append(li);
            feather.replace();
        } else if (bookingStart > now) {
            $("#upcomingList").append(li);
            feather.replace();
        }
    });

    // Jika kosong kasih keterangan
    if ($("#ongoingList").children().length === 0) {
        $("#ongoingList").html(`<li class="text-gray-400 italic">Tidak ada ruangan yang kamu booking hari ini</li>`);
    }
    if ($("#upcomingList").children().length === 0) {
        $("#upcomingList").html(`<li class="text-gray-400 italic">Tidak ada ruang yang kamu booking untuk nanti</li>`);
    }
}
  // Cancel booking
$(document).on('click', '.cancelBtn', function() {
    const id = $(this).data('id');

    // cek role user dari variabel global
    const isAdminGA = (window.currentUserRoles.includes("Admin GA"));

    if (isAdminGA) {
        // Admin GA → wajib isi alasan cancel
        Swal.fire({
            title: 'Cancel this booking?',
            input: 'textarea',
            inputLabel: 'Reason for cancellation',
            inputPlaceholder: 'Enter the reason here...',
            inputAttributes: {
                'aria-label': 'Reason for cancellation'
            },
            showCancelButton: true,
            confirmButtonText: 'Submit',
            cancelButtonText: 'Back',
            inputValidator: (value) => {
                if (!value) {
                    return 'Reason is required!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/facility/booking-room/cancel/' + id,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        cancel_reason: result.value
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadSchedule($("#datePicker").val());
                        loadBookings();
                    },
                    error: function(err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Failed to cancel booking',
                        });
                    }
                });
            }
        });

    } else {
        // User biasa → langsung confirm cancel
        Swal.fire({
            title: 'Cancel this booking?',
            text: "This booking will be cancelled and cannot be recovered.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'Back'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/facility/booking-room/cancel/' + id,
                    type: 'POST',
                    data: {_token: $('meta[name="csrf-token"]').attr('content')},
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        loadSchedule($("#datePicker").val());
                        loadBookings();
                    },
                    error: function(err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Failed to cancel booking',
                        });
                    }
                });
            }
        });
    }
});


// Handle Approve button
$(document).on("click", ".approveBtn", function () {
    const id = $(this).data("id");

    Swal.fire({
        title: "Approve Booking?",
        text: "Approve this Booking?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Yes, Approve",
        cancelButtonText: "Back"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('facility.booking-room.approve') }}", // route approve
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Approved!",
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        loadBookings(); // refresh list
                        loadSchedule($("#datePicker").val()); // refresh schedule
                    } else {
                        Swal.fire("Error", res.message, "error");
                    }
                },
                error: function () {
                    Swal.fire("Error", "Terjadi kesalahan saat approve booking", "error");
                }
            });
        }
    });
});



// Load saat halaman pertama kali
loadBookings();
loadSchedule(today);
});



</script>
@endpush
@endsection