@extends('layouts.app-pk')

@section('title', 'Product Knowledge')
@section('page-title', 'PRODUCT KNOWLEDGE')
@section('breadcrumb-item', 'Logistic')
@section('breadcrumb-active', 'product Knowledge')

@section('content')
<!-- PRODUCT KNOWLEDGE HEADER -->
<div class="px-3 py-3 flex items-center justify-between">

    <!-- LEFT : TITLE -->
    <div class="flex flex-col">
        <h1 class="text-xs sm:text-sm md:text-md font-semibold text-gray-800">
            Product Knowledge
        </h1>
    </div>

    <!-- RIGHT : LOGO / ILLUSTRATION -->
    <div class="flex items-center justify-end">
        <img src="{{ asset('img/logo.png') }}"
             class="w-10 sm:w-36 md:w-24
                    animate-float drop-shadow-xl select-none"
             alt="Product Illustration">
    </div>

</div>

<section class="relative
    bg-gradient-to-b
    from-indigo-50 via-slate-50 to-white
    rounded-2xl overflow-hidden
    border border-indigo-100">

    <!-- subtle dot pattern -->
    <div class="absolute inset-0 opacity-[0.035]
        bg-[radial-gradient(circle_at_1px_1px,#3730a3_1px,transparent_0)]
        bg-[size:22px_22px]">
    </div>

    <!-- soft indigo glow -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2
        w-[700px] h-[300px]
        bg-indigo-300/30 blur-3xl rounded-full">
    </div>

    <div class="relative max-w-6xl mx-auto px-4 md:px-6 py-10 md:py-16">

        <!-- HEADER : FLEX BETWEEN -->
        <div class="px-6 py-5 flex items-center justify-between mb-8 md:mb-12">


        <!-- CONTENT ROW -->
        <div class="flex flex-row items-center justify-start gap-6 md:gap-10">

            <!-- STAFF IMAGE -->
            <div class="flex-shrink-0">
                <img src="{{ asset('img/staff.png') }}"
                     class="w-28 sm:w-36 md:w-52
                            animate-float drop-shadow-xl"
                     alt="Staff Illustration">
            </div>

            <!-- TEXT -->
            <div class="max-w-xl text-left">

                <!-- badge -->
                <div class="hidden inline-flex items-center gap-2
                            bg-indigo-100/70 text-indigo-700
                            text-xs font-medium
                            px-3 py-1 rounded-full mb-3
                            backdrop-blur-sm">

                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    Product Knowledge System
                </div>

                <h1 class="text-2xl sm:text-xl md:text-4xl
                           font-semibold text-gray-800
                           leading-tight tracking-tight">

                    Howdy,
                    <span class="text-indigo-600">
                        What Can I Find for You?
                    </span>
                </h1>

                <p class="
                    mt-3 text-[8px] sm:text-sm md:text-base
                    text-gray-600 leading-relaxed">

                    Powerful product discovery designed for operational teams.
                    Instantly find material items with intelligent search
                    and structured details.
                </p>

            </div>

        </div>

    </div>

</section>

<!-- ================= FLOATING SEARCH ================= -->
<div class="relative z-20 -mt-8 md:-mt-10 px-4">

    <!-- glow UNDER card -->
    <div class="absolute inset-0 flex justify-center pointer-events-none">
        <div class="w-[75%] h-24 bg-indigo-400/25 blur-3xl rounded-full"></div>
    </div>

    <div class="relative max-w-3xl mx-auto
                bg-white
                rounded-2xl
                border border-indigo-500
                p-3 md:p-4
                shadow-[0_10px_30px_rgba(0,0,0,0.08)]
                transition-all duration-300
                focus-within:ring-2
                focus-within:ring-indigo-500
                focus-within:shadow-[0_15px_40px_rgba(79,70,229,0.25)]">


        <div class="flex items-center gap-3">

            <!-- icon -->
            <div class="flex items-center justify-center
                        w-10 h-10 rounded-xl
                        bg-indigo-600 text-white
                        shadow-md">

                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </div>

            <!-- input -->
            <input id="searchInput" type="text"
                   placeholder="Search product name or code..."
                   class="w-full outline-none
                          text-sm md:text-base
                          placeholder-gray-400">

        </div>

    </div>

</div>



<!-- ================= RESULT CONTENT ================= -->
<section class="w-full mx-auto px-4 mt-8 pb-16">

    <!-- Section Header -->
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-gray-800 font-semibold text-lg">
            Search Results
        </h2>

        <span id="resultCount" class="text-xs text-gray-400">
            0 products found
        </span>
    </div>


    <div id="resultContainer" class="space-y-4">

        <!-- ================= ITEM ================= -->
        <div class="group bg-white rounded-2xl
                    border border-gray-200/80
                    shadow-sm
                    hover:shadow-lg
                    hover:border-indigo-200
                    transition-all duration-300">

           <div class="accordion-content hidden
            border-t border-gray-100
            px-4 md:px-6 py-5">

    <!-- PHOTO GRID -->
    <div class="grid grid-cols-2 gap-4 md:gap-6">

        <!-- PHOTO 1 -->
        <div class="group relative
                    rounded-xl overflow-hidden
                    bg-slate-50 border border-gray-100
                    aspect-square">

            <img src="{{ asset('img/product1.jpg') }}"
                 class="w-full h-full object-cover
                        transition duration-300
                        group-hover:scale-105"
                 alt="Product Image">

            <!-- subtle overlay -->
            <div class="absolute inset-0
                        bg-gradient-to-t
                        from-black/10 to-transparent
                        opacity-0 group-hover:opacity-100
                        transition">
            </div>

            <div class="absolute top-2 left-2
            text-[10px] px-2 py-1
            bg-white/80 backdrop-blur
            rounded-md text-gray-700">
    Rear View
</div>

        </div>

        <!-- PHOTO 2 -->
        <div class="group relative
                    rounded-xl overflow-hidden
                    bg-slate-50 border border-gray-100
                    aspect-square">

            <img src="{{ asset('img/product2.jpg') }}"
                 class="w-full h-full object-cover
                        transition duration-300
                        group-hover:scale-105"
                 alt="Product Image">

            <div class="absolute inset-0
                        bg-gradient-to-t
                        from-black/10 to-transparent
                        opacity-0 group-hover:opacity-100
                        transition">
            </div>

            <div class="absolute top-2 left-2
            text-[10px] px-2 py-1
            bg-white/80 backdrop-blur
            rounded-md text-gray-700">
    Front View
</div>

        </div>

    </div>

</div>

        </div>
    </div>

</section>


@push('scripts')
<!-- ================= ACCORDION SCRIPT ================= -->
<script>
$(document).on('click', '.accordion-btn', function () {

    const content = $(this).closest('.group')
                           .find('.accordion-content');

    content.slideToggle(200);

    $(this)
        .find('.fa-chevron-down')
        .toggleClass('rotate-180');
});

const noDataHTML = `
    <div class="group bg-white rounded-2xl
                border border-gray-200/80
                shadow-sm">

        <div class="text-center py-10 text-gray-400 text-sm">
            <i class="fa-solid fa-magnifying-glass mb-2 text-lg"></i>
            <p>No Data Available</p>
        </div>
    </div>
`;

$('#resultContainer').html(noDataHTML);

$(function () {

    
let debounceTimer = null;

$('#searchInput').on('keyup', function () {

    const keyword = $(this).val().trim();

    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {

        // ✅ jika input dihapus
        if (keyword.length === 0) {
            $('#resultContainer').html(noDataHTML);
             $('#resultCount').text('0 products found');
            return;
        }

        fetchProducts(keyword);

    }, 400);

});


    // =============================
    // FETCH DATA API
    // =============================
    function fetchProducts(q = '') {

        $.ajax({
            url: "{{ route('product-knowledge.data') }}",
            method: "GET",
            data: { q: q },
            beforeSend() {
                $('#resultContainer').html(loadingSkeleton());
            },
            success(res) {

                if (!res.success) return;

                renderResults(res.data);
            },
            error() {
                $('#resultContainer').html(
                    '<p class="text-sm text-red-500">Failed to load data</p>'
                );
            }
        });
    }


    // =============================
    // RENDER RESULT
    // =============================
    function renderResults(data)
{
    $('#resultCount').text(`${data.length} products found`);

    if (!data || data.length === 0) {
        $('#resultContainer').html(emptyState());
        return;
    }

    let html = '';

    data.forEach(item => {

        // =============================
        // BUILD IMAGE GRID
        // =============================
        let imageHTML = '';

        if (item.images && item.images.length > 0) {

            item.images.slice(0,2).forEach((img, index) => {

                const label = index === 0 ? 'Check Point 1' : 'Check Point 2';

                imageHTML += `
                <a href="${img}" target="_blank"
                   class="group relative rounded-xl overflow-hidden
                          bg-slate-50 border border-gray-100
                          aspect-square block">

                    <img src="${img}"
                         class="w-full h-full object-cover
                                transition duration-300
                                group-hover:scale-105">

                    <div class="absolute inset-0
                                bg-gradient-to-t
                                from-black/10 to-transparent
                                opacity-0 group-hover:opacity-100
                                transition"></div>

                    <div class="absolute top-2 left-2
                                text-[10px] px-2 py-1
                                bg-white/80 backdrop-blur
                                rounded-md text-gray-700">
                        ${label}
                    </div>
                </a>`;
            });

        } else {

            imageHTML = `
            <div class="col-span-2 text-center py-10 text-gray-400 text-sm">
                <i class="fa-regular fa-image mb-2"></i>
                <p>No Image Available</p>
            </div>`;
        }

        // =============================
        // CARD + ACCORDION
        // =============================
        html += `
        <div class="group bg-white rounded-2xl
                    border border-gray-200/80 shadow-sm
                    hover:shadow-lg hover:border-indigo-200
                    transition-all duration-300">

            <!-- HEADER -->
            <button class="accordion-btn w-full px-4 md:px-6 py-4 text-left"
                    data-target="accordion-${item.id}">

                <div class="flex items-start md:items-center gap-4">

                    <div class="w-10 h-10 rounded-xl
                                bg-indigo-50 text-indigo-600
                                flex items-center justify-center">
                        <i class="fa-solid fa-box text-sm"></i>
                    </div>

                    <div class="flex-1 min-w-0">

                        <p class="font-semibold text-gray-800
                                  text-sm md:text-base truncate">
                            ${item.code} - ${item.description}
                        </p>

                        <div class="flex flex-wrap gap-2 mt-2">

                            <span class="px-2.5 py-1 text-[11px]
                                         rounded-md bg-indigo-50 text-indigo-600 font-medium">
                                ${item.article_type ?? '-'}
                            </span>

                            <span class="px-2.5 py-1 text-[11px]
                                         rounded-md bg-emerald-50 text-emerald-600 font-medium">
                                ${item.partner_name ?? '-'}
                            </span>

                            <span class="px-2.5 py-1 text-[11px]
                                         rounded-md bg-amber-50 text-amber-600 font-medium
                                         flex items-center gap-1">
                                <i class="fa-solid fa-cubes text-[10px]"></i>
                                Standard Package: ${item.min_package ?? '-'}
                            </span>

                        </div>
                    </div>

                    <i class="fa-solid fa-chevron-down
                              text-gray-400 transition-transform duration-300"></i>

                </div>
            </button>

            <!-- ACCORDION BODY -->
            <div id="accordion-${item.id}"
                 class="accordion-content hidden
                        border-t border-gray-100
                        px-4 md:px-6 py-5">

                <div class="grid grid-cols-2 gap-4 md:gap-6">
                    ${imageHTML}
                </div>

            </div>

        </div>`;
    });

    $('#resultContainer').html(html);
}


    // =============================
    // LOADING SKELETON (SaaS feel)
    // =============================
    function loadingSkeleton() {
        return `
            <div class="animate-pulse space-y-3">
                <div class="h-20 bg-gray-100 rounded-2xl"></div>
                <div class="h-20 bg-gray-100 rounded-2xl"></div>
                <div class="h-20 bg-gray-100 rounded-2xl"></div>
            </div>
        `;
    }

    // =============================
    // EMPTY STATE
    // =============================
    function emptyState() {
        return `
            <div class="text-center py-10 text-gray-400 text-sm">
                <i class="fa-solid fa-magnifying-glass mb-2 text-lg"></i>
                <p>No products found</p>
            </div>
        `;
    }

});
</script>
@endpush
@endsection
