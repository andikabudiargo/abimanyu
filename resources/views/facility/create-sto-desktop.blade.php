 <!-- DESKTOP VERSION -->
  <div class="pc-container ml-[264px] p-6 min-h-screen">
    <!-- Page Header: hidden di mobile & tablet -->
  <div class="page-header">
    <div class="page-block flex items-center justify-start lg:justify-between gap-4">
      
      <!-- Page Title -->
      <div class="page-header-title">
        <h5 class="mb-0 font-medium">@yield('page-title', 'Dashboard')</h5>
      </div>

      <!-- Breadcrumb -->
      <ul class="mb-0 text-xs text-gray-500 flex items-center">
        <li class="flex items-center">
          <a href="{{ url('/') }}" class="text-gray-600 hover:underline">
            <i data-feather="home" class="w-4 h-4"></i>
          </a>
          <span class="mx-2 text-gray-400">›</span>
        </li>
        <li class="flex items-center">
          <span>@yield('breadcrumb-item')</span>
          <span class="mx-2 text-gray-400">›</span>
        </li>
        <li class="text-gray-800 font-medium">@yield('breadcrumb-active')</li>
      </ul>
      
    </div>
  </div>

    <div class="w-full bg-white p-0 rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

      <!-- HEADER BAR -->
      <div class="bg-blue-500 px-10 py-8 border-b border-blue-600">
        <h1 class="text-3xl font-semibold tracking-wide text-white">
          ELECTRONIC STOCK OPNAME (e-STO)
        </h1>
        <p class="text-sm text-white mt-1 opacity-80">
          Sistem Pencatatan Stock Opname Digital
        </p>
      </div>

      <!-- CONTENT WRAPPER -->
      <div class="p-10">

        <!-- TOP GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 mb-10 items-center">
          <div class="flex-shrink-0">
            <img src="{{ asset('img/logo-2.jpg') }}"
              alt="Company Logo"
              class="h-14 sm:h-20 w-auto opacity-90">
          </div>

        
           <div>
  <label class="block text-sm font-medium text-gray-600 mb-1">
    STO Number
  </label>

  <select name="sto_number" id="sto_number"
    class="w-full px-3 py-2 border border-gray-300 rounded-lg
           focus:ring-[#8b5cf6] focus:border-[#8b5cf6]
           bg-gray-50 text-gray-700">

  @php
  $year  = 2026;
  $month = '04'; // langsung string saja

  $month = str_pad($month, 2, '0', STR_PAD_LEFT);

   $stoRange = [
    'Dead Stock CM1' => [1, 48],
    'Chemical'       => [1000, 1143],
    'Consumable'     => [2000, 2095],
    'Raw Material'   => [3000, 3145],
    'WIP Buffing'    => [5000, 5134],
    'WIP Sanding'    => [6000, 6146],
    'WIP Touch Up'   => [7000, 7999],
    'Finish Goods'   => [4000, 4145],
    'OT'             => [51, 200],
    'Werate'         => [8000, 8136],
  ];

  $ranges = [];

  if (is_array($allowedWarehouses)) {
    foreach ($allowedWarehouses as $wh) {
      if (isset($stoRange[$wh])) {
        $ranges[] = $stoRange[$wh];
      }
    }
  }

  if (empty($ranges)) {
    $ranges = array_values($stoRange);
  }
@endphp

@foreach ($ranges as [$start, $end])
  @for ($i = $start; $i <= $end; $i++)
    @php
      $number = str_pad($i, 4, '0', STR_PAD_LEFT);
      $val = "{$year}/{$month}/{$number}";
    @endphp

    @if (!in_array($val, $usedStoNumbers))
      <option value="{{ $val }}">{{ $val }}</option>
    @endif
  @endfor
@endforeach

  </select>
</div>

        </div>


        <!-- TABLE SECTION -->
        <div class="mb-10">
          <div class="overflow-x-auto border border-gray-200 rounded-xl bg-white shadow-xs">
            <table class="w-full text-sm">
              <thead class="bg-blue-500 text-white border-b border-blue-600">
                <tr>
                  <th class="px-4 py-3 text-left font-medium">PART CODE</th>
                  <th class="px-4 py-3 text-left font-medium">PART NAME</th>
                  <th class="px-4 py-3 text-center font-medium w-32">QTY</th>
                  <th class="px-4 py-3 text-center font-medium w-32">QTY BOX</th>
                   <th class="px-4 py-3 text-center font-medium w-32">UOM</th>
                  <th class="px-4 py-3 text-center font-medium w-48">LOCATION

  @if(in_array(auth()->id(), [2, 53]))
    
    @if($warehouse === null)
      <select name="warehouse"
              id="warehouse-null-desktop"
              class="mt-1 w-full text-black text-sm rounded px-1 py-1">
        <option value="">-- Pilih Gudang --</option>

         @foreach($allowedWarehouses as $wh)
          <option value="{{ $wh }}">{{ $wh }}</option>
        @endforeach
      </select>
    @else
      <input type="text"
             class="mt-1 w-full bg-gray-100 text-sm rounded px-1 py-1"
             value="{{ $warehouse }}"
             readonly>
    @endif

  @endif
                  </th>
                </tr>
              </thead>

              <tbody id="article-table" class="divide-y divide-gray-100">
                @for ($i = 0; $i < 8; $i++)
                  <tr class="sto-row">
                    <input type="hidden" name="articles[{{ $i }}][other_name]" class="other-name-input">

                    <td class="px-3 py-2">
                      <input type="text"
                        name="articles[{{ $i }}][article_code]"
                        class="article-code w-full border border-gray-300 rounded-lg px-2 py-1 bg-gray-50"
                        readonly>
                    </td>

                    <td class="px-3 py-2">
                      <select class="part-select w-full border-gray-300 rounded-lg focus:ring-[#a78bfa] focus:border-[#a78bfa]"
                          name="articles[{{ $i }}][article_id]"
                          data-row="{{ $i }}">
                        <option value=""> -- pilih part -- </option>
                        @foreach ($articles as $a)
                          <option value="{{ $a->id }}"
                            data-code="{{ $a->article_code }}"
                            data-uom="{{ $a->unit }}"
                            data-min-package="$a->min_package">
                            {{ $a->description }}
                          </option>
                        @endforeach
                      </select>
                    </td>

                    <td class="px-3 py-2 text-center">
                      <input type="number" min="0"
                        name="articles[{{ $i }}][qty]"
                        class="w-full border border-gray-300 rounded-lg px-2 py-1 text-center">
                    </td>

                    <td class="px-3 py-2 text-center">
                      <input type="text"
                        name="articles[{{ $i }}][min_package]"
                        class="part-min-package w-full border border-gray-300 rounded-lg px-2 py-1 text-center bg-gray-50"
                        readonly>
                    </td>

                    <td class="px-3 py-2 text-center">
                      <input type="text"
                        name="articles[{{ $i }}][uom]"
                        class="part-uom w-full border border-gray-300 rounded-lg px-2 py-1 text-center bg-gray-50"
                        readonly>
                    </td>

                     

                    <td class="px-3 py-2 text-center">
                      <input type="text"
                        name="articles[{{ $i }}][location]"
                        value="{{ $warehouse }}"
                        readonly
                        class="location-input w-full bg-gray-100 border border-gray-300 rounded-lg px-2 py-1">
                    </td>
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>
        </div>



        <!-- NOTE + BUTTONS -->
        <div class="flex flex-col md:flex-row md:justify-between gap-10">

          <div class="w-full md:w-2/3">
            <label class="block text-sm font-medium text-gray-600 mb-2">
              Catatan / Note
            </label>
            <textarea id="note" name="note" rows="3"
              class="w-full resize-none border border-gray-300 rounded-xl p-3 bg-gray-50 focus:ring-[#a78bfa] focus:border-[#a78bfa]"></textarea>
          </div>

          <div class="flex justify-end gap-4 self-end">
            <a href="{{ url()->previous() }}"
              class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100">
              Back
            </a>

            <button type="submit" id="btnSave"
              class="px-6 py-2 bg-green-500 text-white rounded-lg shadow-sm hover:bg-green-600 transition">
              Save
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>