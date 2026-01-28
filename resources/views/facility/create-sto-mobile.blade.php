 <div class="relative z-20 -mt-20 overflow-hidden w-full bg-white p-6 space-y-4 rounded-t-md sm:rounded-2xl shadow-lg">
    <!-- MOBILE VERSION (REPLACE SELECT WITH HEADING) -->
    <div class="relative pb-4">
      <span class="text-lg font-semibold text-gray-900">🏷️ Electronic Stock Opname</span>
      <span class="text-xs text-gray-500 mt-1">(e-STO)</span>
    </div>
    <!-- MOBILE MODE (CARD STYLE) -->
  <div class="col-span-2 space-y-4">
  
    @for ($i = 0; $i < 7; $i++)
    <div class="bg-white/80 backdrop-blur-md rounded-xl shadow-lg border border-gray-200 overflow-hidden sto-row" data-row="{{ $i }}">

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
                    data-uom="{{ $a->unit }}">
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

        <!-- QTY + UOM in one row -->
        <div class="grid grid-cols-2 gap-3 mt-3">
          <div>
            <label class="text-xs font-semibold text-gray-600 mb-1 block">Qty</label>
            <input type="number"
                  min="0"
                  name="articles[{{ $i }}][qty]"
                  class="w-full border rounded px-2 py-1">
          </div>

          <div>
            <label class="text-xs font-semibold text-gray-600 mb-1 block">UOM</label>
            <input type="text"
                  name="articles[{{ $i }}][uom]"
                  class="part-uom w-full border rounded px-2 py-1 bg-gray-100"
                  readonly>
          </div>
        </div>

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