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

<div class="w-full bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4">Create New Article</h2>

    <form action="{{ route('inventory.article.store') }}" method="POST">
        @csrf

        <div id="supplier-container">
            <!-- Group Pertama -->
            <div class="supplier-group border rounded-lg p-4 mb-4 bg-gray-50 relative">
                <div id="supplierWrapper" class="mb-4 flex justify-between items-center">
                    <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier <small class="text-red-500">*</small></label>
                    <button type="button" class="remove-supplier text-red-600 hover:text-red-800 text-sm hidden"><i data-feather="x-circle"></i></button>
                </div>
                <select id="supplier" name="articles[0][supplier]" class="supplier-input w-1/2 px-3 py-1 border border-gray-300 rounded-md shadow-sm focus:border border-blue-500" required>
                <option value="">-- Choose Supplier --</option>
                </select>
                <div class="item-container grid grid-cols-1 gap-4 mt-4">
                    <!-- Item pertama -->
                   <div class="grid grid-cols-10 gap-1 item-group items-start">
        <div id="articleTypeWrapper"><select id="article_type" class="article_type text-xs w-full px-3 py-1 border rounded-md"><option value="">-- Choose Type --</option>
                </select></div>
        <div class="col-span-3"><input type="text" class="description  w-full px-3 py-1 border rounded-md" placeholder="Description" required></div>
        <div><input type="text" class="color w-full px-3 py-1 border rounded-md" placeholder="Color"></div>
        <div><input type="text" class="model w-full px-3 py-1 border rounded-md" placeholder="Model"></div>
        <div><input type="text" class="unit w-full px-3 py-1 border rounded-md" placeholder="Smallest Unit" required></div>
        <div><input type="text" class="unit w-full px-3 py-1 border rounded-md" placeholder="Safety Stock"></div>
        <div><input type="text" class="unit w-full px-3 py-1 border rounded-md" placeholder="Min Package"></div>
        <div class="flex items-center pt-1">
        <button type="button" class="remove-item text-red-600 hover:text-red-800 text-sm px-2 py-1 rounded hidden">✕</button>
    </div>
                </div>
                </div>
                <div class="mt-3">
                    <button type="button" class="add-item bg-blue-500 text-white text-sm px-3 py-1 rounded shadow hover:bg-blue-600">+ Add Item</button>
                </div>
            </div>
        </div>
        <div class="mt-4 mb-4 justify-start">
            <button type="button" id="add-supplier" class="bg-indigo-500 text-white text-sm px-4 py-2 rounded shadow hover:bg-indigo-600">+ Add Supplier/Customer</button>
        </div>
        <hr>
        <div class="justify-end gap-2 mt-6">
             <a href="{{ route('inventory.article.index') }}" class="bg-white text-black hover:bg-gray-700 hover:text-white border border-gray-500 px-4 py-2 rounded-lg shadow">Back</a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">Save</button>
        </div>
        </div>
    </form>
    </div>


{{-- Templates --}}
<template id="supplier-template">
    <div class="supplier-group border rounded-lg p-4 mb-4 bg-gray-50 relative">
         <div id="supplierWrapper" class="mb-4 flex justify-between items-center">
                    <label for="supplier" class="block text-sm font-medium text-gray-700">Supplier <small class="text-red-500">*</small></label>
                    <button type="button" class="remove-supplier text-red-600 hover:text-red-800 text-sm hidden"><i data-feather="x-circle"></i></button>
                </div>
                <select id="supplier" name="articles[0][supplier]" class="supplier-input w-1/2 px-3 py-1 border border-gray-300 rounded-md shadow-sm focus:border border-blue-500" required>
                <option value="">-- Pilih Supplier --</option>
                </select>
        <div class="item-container grid grid-cols-1 gap-4 mt-4">
            <!-- diisi item -->
        </div>

        <div class="mt-3">
            <button type="button" class="add-item bg-blue-500 text-white text-sm px-3 py-1 rounded shadow hover:bg-blue-600">+ Add Item</button>
        </div>
    </div>
</template>

<template id="item-template">
     <div class="grid grid-cols-10 gap-1 item-group items-start">
        <div id="articleTypeWrapper"><select id="article_type" class="article_type text-xs w-full px-3 py-1 border rounded-md" title="Article Type" required><option value="">-- Choose Type --</option>
                </select></div>
        <div class="col-span-3"><input type="text" class="description  w-full px-3 py-1 border rounded-md" placeholder="Description"  title="Description" required></div>
        <div><input type="text" class="color w-full px-3 py-1 border rounded-md" placeholder="Color"></div>
        <div><input type="text" class="model w-full px-3 py-1 border rounded-md" placeholder="Model"></div>
        <div><input type="text" class="unit w-full px-3 py-1 border rounded-md" placeholder="Smallest Unit" required></div>
        <div><input type="text" class="unit w-full px-3 py-1 border rounded-md" placeholder="Safety Stock"></div>
        <div><input type="text" class="unit w-full px-3 py-1 border rounded-md" placeholder="Min Package"></div>
        <div class="flex items-center pt-1">
        <button type="button" class="remove-item text-red-600 hover:text-red-800 text-sm px-2 py-1 rounded">✕</button>
    </div>
                </div>
</template>

<style>
    input::placeholder {
  font-size: 10px;
  color: #9ca3af; /* Tailwind gray-400 */
}

</style>
{{-- Scripts --}}
<script>
    function updateNameAttributes() {
        document.querySelectorAll('.supplier-group').forEach((group, i) => {
            const supplierInput = group.querySelector('.supplier-input');
            supplierInput.name = `articles[${i}][supplier]`;

            const itemGroups = group.querySelectorAll('.item-group');
            itemGroups.forEach((item, j) => {
                item.querySelector('.article_type').name = `articles[${i}][items][${j}][article_type]`;
                item.querySelector('.description').name = `articles[${i}][items][${j}][description]`;
                item.querySelector('.color').name = `articles[${i}][items][${j}][color]`;
                item.querySelector('.model').name = `articles[${i}][items][${j}][model]`;
                item.querySelector('.unit').name = `articles[${i}][items][${j}][unit]`;
            });

            // Toggle visibility of remove
            const removeSupplierBtn = group.querySelector('.remove-supplier');
            removeSupplierBtn.classList.toggle('hidden', document.querySelectorAll('.supplier-group').length === 1);
        });
    }

   document.getElementById('add-supplier').addEventListener('click', function () {
    const supplierTemplate = document.getElementById('supplier-template');
    const supplierClone = supplierTemplate.content.cloneNode(true);

    const itemTemplate = document.getElementById('item-template');
    const firstItem = itemTemplate.content.cloneNode(true);
    supplierClone.querySelector('.item-container').appendChild(firstItem);

    const newSupplier = supplierClone.querySelector('.supplier-input');
    const newArticleType = supplierClone.querySelector('.article_type');

    loadSuppliers(newSupplier);
    loadArticleTypes(newArticleType);

    setupArticleTypeListener(newArticleType, supplierClone);

    document.getElementById('supplier-container').appendChild(supplierClone);
    updateNameAttributes();
    feather.replace();
});

function setupArticleTypeListener(articleTypeSelect, supplierGroup) {
    articleTypeSelect.addEventListener('change', function () {
        const label = supplierGroup.querySelector('label[for="supplier"], .supplier-label');
        if (this.options[this.selectedIndex].text.toLowerCase().includes('finish good')) {
            label.textContent = 'Customer';
        } else {
            label.textContent = 'Supplier';
        }
    });
}


   document.addEventListener('click', function (e) {
    // Tambah item
    if (e.target.closest('.add-item')) {
    const supplierGroup = e.target.closest('.supplier-group');
    const itemTemplate = document.getElementById('item-template');
    const itemClone = itemTemplate.content.cloneNode(true);

    const articleTypeSelect = itemClone.querySelector('.article_type');
    loadArticleTypes(articleTypeSelect);

    // Tambahkan listener ubah label jika perlu
    setupArticleTypeListener(articleTypeSelect, supplierGroup);

    supplierGroup.querySelector('.item-container').appendChild(itemClone);
    updateNameAttributes();
}


    // Hapus
    if (e.target.closest('.remove-supplier')) {
        const allSuppliers = document.querySelectorAll('.supplier-group');
        if (allSuppliers.length > 1) {
            e.target.closest('.supplier-group').remove();
            updateNameAttributes();
        }
    }

    // Hapus item
    if (e.target.closest('.remove-item')) {
        const itemContainer = e.target.closest('.item-container');
        const itemGroups = itemContainer.querySelectorAll('.item-group');
        if (itemGroups.length > 1) {
            e.target.closest('.item-group').remove();
            updateNameAttributes();
        }
    }
});

function loadSuppliers(selectElement) {
    fetch('')
        .then(res => res.json())
        .then(data => {
            data.forEach(sp => {
                const option = document.createElement('option');
                option.value = sp.id;
                option.textContent = sp.name;
                selectElement.appendChild(option);
            });
        })
        .catch(err => console.error('Gagal memuat supplier:', err));
}

function loadArticleTypes(selectElement) {
    fetch('')
        .then(res => res.json())
        .then(data => {
            data.forEach(at => {
                const option = document.createElement('option');
                option.value = at.id;
                option.textContent = `${at.code} - ${at.name}`;
                selectElement.appendChild(option);
            });
        })
        .catch(err => console.error('Gagal memuat article type:', err));
}

document.addEventListener('DOMContentLoaded', function () {

    const supplierSelect = document.getElementById('supplier');
    const articleTypeSelect = document.getElementById('article_type');

    loadSuppliers(supplierSelect);
    loadArticleTypes(articleTypeSelect);
    setupArticleTypeListener(articleTypeSelect, document.querySelector('.supplier-group'));

    updateNameAttributes();
    // Inisialisasi awal
    updateNameAttributes();

    });

    
</script>
@endsection

