@extends('layouts.app')

@section('title', 'IT Project Management')
@section('page-title', 'DASHBOARD PROJECT')
@section('breadcrumb-item', 'IT Project Management')
@section('breadcrumb-active', 'IT Project Management')

@section('content')

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
  <div class="p-4 bg-blue-100 rounded-lg">
    <div class="text-sm text-blue-700">Total Project</div>
    <div class="text-xl font-bold">152</div>
  </div>
  <div class="p-4 bg-yellow-100 rounded-lg">
    <div class="text-sm text-yellow-700">Project in Progress</div>
    <div class="text-xl font-bold">23</div>
  </div>
  <div class="p-4 bg-green-100 rounded-lg">
    <div class="text-sm text-green-700">Project Done</div>
    <div class="text-xl font-bold">119</div>
  </div>
  <div class="p-4 bg-red-100 rounded-lg">
    <div class="text-sm text-red-700">Overdue</div>
    <div class="text-xl font-bold">10</div>
  </div>
</div>


   <div class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Filter Project</h2>

    <form id="filter-form">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Project Number</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Date</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Type</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Status</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
             <div>
                <label for="filterCustomer" class="block text-sm mb-1 font-medium text-gray-700">Department</label>
                <input type="text" class="w-full px-3 py-1 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
            </div>
            </div>

        <div class="flex justify-start gap-2 mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">Search</button>
            <a href="{{ route('it.project.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Create</a>
        </div>
    </form>
</div>

   {{-- 📄 TABEL --}}
<div class="table-responsive bg-white shadow rounded-xl p-6 mb-2">
    <h2 class="text-lg font-semibold mb-2">Project List</h2>
    <div class="w-full overflow-x-auto" id="ticket-scroll-wrapper">
    <table id="ticket-table" class="min-w-full text-sm text-left whitespace-nowrap">
            <thead class="bg-blue-500 text-white uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-2">Action</th>
                    <th class="px-4 py-2">Project Number</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2">Subject</th>
                    <th class="px-4 py-2">PIC</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Progress</th>
                    <th class="px-4 py-2">Created by</th>
                    <th class="px-4 py-2">Created at</th>
                    <th class="px-4 py-2">Closed at</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
    <tr>
        <td class="px-4 py-2">
            <a href="#" class="text-blue-600 hover:underline">Detail</a>
        </td>
        <td class="px-4 py-2">PRJ-001</td>
        <td class="px-4 py-2">Application</td>
        <td class="px-4 py-2">Internal Helpdesk System</td>
        <td class="px-4 py-2">Andi</td>
        <td class="px-4 py-2">
            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">In Progress</span>
        </td>
        <td class="px-4 py-2 w-48">
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-yellow-500 h-5 rounded-full text-xs text-white text-center" style="width: 45%">45%</div>
            </div>
        </td>
        <td class="px-4 py-2">Rina</td>
        <td class="px-4 py-2">2025-07-01</td>
        <td class="px-4 py-2 text-gray-400 italic">-</td>
    </tr>
    <tr>
        <td class="px-4 py-2">
            <a href="#" class="text-blue-600 hover:underline">Detail</a>
        </td>
        <td class="px-4 py-2">PRJ-002</td>
        <td class="px-4 py-2">Infrastructure</td>
        <td class="px-4 py-2">Server Migration</td>
        <td class="px-4 py-2">Doni</td>
        <td class="px-4 py-2">
            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Done</span>
        </td>
        <td class="px-4 py-2 w-48">
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-green-500 h-5 rounded-full text-xs text-white text-center" style="width: 100%">100%</div>
            </div>
        </td>
        <td class="px-4 py-2">Ayu</td>
        <td class="px-4 py-2">2025-06-15</td>
        <td class="px-4 py-2">2025-07-10</td>
    </tr>
    <tr>
        <td class="px-4 py-2">
            <a href="#" class="text-blue-600 hover:underline">Detail</a>
        </td>
        <td class="px-4 py-2">PRJ-003</td>
        <td class="px-4 py-2">Security</td>
        <td class="px-4 py-2">VPN Firewall Upgrade</td>
        <td class="px-4 py-2">Tika</td>
        <td class="px-4 py-2">
            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Overdue</span>
        </td>
        <td class="px-4 py-2 w-48">
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-red-500 h-5 rounded-full text-xs text-white text-center" style="width: 60%">60%</div>
            </div>
        </td>
        <td class="px-4 py-2">Bayu</td>
        <td class="px-4 py-2">2025-06-01</td>
        <td class="px-4 py-2 text-red-600 font-semibold">Overdue!</td>
    </tr>
</tbody>

        </table>
    </div>
</div>


{{-- SCRIPT --}}
@push('scripts')
<style>
/* Ubah warna baris even dan odd */
#ticket-table tbody tr:nth-child(even) {
     background-color: #f3f4f6; /* lebih gelap: tailwind slate-100 */
}
#ticket-table tbody tr:nth-child(odd) {
    background-color: #ffffff;
}

/* 🔍 Search input styling */
.dataTables_filter input {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 6px 10px;
    margin-left: 10px;
}

/* 🧾 Export Button styling (inherit from JS config) */
.dt-buttons {
    margin-left: 10px;
}

/* 🧭 Spacing */
#ticket-table_wrapper {
    margin-top: 2rem;
    margin-bottom: 2rem;
}

/* Hilangkan border samping */
#ticket-table th, #ticket-table td {
    border: none !important;
}

/* Biar teks tidak wrap */
#ticket-table td, #ticket-table th {
    white-space: nowrap;
}

/* Biar scroll horizontal muncul hanya untuk tabel, bukan seluruh container */
#ticket-scroll-wrapper {
    overflow-x: auto;
    padding-bottom: 8px;
    margin-bottom: 1rem;
}
.table-scroll-wrapper {
    overflow-x: auto;
}

</style>
<script>
    function rejectTicket(id) {
        document.getElementById('reject_ticket_id').value = id;
        document.getElementById('reject_reason').value = '';
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

  function showHoldReason(reason, start, duration) {
    document.getElementById('modal_hold_reason').innerText = reason;
    document.getElementById('modal_hold_start').innerText = start;
    document.getElementById('modal_hold_duration').innerText = duration;
    document.getElementById('holdReasonModal').classList.remove('hidden');
  }

  function closeHoldReasonModal() {
    document.getElementById('holdReasonModal').classList.add('hidden');
  }

    document.getElementById('hold_reason').addEventListener('change', function () {
    const container = document.getElementById('custom_hold_reason_container');
    if (this.value === 'Other') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
});

    function openProcessModal(ticketId) {
    $('#process_ticket_id').val(ticketId);
    $('#processForm').attr('action', `/it/ticket/${ticketId}/process`);
    $('#processModal').removeClass('hidden');
}

function closeProcessModal() {
    $('#processModal').addClass('hidden');
}

function openHoldModal(ticketId) {
    document.getElementById('hold_ticket_id').value = ticketId;
    document.getElementById('holdModal').classList.remove('hidden');
}

function closeHoldModal() {
    document.getElementById('holdModal').classList.add('hidden');
}

function showDoneModal(ticketId) {
    document.getElementById('modal_ticket_id').value = ticketId;
    document.getElementById('doneModal').classList.remove('hidden');
  }

  function hideDoneModal() {
    document.getElementById('doneModal').classList.add('hidden');
  }

  $('#rejectForm').on('submit', function (e) {
    e.preventDefault();

    let form = $(this);
    let ticketId = $('#reject_ticket_id').val();
    let data = form.serialize();

    $.post(`/it/ticket/${ticketId}/reject`, data, function (res) {
        if (res.success) {
            alert(res.message);
            location.reload();
        } else {
            alert("Failed: " + res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        alert('An error occurred.');
    });
});

$('#holdForm').on('submit', function (e) {
    e.preventDefault();

    let form = $(this);
    let ticketId = $('#hold_ticket_id').val();
    let data = form.serialize();

    $.post(`/it/ticket/${ticketId}/hold`, data, function (res) {
        if (res.success) {
            alert(res.message);
            location.reload();
        } else {
            alert("Failed: " + res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        alert('An error occurred.');
    });
});


$('#processForm').on('submit', function (e) {
    e.preventDefault();

    let form = $(this);
    let action = form.attr('action');
    let data = form.serialize();

    $.post(action, data, function (res) {
        if (res.success) {
            alert('Ticket is now in progress!');
            location.reload();
        } else {
            alert(res.message);
        }
    }).fail(function (err) {
        console.error(err.responseText);
        alert('An error occurred while processing.');
    });
});

function resumeTicket(id) {
  if (confirm('Resume this ticket and continue progress?')) {
    $.post(`/it/ticket/${id}/resume`, {
      _token: '{{ csrf_token() }}'
    }, function (res) {
      if (res.success) {
        alert('Ticket resumed to In Progress.');
        location.reload();
      } else {
        alert(res.message || 'Failed to resume ticket.');
      }
    }).fail(function (err) {
      console.error(err.responseText);
      alert('An error occurred while resuming the ticket.');
    });
  }
}

$('#doneForm').on('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const ticketId = $('#modal_ticket_id').val();

    $.ajax({
      url: `/it/ticket/${ticketId}/done`,
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(res) {
        alert(res.success);
        hideDoneModal();
        location.reload();
      },
      error: function(err) {
        alert(err.responseJSON.error || 'Terjadi kesalahan');
      }
    });
  });

  function closeTicket(id) {
    if (confirm('Are you sure you want to close this ticket?')) {
        $.post(`/it/ticket/${id}/close`, {
            _token: '{{ csrf_token() }}'
        }, function (res) {
            if (res.success) {
                alert('Ticket has been closed successfully.');
                location.reload();
            } else {
                alert(res.message || 'Failed to close ticket.');
            }
        }).fail(function () {
            alert('Server error while closing the ticket.');
        });
    }
}

 $(document).ready(function () {
    const table = $('#ticket-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
         drawCallback: function(settings) {
    feather.replace(); // <-- WAJIB di sini
   // Tambahkan scroll wrapper hanya jika belum ada
    if (!$('#ticket-table').parent().hasClass('scroll-wrapper')) {
        $('#ticket-table').wrap('<div class="scroll-wrapper overflow-x-auto"></div>');
    }
},
      ajax: '{{ route("it.ticket.data") }}',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"flex justify-between items-center mb-2"l<"flex"fB>>rt<"flex justify-between items-center"ip>',
        buttons: [
            {
                extend: 'collection',
                text: 'Export',
                className: 'bg-blue-600 text-white px-4 py-1 rounded shadow-sm',
                buttons: [
                    {
                        extend: 'copyHtml5',
                        text: 'Copy',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        exportOptions: { columns: ':visible' }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        exportOptions: { columns: ':visible' }
                    }
                ]
            }
        ],
      columns: [
        { data: 'action', name: 'action', orderable: false, searchable: false },
        { data: 'ticket_number', name: 'ticket_number' },
        { data: 'category', name: 'category' },
        { data: 'title', name: 'title' },
        { data: 'status', name: 'status' },
        { data: 'department', name: 'requestor.departments.name' }, // relasi pivot
        { data: 'request_by', name: 'requestor.name' },
        { data: 'created_at', name: 'created_at' },
        { data: 'approved_by', name: 'approved.name' },
        { data: 'approved_at', name: 'approved_at' },
        { data: 'processed_by', name: 'process.name' },
        { data: 'processed_at', name: 'processed_at' },
        { data: 'done_at', name: 'done_at' },
        { data: 'closed_at', name: 'closed_at' },
      ]
    });
  });
  let openDropdown = null;

function toggleDropdown(id) {
  const dropdown = document.getElementById(id);

  // Tutup dropdown lain
  if (openDropdown && openDropdown !== dropdown) {
    openDropdown.classList.add('hidden');
  }

  dropdown.classList.toggle('hidden');
  openDropdown = dropdown.classList.contains('hidden') ? null : dropdown;
}

// Tutup dropdown saat klik di luar
document.addEventListener('click', function (e) {
  if (openDropdown && !openDropdown.contains(e.target)) {
    const isTrigger = e.target.closest('button[onclick^="toggleDropdown"]');
    if (!isTrigger) {
      openDropdown.classList.add('hidden');
      openDropdown = null;
    }
  }
  });
  
  function approveTicket(id) {
  if (confirm("Yakin ingin APPROVE tiket ini?")) {
    // TODO: lakukan ajax atau redirect
    alert("Approved ticket ID: " + id);
  }
}

  </script>
@endpush


@endsection