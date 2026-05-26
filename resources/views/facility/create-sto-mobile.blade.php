 @php
  $isChemCons = in_array($warehouse ?? null, ['Chemical', 'Consumable']);
@endphp

 <div class="relative z-20 -mt-20 overflow-hidden w-full bg-white p-6 space-y-4 rounded-t-md sm:rounded-2xl shadow-lg">
    <!-- MOBILE VERSION (REPLACE SELECT WITH HEADING) -->
    <div class="relative pb-4">
      <span class="text-lg font-semibold text-gray-900">🏷️ Electronic Stock Opname</span>
      <span class="text-xs text-gray-500 mt-1">(e-STO)</span>
    </div>
    <!-- MOBILE MODE (CARD STYLE) -->
  <div class="col-span-2 space-y-4" id="mobile-article-list">
  
   @php $defaultMobileRows = in_array($warehouse ?? null, ['Chemical', 'Consumable']) ? 1 : 8; @endphp
@for ($i = 0; $i < $defaultMobileRows; $i++)
    <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-lg border border-gray-200 overflow-hidden sto-row" 
     data-row="{{ $i }}" data-is-ref="0" data-is-manual="1">

      <!-- HEADER CARD -->
      <div class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-800 text-white px-4 py-2">
        <span class="header-label text-sm font-semibold">❖ Item {{ $i + 1 }}</span>
      </div>

      <div class="p-4">

        <!-- PART NAME -->
        <label class="text-xs font-semibold text-gray-600 mb-1">Nama Part</label>
        <select class="part-select w-full mt-1"
                name="articles[{{ $i }}][article_id]"
                data-row="{{ $i }}">
          <option value="">-- pilih part --</option>
          @foreach ($articles as $a)
            <option value="{{ $a->id }}"
                    data-code="{{ $a->article_code }}"
                    data-uom="{{ $a->unit }}"
                    data-min-package="{{ $a->min_package }}">
              {{ $a->description }}
            </option>
          @endforeach
        </select>
  <input type="hidden"
        name="articles[{{ $i }}][other_name]"
        class="other-name-input">
        <!-- PART CODE -->
        <label class="text-xs font-semibold text-gray-600 mt-3 mb-1 block">Kode Part</label>
        <input type="text"
              name="articles[{{ $i }}][article_code]"
              class="article-code w-full border rounded px-2 py-1 bg-gray-100"
              readonly>

       <!-- QTY block (hidden saat mode area) -->
        <div class="mobile-qty-block">
          <div class="grid grid-cols-2 gap-3 mt-3">
            <div>
              <label class="text-xs font-semibold text-gray-600 mb-1 block">Qty</label>
              <input type="number"
                    min="0"
                    name="articles[{{ $i }}][qty]"
                    class="qty-input w-full border rounded px-2 py-1">
            </div>
            <div>
              <label class="text-xs font-semibold text-gray-600 mb-1 block">Packing</label>
              <input type="number"
                    min="0"
                    name="articles[{{ $i }}][min_package]"
                    class="w-full border rounded px-2 py-1 bg-gray-100">
            </div>
          </div>
        </div>
        <!-- ADDRESS block (muncul saat mode area) -->
        <div class="mobile-addr-block mt-3" style="display:none;">
          <label class="text-xs font-semibold text-gray-600 mb-1 block">Address</label>
          <div class="mobile-addr-label w-full border rounded px-2 py-1 bg-blue-50 text-blue-700 text-sm font-semibold">—</div>
        </div>

        <!-- LOCATION -->

{{-- SESUDAH --}}
          <div>
            <label class="text-xs font-semibold text-gray-600 mb-1 block">UOM</label>
            <input type="text"
                  name="articles[{{ $i }}][uom]"
                  class="part-uom w-full border rounded px-2 py-1 bg-gray-100"
                  readonly>
          </div>

          @if(($warehouse ?? null) === 'Chemical')
          <div class="mt-3">
            <label class="text-xs font-semibold text-gray-600 mb-1 block">Kondisi</label>
            <select name="articles[{{ $i }}][kondisi]"
              class="kondisi-select w-full border rounded px-2 py-1 text-sm">
              <option value="">—</option>
              <option value="Utuh">Utuh</option>
              <option value="Tidak Utuh">Tidak Utuh</option>
            </select>
          </div>
          @endif

        <!-- LOCATION -->
        <label class="text-xs font-semibold text-gray-600 mt-3 mb-1 block">Location</label>
        <input type="text"
              name="articles[{{ $i }}][location]"
              value="{{ $warehouse }}"
              readonly
              class="location-input w-full bg-gray-100 border rounded px-2 py-1">

      </div>

    </div>
    @endfor

  </div>

  @if($isChemCons)
  <div class="flex gap-2 mt-2 mb-4">
    <button type="button" id="btnAddRowMobile"
      class="flex items-center gap-1 px-3 py-2 text-sm font-semibold text-blue-600
             bg-blue-50 border border-blue-200 rounded-lg">
      ＋ Add Row
    </button>
    <button type="button" id="btnClearRefMobile"
      class="flex items-center gap-1 px-3 py-2 text-sm font-semibold text-gray-600
             bg-gray-100 border border-gray-200 rounded-lg"
      style="display:none;">
      ✕ Reset ke Default
    </button>
  </div>
  @endif
  <!-- MOBILE VIEW -->
  <div class=" mb-4">
    <h2 class="text-lg font-semibold text-gray-700 tracking-wide drop-shadow">
      Catatan
    </h2>

    <textarea
      id="note_mobile"
      name="note"
      rows="3"
      class="w-full mt-3 p-3 rounded-lg bg-gray/10 text-gray-700 placeholder-gray/50 border border-gray/20 backdrop-blur focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
      placeholder="Tambahkan catatan di sini..."
    ></textarea>
  </div>

  <!-- MOBILE BUTTONS -->
  <div class="w-full space-y-3">

    <button type="submit" id="btnSaveMobile"
            class="w-full bg-green-600 text-white py-3 rounded-lg text-lg font-semibold shadow-md">
      Save
    </button>

    <a href="{{ url()->previous() }}"
      class="w-full block text-center bg-gray-200 text-gray-700 py-3 rounded-lg text-lg font-semibold shadow-md">
      Back
    </a>

  </div>

  </div>