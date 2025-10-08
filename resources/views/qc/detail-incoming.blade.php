@extends('layouts.app')

@section('title', 'Detail Incoming Inspection')
@section('page-title', 'DETAIL INCOMING INSPECTION')
@section('breadcrumb-item', 'Incoming Inspection')
@section('breadcrumb-active', 'Detail Incoming Inspection')

@section('content')
  @php
          $status = $inspection->status ?? '';
$colorClass = match($status) {
    'DRAFT'    => 'text-gray-600 bg-gray-200',
    'VERIFIED' => 'text-white bg-green-600',
    default    => 'text-gray-700 bg-gray-100',
};


          $months = [
      '01' => 'JANUARI',
      '02' => 'FEBRUARI',
      '03' => 'MARET',
      '04' => 'APRIL',
      '05' => 'MEI',
      '06' => 'JUNI',
      '07' => 'JULI',
      '08' => 'Agustus',
      '09' => 'SEPTEMBER',
      '10' => 'OKTOBER',
      '11' => 'NOVEMBER',
      '12' => 'DESEMBER',
  ];

  [$year, $month] = explode('-', $inspection->periode);
  $monthName = $months[$month] ?? $month;
        @endphp
  <!-- Main Panel -->
  <div class="w-full bg-white shadow-lg rounded-xl p-6 space-y-8">

    {{-- HEADER --}}
   <div class="border-b border-gray-300 pb-4 space-y-3">

  {{-- Baris 1: Incoming Number & Status --}}
  <div class="flex items-center gap-4">
    <h1 class="text-3xl font-extrabold text-gray-900">{{ $inspection->incoming_number }}</h1>

    <span class="px-3 py-1 rounded-full font-semibold text-sm {{ $colorClass }}">
      {{ strtoupper($inspection->status ?? '-') }}
    </span>
  </div>

  {{-- Baris 2: Supplier & Part name di kiri, Periode & User di kanan --}}
  <div class="flex justify-between items-center text-gray-600 text-sm">

    <div class="flex gap-6">
      <div class="flex items-center gap-2">
        <span class="font-medium">{{ $inspection->supplier->name ?? '-' }}</span> -  <span class="font-medium">{{ $inspection->article->description ?? '-' }}</span>
      </div>
    </div>

    <div class="flex gap-6">
      <div class="flex items-center gap-2">
        <i data-feather="calendar" class="w-5 h-5 text-gray-400"></i>
        <span>Periode {{ $monthName }} {{ $year }}</span>
      </div>
      <div class="flex items-center gap-2">
        <i data-feather="user" class="w-5 h-5 text-gray-400"></i>
        <span>{{ optional($inspection->user)->name ?? '-' }}</span>
      </div>
    </div>

  </div>
  
</div>


    {{-- CHARTS --}}
    <div class="flex flex-col md:flex-row gap-8">
      {{-- Pareto Defect --}}
      <div class="flex-1 bg-white p-6 rounded-xl shadow-xl">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b border-gray-300 pb-2">Pareto Defect (%)</h2>
        <canvas id="paretoDefectChart" height="100"></canvas>
      </div>

      {{-- Pie Chart OK vs NG --}}
      <div class="w-72 bg-white p-6 rounded-xl shadow-xl">
        <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b border-gray-300 pb-2">Summary Inspection</h2>
        <canvas id="defectPieChart" height="100"></canvas>
      </div>
    </div>

    {{-- Line Chart Pass Rate --}}
    <div class="bg-white p-6 rounded-xl shadow-xl">
      <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b border-gray-300 pb-2">Pass Rate (%)</h2>
      <canvas id="passRateChart" height="90"></canvas>
    </div>

   {{-- TABLES --}}
{{-- TABLES --}}
<div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 p-4 bg-white">
  <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b border-gray-300 pb-2">Total Inspection</h2>
  <table class="min-w-full text-center border-collapse">
    <thead class="bg-blue-600 text-white">
      <tr>
        <th class="p-3 border-r border-blue-700 text-left">Item</th>
        @foreach ($dates as $date)
          <th class="p-3 border-r border-blue-700">{{ $date }}</th>
        @endforeach
        <th class="p-3">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($itemLabels as $label)
        @php
          // Tentukan warna berdasarkan label
          $textColor = match($label) {
              'Total OK' => 'text-green-600',
              'Total OK Repair' => 'text-yellow-600',
              'Total NG' => 'text-red-600',
              default => 'text-gray-700'
          };
        @endphp

        <tr class="even:bg-gray-50 hover:bg-blue-50 transition-colors">
          <th class="p-3 border-r border-gray-200 text-left font-medium {{ $textColor }}">
            {{ $label }}
          </th>

          @foreach ($dates as $date)
            <td class="p-3 border-r border-gray-200 {{ $textColor }}">
              {{ $totals[$label][$date] ?? 0 }}
            </td>
          @endforeach

          <td class="p-3 font-semibold {{ $textColor }}">
            {{ $totals[$label]['total'] ?? 0 }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>



    <div class="overflow-x-auto rounded-lg shadow-md border border-gray-200 p-4 bg-white mt-8">
      <h2 class="text-xl font-semibold text-gray-800 mb-6 border-b border-gray-300 pb-2">Defect</h2>
      <table class="min-w-full text-center shadow-md border-collapse">
        <thead class="bg-blue-600 text-white">
          <tr>
            <th class="p-3 border-r border-blue-700 text-left">Defect</th>
            @foreach ($dates as $date)
              <th class="p-3 border-r border-blue-700">{{ $date }}</th>
            @endforeach
            <th class="p-3">Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($defectTotals as $defect => $values)
          <tr class="even:bg-gray-50 hover:bg-blue-50 transition-colors">
            <th class="p-3 border-r border-gray-200 text-left font-medium text-gray-700">{{ $defect }}</th>
            @foreach ($dates as $date)
              <td class="p-3 border-r border-gray-200">{{ $values[$date] ?? 0 }}</td>
            @endforeach
            <td class="p-3 font-semibold text-red-600">{{ array_sum($values) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Summary Box --}}
    <div class="flex justify-end mt-8">
      <div class="w-full md:w-1/3 bg-gray-100 p-5 rounded-xl shadow-md space-y-4">
        <div class="flex justify-between items-center text-gray-700 font-semibold">
          <span>OK Percentage</span>
          <span class="text-green-600">{{ $summary['ok_percentage'] ?? '0%' }}</span>
        </div>
        <div class="flex justify-between items-center text-gray-700 font-semibold">
          <span>NG Percentage</span>
          <span class="text-red-600">{{ $summary['ng_percentage'] ?? '0%' }}</span>
        </div>
        <div class="flex justify-between items-center text-gray-700 font-semibold">
          <span>OK Repair Percentage</span>
          <span class="text-yellow-600">{{ $summary['ok_repair_percentage'] ?? '0%' }}</span>
        </div>
      </div>
    </div>

  <div class="text-left border-t py-4 flex gap-2">
    <a href="{{ route('qc.incoming.index') }}" class="flex items-center justify-center gap-2 w-28 bg-gray-400 px-4 text-white py-2 rounded">
        <i data-feather="arrow-left" class="w-4 h-4"></i> Back
</a>

    <button type="button" class="flex items-center justify-center gap-2 w-28 bg-purple-600 text-white px-4 py-2 rounded">
        <i data-feather="printer" class="w-4 h-4"></i> Print
    </button>

    @if(
    $inspection->status === 'DRAFT' &&
    auth()->user()->roles->contains('name', 'Supervisor Special Access') &&
    auth()->user()->departments->contains('name', 'Quality Control')
)
    <button 
        type="button" 
        data-id="{{ $inspection->id }}"
        class="btn-verified w-28 bg-green-600 text-white px-4 py-2 rounded flex items-center gap-1">
        <i data-feather="check-circle" class="w-4 h-4"></i> Verified
    </button>
@endif

</div>

</div>

@push('scripts')
<script>
   $(document).ready(function () {
    $('.btn-verified').on('click', function (e) {
        e.preventDefault();

        let id = $(this).data('id');
        let url = `/qc/incoming/${id}/verified`;

        if (confirm('Verify this Number?')) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    alert(response.message || 'Status berhasil diverifikasi');
                    location.reload(); // Refresh tabel atau halaman
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('Gagal memverifikasi data');
                }
            });
        }
    });
});


    $(document).ready(function () {
        
const ctxPie = document.getElementById('defectPieChart').getContext('2d');

new Chart(ctxPie, {
  type: 'pie',
  data: {
    labels: ['OK', 'NG'],
    datasets: [{
      data: [{{ $totalOk ?? 0 }}, {{ $totalNg ?? 0 }}],
      backgroundColor: [
        'rgba(34, 197, 94, 0.7)',  // hijau
        'rgba(239, 68, 68, 0.7)'   // merah
      ],
      borderColor: [
        'rgba(34, 197, 94, 1)',
        'rgba(239, 68, 68, 1)'
      ],
      borderWidth: 1
    }]
  },
  options: {
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          font: {
            size: 14
          }
        }
      },
      datalabels: {
        color: '#fff',
        formatter: function(value, context) {
          // Hitung persentase berdasarkan total dataset
          const data = context.chart.data.datasets[0].data;
          const total = data.reduce((a, b) => a + b, 0);
          const percentage = total ? (value / total * 100).toFixed(0) : 0;
          return percentage + '%';
        },
        font: {
          weight: 'bold',
          size: 16
        }
      }
    }
  },
  plugins: [ChartDataLabels]

  });

  const ctx = document.getElementById('paretoDefectChart').getContext('2d');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: @json($defectLabels),
    datasets: [{
      label: 'Defect (%)',
      data: @json($defectValues),
      backgroundColor: 'rgba(239, 68, 68, 0.7)', // merah semi transparan
      borderColor: 'rgba(239, 68, 68, 1)', // merah solid
      borderWidth: 1,
      borderRadius: 4,
      barPercentage: 0.6,
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
        max: 100,
        ticks: {
          stepSize: 10,
          callback: function(value) {
            return value + '%';
          }
        },
        title: {
          display: true,
          text: 'Persentase (%)'
        }
      },
      x: {
        title: {
          display: true,
          text: 'Defect'
        },
        ticks: {
          maxRotation: 45,
          minRotation: 30,
          autoSkip: false
        }
      }
    },
    plugins: {
      legend: {
        display: false
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            return context.parsed.y + '%';
          }
        }
      },
      datalabels: {
        anchor: 'end',
        align: 'end',
        formatter: function(value) {
          return value + '%';
        },
        font: {
          weight: 'bold',
          size: 12
        },
        color: '#333'
      }
    }
  },
  plugins: [ChartDataLabels]  // aktifkan plugin datalabels
});
});

const ctxPassRate = document.getElementById('passRateChart').getContext('2d');

new Chart(ctxPassRate, {
  type: 'line',
  data: {
    labels: @json($allDates),  // tanggal 1 sampai akhir bulan
    datasets: [{
      label: 'Pass Rate (%)',
      data: @json(array_values($passRateByDate)), // data dengan 0 jika tanggal kosong
      borderColor: 'rgba(34, 197, 94, 1)',
      backgroundColor: 'rgba(34, 197, 94, 0.3)',
      fill: true,
      tension: 0.3,
      pointRadius: 6,
      pointHoverRadius: 8,
      borderWidth: 3,
      pointBorderColor: 'white',
      pointBorderWidth: 2,
    }]
  },
  options: {
    scales: {
      y: {
        position: 'left',
        beginAtZero: true,
        max: 100,
        ticks: {
          stepSize: 10,
          callback: val => val + '%',
        },
        title: {
          display: true,
          text: 'Pass Rate (%)'
        }
      },
      x: {
        title: {
          display: true,
          text: 'Tanggal'
        }
      }
    },
    plugins: {
      legend: { display: true },
      tooltip: {
        callbacks: {
          label: ctx => ctx.parsed.y + '%'
        }
      }
    }
  }
});
</script>
@endpush
@endsection
