@extends('layouts.app')

@section('title', 'Create Article')
@section('page-title', 'Create Article')
@section('breadcrumb-item', 'Inventory')
@section('breadcrumb-active', 'Create Article')

@section('content')
<div class="w-full bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Upload Multiple Article</h2>
    @if(session('success'))
    <div class="text-green-600 mt-2">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="text-red-600 mt-2">{{ session('error') }}</div>
@endif

    <div class="bg-gray-50 border border-dashed border-gray-300 p-4 rounded mb-6">
    <h3 class="text-lg font-medium mb-2">Upload via Excel (.xlsx)</h3>
    <form action="{{ route('inventory.article.import') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4">
         @csrf
      <input type="file" name="csv_file" accept=".xlsx" class="flex-1 border border-gray-300 rounded p-2">
      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Upload</button>
    </form>
    <p class="text-sm text-gray-500 mt-2">
      Download template Excel: <a href="{{ route('inventory.article.template') }}" class="text-blue-600 underline" download>Download Template</a>
    </p>
  </div>
</div>

<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-8">
    <h2 class="text-lg font-semibold text-gray-700 flex items-center gap-2">
  <i data-feather="box" class="w-5 h-5 relative top-[1px]"></i>
  Create New Article
</h2>


    <form id="supplier-form" action="" method="POST">
    @csrf
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
       <div>
  <label for="article_type" class="block text-sm font-medium text-gray-700 mb-1">
    Article Type <small class="text-red-600">*</small>
  </label>
  <select name="article_type" id="article_type" class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm">
    <option value="">-- Choose Article Type --</option>
  </select>
</div>
        <div>
        <label for="group_material" class="block text-sm font-medium text-gray-700 mb-1">Group of Material <small class="text-red-600"> *</small></label>
       <select name="group_material" id="group_material"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Group --</option>
          </select>
        </div>
         <div class="flex items-center h-full pt-6">
        <input type="checkbox" name="as_customer" value="1" checked class="form-checkbox text-indigo-600 mr-2">
        <label for="as_customer" class="text-sm text-gray-700">Orderable</label>
        </div>
      </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div id="supplier-field" class="col-span-2">
        <label for="coa_hutang" class="block text-sm font-medium text-gray-700 mb-1">Supplier <small class="text-red-600">*</small></label>
       <select name="supplier_id" id="supplier_id"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Supplier --</option>
          </select>
        </div>
         <div id="customer-field" class="col-span-2 hidden">
        <label for="coa_hutang" class="block text-sm font-medium text-gray-700 mb-1">Customer <small class="text-red-600">*</small></label>
       <select name="coa_hutang" id="coa_hutang"
                  class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">-- Choose Customer --</option>
          </select>
        </div>
        <div class="col-span-2">
        <label for="initial" class="block text-sm font-medium text-gray-700 mb-1">Description<small class="text-red-600">*</small></label>
        <input type="text" name="initial" id="initial"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
          <div>
        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
        <input type="text" name="join_date" id="join_date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
         <div>
        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
        <input type="text" name="join_date" id="join_date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        </div>
         <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Smallest Unit <small class="text-red-600">*</small></label>
        <input type="text" name="join_date" id="join_date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
         <div>
        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Minimum Package</label>
        <input type="text" name="join_date" id="join_date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        </div>
         <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div>
        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Safety Stock <small class="text-red-600">*</small></label>
        <input type="text" name="join_date" id="join_date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
         <div>
        <label for="join_date" class="block text-sm font-medium text-gray-700 mb-1">Maximum Stock <small class="text-red-600">*</small></label>
        <input type="text" name="join_date" id="join_date"
               class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
        </div>
        </div>

      <!-- 🎯 Tombol Submit -->
      <div class="flex justify-start items-center border-t pt-5 gap-2 mt-6">
        <button id="resetBtn" class="w-24 bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600"> ← Back</button>
        <button id="submitBtn" class="w-24 flex items-center justify-center gap-2 bg-green-600 text-white px-3 py-2 rounded shadow hover:bg-green-700"> <i data-feather="save" class="h-4 w-4"></i>
   Save</button>
      </div>
    </form>
  </div>
</div>


<style>
    input::placeholder {
  font-size: 10px;
  color: #9ca3af; /* Tailwind gray-400 */
}

</style>
{{-- Scripts --}}
<<script>
$(document).ready(function() {
    $('#article_type').select2({
        placeholder: "-- Choose Article Type --",
        allowClear: true,
        ajax: {
            url: "{{ route('inventory.article-type.select') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: `${item.code} - ${item.name}`,
                            code: item.code
                        }
                    })
                };
            },
            cache: true
        }
    });

     $('#group_material').select2({
        placeholder: "-- Choose Group of Material --",
        allowClear: true,
        ajax: {
            url: "{{ route('inventory.gom.select') }}",
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: `${item.code}`,
                        }
                    })
                };
            },
            cache: true
        }
    });

 $('#article_type').on('change', function () {
    var data = $('#article_type').select2('data')[0]; // ambil object terpilih
    var selectedCode = data ? data.code : '';

    if (selectedCode === 'FG') {   // cek berdasarkan kode
        $('#supplier-field').addClass('hidden');
        $('#customer-field').removeClass('hidden');
    } else {
        $('#supplier-field').removeClass('hidden');
        $('#customer-field').addClass('hidden');
    }
});

// === Supplier Select ===
    $('#supplier_id').select2({
        placeholder: "-- Choose Supplier --",
        allowClear: true,
        ajax: {
            url: "{{ route('purchasing.supplier.select') }}", // route untuk ambil data supplier
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id,
                            text: `${item.code} - ${item.name}`
                        }
                    })
                };
            },
            cache: true
        }
    });

});
</script>

@endsection

