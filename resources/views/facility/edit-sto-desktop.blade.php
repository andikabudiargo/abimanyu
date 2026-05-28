{{-- ============================================================
     edit-sto-desktop.blade.php
     Tampilan Edit STO — desain sama dengan create-sto-desktop-2
     ============================================================ --}}

@push('styles')
<style>
:root {
  --sto-blue:        #1565C0;
  --sto-blue-mid:    #1976D2;
  --sto-blue-light:  #E3F0FC;
  --sto-blue-border: #BBDEFB;
  --sto-surface:     #F8FAFD;
  --sto-white:       #FFFFFF;
  --sto-border:      #DDE3EC;
  --sto-text:        #1A2332;
  --sto-text-muted:  #64748B;
  --sto-text-label:  #475569;
  --sto-accent:      #0D47A1;
  --sto-green:       #1B5E20;
  --sto-green-mid:   #2E7D32;
  --sto-green-light: #E8F5E9;
  --sto-amber:       #F59E0B;
  --sto-amber-light: #FFFBEB;
  --sto-amber-border:#FDE68A;
  --sto-row-hover:   #F0F6FF;
  --sto-radius-sm:   4px;
  --sto-radius-md:   6px;
  --sto-radius-lg:   8px;
  --sto-shadow-card: 0 1px 4px rgba(21,101,192,.08), 0 0 0 1px rgba(21,101,192,.07);
  --sto-shadow-sm:   0 1px 2px rgba(0,0,0,.06);
  --font-ui:         'DM Sans', 'Segoe UI', system-ui, sans-serif;
}

.sto-wrap { font-family: var(--font-ui); background: var(--sto-surface); min-height: 100vh; }
.sto-card {
  background: var(--sto-white); border: 1px solid var(--sto-border);
  border-radius: var(--sto-radius-lg); box-shadow: var(--sto-shadow-card); overflow: hidden;
}
.sto-header {
  background: var(--sto-blue); padding: 20px 32px;
  display: flex; align-items: center; justify-content: space-between;
  border-bottom: 3px solid var(--sto-accent);
}
.sto-header-left h1 { font-size: 17px; font-weight: 600; color: #fff; letter-spacing: .4px; margin: 0; }
.sto-header-left p  { font-size: 12px; color: rgba(255,255,255,.72); margin: 3px 0 0; }
.sto-logo-wrap img  { height: 48px; width: auto; opacity: .92; }
.sto-mode-badge {
  display: inline-flex; align-items: center; gap: 6px;
  border-radius: 20px; padding: 5px 14px; font-size: 12px; font-weight: 600;
}
.sto-mode-badge.mode-edit    { background: rgba(245,158,11,.2); border: 1px solid rgba(245,158,11,.4); color: #FDE68A; }
.sto-mode-badge.mode-super   { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3); color: #fff; }
.sto-mode-badge .dot { width: 7px; height: 7px; border-radius: 50%; }
.sto-mode-badge.mode-edit .dot  { background: #FBBF24; }
.sto-mode-badge.mode-super .dot { background: #69F0AE; }

.sto-body { padding: 28px 32px; }
.sto-section-title {
  font-size: 11px; font-weight: 700; color: var(--sto-text-muted);
  text-transform: uppercase; letter-spacing: .8px;
  margin: 0 0 14px; padding-bottom: 8px;
  border-bottom: 1px solid var(--sto-border);
  display: flex; align-items: center; gap: 6px;
}
.sto-field { display: flex; flex-direction: column; gap: 5px; }
.sto-field label {
  font-size: 11px; font-weight: 700; color: var(--sto-text-label);
  letter-spacing: .3px; text-transform: uppercase;
}
.sto-input, .sto-select {
  width: 100%; height: 36px; padding: 0 10px;
  font-size: 13px; font-family: var(--font-ui);
  color: var(--sto-text); background: var(--sto-white);
  border: 1px solid var(--sto-border); border-radius: var(--sto-radius-md);
  outline: none; transition: border-color .15s, box-shadow .15s;
  box-shadow: var(--sto-shadow-sm); appearance: none; -webkit-appearance: none;
}
.sto-select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center; padding-right: 30px;
}
.sto-input:focus, .sto-select:focus {
  border-color: var(--sto-blue-mid); box-shadow: 0 0 0 3px rgba(25,118,210,.12);
}
.sto-input[readonly], .sto-input.readonly {
  background: var(--sto-surface); color: var(--sto-text-muted); cursor: default;
}
.sto-alert-banner {
  display: flex; align-items: center; gap: 10px;
  border-radius: var(--sto-radius-md); padding: 10px 14px;
  font-size: 12px; margin-bottom: 20px;
}
.sto-alert-banner.amber { background: var(--sto-amber-light); border: 1px solid var(--sto-amber-border); color: #92400E; }
.sto-alert-banner.blue  { background: var(--sto-blue-light);  border: 1px solid var(--sto-blue-border);  color: var(--sto-blue); }
.sto-alert-banner strong { font-weight: 700; }

.sto-table-wrap {
  border: 1px solid var(--sto-border); border-radius: var(--sto-radius-lg);
  overflow: hidden; margin-bottom: 20px;
}
.sto-table { width: 100%; border-collapse: collapse; font-size: 13px; table-layout: fixed; }
.sto-table thead tr { background: var(--sto-blue); }
.sto-table thead th {
  padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 700;
  color: rgba(255,255,255,.9); letter-spacing: .5px; text-transform: uppercase; white-space: nowrap;
}
.sto-table thead th.center { text-align: center; }
.sto-table thead th.th-qty2 { background: rgba(0,0,0,.15); }
.sto-table tbody tr { border-bottom: 1px solid var(--sto-border); transition: background .12s; }
.sto-table tbody tr:last-child { border-bottom: none; }
.sto-table tbody tr:hover { background: var(--sto-row-hover); }
.sto-table tbody tr:nth-child(even) { background: #FAFCFF; }
.sto-table tbody tr:nth-child(even):hover { background: var(--sto-row-hover); }
.sto-table th, .sto-table td { padding: 6px 8px; vertical-align: middle; overflow: hidden; white-space: nowrap; }
.sto-table td.center { text-align: center; }
.sto-table input, .sto-table select { width: 100%; box-sizing: border-box; min-width: 0; }
.select2-container { width: 100% !important; min-width: 0 !important; }
.td-qty2 { background: rgba(232,245,233,.5); }
.sto-table .qty2-input {
  background: var(--sto-green-light) !important; border-color: #A5D6A7 !important;
}
.sto-table .qty2-input:focus {
  border-color: var(--sto-green-mid) !important; box-shadow: 0 0 0 3px rgba(46,125,50,.12) !important;
}
.sto-row-num {
  display: inline-flex; align-items: center; justify-content: center;
  width: 22px; height: 22px; border-radius: 50%;
  background: var(--sto-blue-light); color: var(--sto-blue); font-size: 11px; font-weight: 700;
}
.sto-location-tag {
  display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 20px;
  background: var(--sto-blue-light); border: 1px solid var(--sto-blue-border);
  color: var(--sto-blue); font-size: 11px; font-weight: 700;
}
.sto-table .sto-input, .sto-table .sto-select {
  height: 32px; font-size: 12px; padding: 0 8px; box-shadow: none;
}
.sto-table .sto-select { padding-right: 26px; background-position: right 6px center; }
.sto-footer-row {
  display: grid; grid-template-columns: 1fr auto; gap: 24px; align-items: end;
  border-top: 1px solid var(--sto-border); padding-top: 24px;
}
.sto-textarea {
  width: 100%; padding: 10px 12px; font-size: 13px; font-family: var(--font-ui);
  color: var(--sto-text); background: var(--sto-white); border: 1px solid var(--sto-border);
  border-radius: var(--sto-radius-md); resize: vertical; outline: none;
  transition: border-color .15s, box-shadow .15s; box-shadow: var(--sto-shadow-sm);
}
.sto-textarea:focus { border-color: var(--sto-blue-mid); box-shadow: 0 0 0 3px rgba(25,118,210,.12); }
.sto-btn-group { display: flex; gap: 8px; align-items: center; }
.sto-btn {
  display: inline-flex; align-items: center; gap: 6px; padding: 0 18px; height: 36px;
  border-radius: var(--sto-radius-md); font-size: 13px; font-weight: 600; font-family: var(--font-ui);
  cursor: pointer; border: 1px solid transparent;
  transition: background .15s, border-color .15s, box-shadow .15s, transform .1s;
  text-decoration: none; white-space: nowrap;
}
.sto-btn:active { transform: scale(.98); }
.sto-btn-ghost { background: var(--sto-white); border-color: var(--sto-border); color: var(--sto-text-muted); }
.sto-btn-ghost:hover { background: var(--sto-surface); border-color: #B0BEC5; color: var(--sto-text); }
.sto-btn-save {
  background: var(--sto-green-mid); border-color: var(--sto-green); color: #fff;
  box-shadow: 0 1px 3px rgba(46,125,50,.3);
}
.sto-btn-save:hover { background: var(--sto-green); }
.sto-btn-add {
  background: var(--sto-white); border-color: var(--sto-border);
  color: var(--sto-blue); font-size: 12px; height: 30px; padding: 0 12px;
}
.sto-btn-add:hover { background: var(--sto-blue-light); border-color: var(--sto-blue-border); }
.sto-hint { font-size: 11px; color: var(--sto-text-muted); margin-top: 2px; }
@media (max-width: 639px) {
  .sto-footer-row { grid-template-columns: 1fr; }
  .sto-btn-group  { justify-content: flex-end; }
  .sto-body   { padding: 20px 16px; }
  .sto-header { padding: 16px 20px; }
}
</style>
@endpush

<div class="pc-container ml-[264px] p-6 min-h-screen sto-wrap">

  {{-- Page Header --}}
  <div class="page-header">
    <div class="page-block flex items-center justify-start lg:justify-between gap-4">
      <div class="page-header-title">
        <h5 class="mb-0 font-medium">@yield('page-title', 'Dashboard')</h5>
      </div>
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

  <div class="sto-card">

    {{-- Header Bar --}}
    <div class="sto-header">
      <div class="sto-header-left">
        <h1>Electronic Stock Opname (e-STO)</h1>
        <p>Edit — {{ $sto->sto_number }}</p>
      </div>
      <div style="display:flex; align-items:center; gap:12px;">
        <div class="sto-logo-wrap">
          <img src="{{ asset('img/logo.png') }}" alt="Company Logo">
        </div>
      </div>
    </div>

    <div class="sto-body">

      {{-- Document Reference --}}
      <p class="sto-section-title">
        <i data-feather="bookmark" class="w-3.5 h-3.5"></i>
        Document Reference
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="sto-field">
          <label>STO Number</label>
          <div style="display:flex;align-items:center;height:36px;">
            <span class="sto-location-tag" style="font-size:13px;padding:4px 14px;">
              <i data-feather="hash" class="w-3 h-3"></i>
              {{ $sto->sto_number }}
            </span>
          </div>
        </div>
        <div class="sto-field">
          <label>Warehouse</label>
          <div style="display:flex;align-items:center;height:36px;">
            <span class="sto-location-tag">
              <i data-feather="map-pin" class="w-3 h-3"></i>
              {{ $warehouse ?? 'All Warehouses' }}
            </span>
          </div>
        </div>
        @if($sto->area)
        <div class="sto-field">
          <label>Rack</label>
          <div style="display:flex;align-items:center;height:36px;">
            <span class="sto-location-tag">
              <i data-feather="layers" class="w-3 h-3"></i>
              {{ $sto->area }}
            </span>
          </div>
        </div>
        @endif
        @if($sto->shelves)
        <div class="sto-field">
          <label>Address</label>
          <div style="display:flex;align-items:center;height:36px;">
            <span class="sto-location-tag">
              <i data-feather="grid" class="w-3 h-3"></i>
              {{ $sto->shelves }}
            </span>
          </div>
        </div>
        @endif
        <div class="sto-field">
          <label>Verificator</label>
          <div style="display:flex;align-items:center;height:36px;">
            <span class="sto-location-tag">
              <i data-feather="user" class="w-3 h-3"></i>
              {{ auth()->user()->name }}
            </span>
          </div>
        </div>
      </div>

      {{-- Info Banner --}}
      @if($isSuperUser)
        <div class="sto-alert-banner blue mb-6" style="background:rgba(13,71,161,.06);border-color:var(--sto-blue-border);">
          <i data-feather="shield" class="w-4 h-4" style="flex-shrink:0;"></i>
          <span>Mode <strong>Superuser</strong> — Qty → <strong>Verificator 1</strong> &nbsp;|&nbsp; Qty 2 → <strong>Verificator 2</strong>.</span>
        </div>
      @endif

      {{-- Stock Items Table --}}
      <p class="sto-section-title">
        <i data-feather="list" class="w-3.5 h-3.5"></i>
        Stock Articles
      </p>

      <div class="sto-table-wrap">
        <table class="sto-table">
          <thead>
            <tr>
              <th style="width:36px;text-align:center;">#</th>
              <th style="width:110px;">Part Code</th>
              <th>Part Name</th>
              @if($isSuperUser)
                <th class="center" style="width:90px;">Qty 1</th>
                <th class="center th-qty2" style="width:90px;">Qty 2</th>
              @else
                <th class="center" style="width:90px;">Qty</th>
              @endif
              <th class="center" style="width:80px;">Packing</th>
              <th class="center" style="width:80px;">UOM</th>
              @if(($warehouse ?? null) === 'Chemical')
                <th class="center" style="width:110px;">Kondisi</th>
              @endif
              <th class="center" style="width:160px;">Location</th>
            </tr>
          </thead>
          <tbody id="article-table">

          @php
            $maxRow   = 7;
            $rowCount = $items->count();
            $total    = max($maxRow, $rowCount);
          @endphp

          {{-- ── EXISTING ITEMS (baris dengan data) ── --}}
          @foreach ($items as $i => $item)
          <tr class="sto-row" data-row="{{ $i }}">

            {{-- 🔑 KUNCI FIX: kirim item ID agar backend tahu update row mana --}}
            <input type="hidden" name="articles[{{ $i }}][item_id]"  value="{{ $item->id }}">
            <input type="hidden" name="articles[{{ $i }}][other_name]" class="other-name-input"
              value="{{ $item->article_code === 'OTHER' ? $item->other_name : '' }}">

            <td class="center"><span class="sto-row-num">{{ $i + 1 }}</span></td>

            <td>
              <input type="text" name="articles[{{ $i }}][article_code]"
                value="{{ $item->article_code }}"
                class="article-code sto-input readonly" readonly>
            </td>

            <td>
              <select class="part-select sto-select"
                name="articles[{{ $i }}][article_id]" data-row="{{ $i }}">
                @if($item->article_code === 'OTHER')
                  <option value="OTHER" data-code="OTHER" data-uom="{{ $item->uom }}" selected>
                    {{ $item->other_name }}
                  </option>
                @endif
                @foreach ($articles as $a)
                  <option value="{{ $a->id }}"
                    data-code="{{ $a->article_code }}"
                    data-uom="{{ $a->unit }}"
                    data-min-package="{{ $a->min_package ?? '' }}"
                    @selected($a->article_code === $item->article_code)>
                    {{ $a->article_code }} — {{ $a->description }}
                  </option>
                @endforeach
              </select>
            </td>

            {{-- Qty columns --}}
            @if($isSuperUser)
              <td class="center">
                <input type="number" min="0" name="articles[{{ $i }}][qty]"
                  value="{{ $item->qty ?? '' }}"
                  class="qty-input sto-input" style="text-align:center;">
              </td>
              <td class="center td-qty2">
                <input type="number" min="0" name="articles[{{ $i }}][qty_2]"
                  value="{{ $item->qty_2 ?? '' }}"
                  class="qty2-input sto-input" style="text-align:center;">
              </td>
            @elseif($isSecondUser)
              <td class="center">
                <input type="number" min="0" name="articles[{{ $i }}][qty_2]"
                  value="{{ $item->qty_2 ?? '' }}"
                  class="qty2-input sto-input" style="text-align:center;">
              </td>
            @else
              <td class="center">
                <input type="number" min="0" name="articles[{{ $i }}][qty]"
                  value="{{ $item->qty ?? '' }}"
                  class="qty-input sto-input" style="text-align:center;">
              </td>
            @endif

            <td class="center">
              <input type="text" name="articles[{{ $i }}][min_package]"
                value="{{ $item->article->min_package ?? '' }}"
                class="part-min-package sto-input readonly" readonly style="text-align:center;">
            </td>

            <td class="center">
              <input type="text" name="articles[{{ $i }}][uom]"
                value="{{ $item->article_code === 'OTHER' ? $item->uom : ($item->article->unit ?? '') }}"
                class="part-uom sto-input readonly"
                @if($item->article_code !== 'OTHER') readonly @endif
                style="text-align:center;">
            </td>

            @if(($warehouse ?? null) === 'Chemical')
              <td class="center">
                <select name="articles[{{ $i }}][kondisi]"
                  class="kondisi-select sto-select"
                  style="text-align:center;font-size:11px;height:32px;">
                  <option value="">—</option>
                  <option value="Utuh"      @selected(($item->kondisi ?? '') === 'Utuh')>Utuh</option>
                  <option value="Tidak Utuh" @selected(($item->kondisi ?? '') === 'Tidak Utuh')>Tidak Utuh</option>
                </select>
              </td>
            @endif

            <td class="center">
              @if($isSuperUser)
                <select class="location-input sto-select" name="articles[{{ $i }}][location]"
                  style="text-align:center;font-size:11px;">
                  @foreach($allowedWarehouses as $wh)
                    <option value="{{ $wh }}" @selected($item->location === $wh)>{{ $wh }}</option>
                  @endforeach
                </select>
              @else
                <input type="text" name="articles[{{ $i }}][location]"
                  value="{{ $item->location }}" readonly
                  class="location-input sto-input readonly" style="text-align:center;">
              @endif
            </td>
          </tr>
          @endforeach

          {{-- ── EMPTY ROWS ── --}}
          @for ($i = $rowCount; $i < $total; $i++)
          <tr class="sto-row" data-row="{{ $i }}">
            {{-- item_id kosong = baris baru (INSERT) --}}
            <input type="hidden" name="articles[{{ $i }}][item_id]"  value="">
            <input type="hidden" name="articles[{{ $i }}][other_name]" class="other-name-input" value="">

            <td class="center"><span class="sto-row-num">{{ $i + 1 }}</span></td>

            <td>
              <input type="text" name="articles[{{ $i }}][article_code]"
                value="" class="article-code sto-input readonly" readonly>
            </td>

            <td>
              <select class="part-select sto-select"
                name="articles[{{ $i }}][article_id]" data-row="{{ $i }}">
                <option value="">— Pilih Part —</option>
                @foreach ($articles as $a)
                  <option value="{{ $a->id }}"
                    data-code="{{ $a->article_code }}"
                    data-uom="{{ $a->unit }}"
                    data-min-package="{{ $a->min_package ?? '' }}">
                    {{ $a->article_code }} — {{ $a->description }}
                  </option>
                @endforeach
              </select>
            </td>

            @if($isSuperUser)
              <td class="center">
                <input type="number" min="0" name="articles[{{ $i }}][qty]" value=""
                  class="qty-input sto-input" style="text-align:center;">
              </td>
              <td class="center td-qty2">
                <input type="number" min="0" name="articles[{{ $i }}][qty_2]" value=""
                  class="qty2-input sto-input" style="text-align:center;">
              </td>
            @elseif($isSecondUser)
              <td class="center">
                <input type="number" min="0" name="articles[{{ $i }}][qty_2]" value=""
                  class="qty2-input sto-input" style="text-align:center;">
              </td>
            @else
              <td class="center">
                <input type="number" min="0" name="articles[{{ $i }}][qty]" value=""
                  class="qty-input sto-input" style="text-align:center;">
              </td>
            @endif

            <td class="center">
              <input type="text" name="articles[{{ $i }}][min_package]" value=""
                class="part-min-package sto-input readonly" readonly style="text-align:center;">
            </td>

            <td class="center">
              <input type="text" name="articles[{{ $i }}][uom]" value=""
                class="part-uom sto-input readonly" readonly style="text-align:center;">
            </td>

            @if(($warehouse ?? null) === 'Chemical')
              <td class="center">
                <select name="articles[{{ $i }}][kondisi]"
                  class="kondisi-select sto-select"
                  style="text-align:center;font-size:11px;height:32px;">
                  <option value="">—</option>
                  <option value="Utuh">Utuh</option>
                  <option value="Tidak Utuh">Tidak Utuh</option>
                </select>
              </td>
            @endif

            <td class="center">
              @if($isSuperUser)
                <select class="location-input sto-select" name="articles[{{ $i }}][location]"
                  style="text-align:center;font-size:11px;">
                  @foreach($allowedWarehouses as $wh)
                    <option value="{{ $wh }}">{{ $wh }}</option>
                  @endforeach
                </select>
              @else
                <input type="text" name="articles[{{ $i }}][location]"
                  value="{{ $warehouse ?? '' }}" readonly
                  class="location-input sto-input readonly" style="text-align:center;">
              @endif
            </td>
          </tr>
          @endfor

          </tbody>
        </table>
      </div>

      <div style="margin-bottom:24px;">
        <button type="button" id="btnAddRow" class="sto-btn sto-btn-add">
          <i data-feather="plus" class="w-3.5 h-3.5"></i>
          Add Row
        </button>
      </div>

      <div class="sto-footer-row">
        <div class="sto-field">
          <label>Catatan / Note</label>
          <textarea id="note" name="note" rows="3" class="sto-textarea"
            placeholder="Tambahkan catatan jika diperlukan...">{{ $sto->note }}</textarea>
        </div>
        <div class="sto-btn-group">
          <a href="{{ url()->previous() }}" class="sto-btn sto-btn-ghost">
            <i data-feather="arrow-left" class="w-3.5 h-3.5"></i>
            Back
          </a>
          <button type="submit" id="btnSave" class="sto-btn sto-btn-save">
            <i data-feather="save" class="w-3.5 h-3.5"></i>
            Save
          </button>
        </div>
      </div>

    </div>
  </div>
</div>