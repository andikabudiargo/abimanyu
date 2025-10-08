@php
    $userDepartments = Auth::user()->departments->pluck('name')->toArray();
     $userRoles = Auth::user()->roles->pluck('name')->toArray();
@endphp
<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header flex items-center py-4 px-6 h-header-height border-b border-gray-200">
      <a href="../dashboard/index.html" class="b-brand flex items-center gap-3">
        <!-- ========   Change your logo from here   ============ -->
        <img src="{{ asset('img/logo.png') }}" class="img-fluid h-12 w-auto" alt="logo" />
      </a>
    </div>
    <div class="navbar-content h-[calc(100vh_-_74px)] py-2.5">
      <ul class="pc-navbar">
        <li class="pc-item pc-caption">
          <label>Navigation</label>
        </li>
        <li class="pc-item">
        <li class="pc-item">
          <a href="{{ route('dashboard') }}" class="pc-link">
            <span class="pc-micon">
              <i data-feather="home"></i>
            </span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

            @if(
    in_array('Superuser', $userRoles)
)
  <li class="pc-item pc-caption">
          <label>Material Management</label>
          <i data-feather="monitor"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="plus-square"></i> </span><span
              class="pc-mtext">Article Management</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('inventory.article.index') }}">Article</a></li>
            <li class="pc-item"><a href="{{ route('inventory.article-type.index') }}" class="pc-link">Article Type</a></li>
            <li class="pc-item"><a href="{{ route('inventory.gom.index') }}" class="pc-link">Group of Material</a></li>
               </ul>
         </li>
          <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="pie-chart"></i> </span><span
              class="pc-mtext">UOM Management</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a href="#!" class="pc-link">Unit of Measurement (UOM)</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">UOM Conversion</a></li>
             </ul>
            </li>

        <li class="pc-item pc-caption">
          <label>Supply Chain Management</label>
          <i data-feather="feather"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="shopping-cart"></i> </span><span
              class="pc-mtext">Purchase Management</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('purchasing.pr.index') }}">Purchase Request</a></li>
           
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Purchase Order</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Forecasting</a></li>
            <li class="pc-item"><a href="{{ route('purchasing.supplier.index') }}" class="pc-link">Supplier Management</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Delivery Instruction</a></li>
            
             </ul>
            </li>
           
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="phone-call"></i> </span><span
              class="pc-mtext">Sales Management</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('purchasing.pr.index') }}">Sales Order</a></li>
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Target Sales Order</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Forecasting</a></li>
            <li class="pc-item"><a href="{{ route('marketing.customer.index') }}" class="pc-link">Customer Management</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Conversion</a></li>
             </ul>
            </li>
            @endif

            @if(
    in_array('Production Planning & Inventory Control', $userDepartments) || 
    in_array('PPIC - Delivery', $userDepartments) || 
    in_array('PPIC - Logistik', $userDepartments) || 
    in_array('Superuser', $userRoles)
)

 <li class="pc-item pc-caption">
          <label>Engineering Management</label>
          <i data-feather="monitor"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="codepen"></i> </span><span
          class="pc-mtext">Product Development</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('ppic.warehouse.index') }}">Bill of Material</a></li>
             </ul>
            </li>

            <li class="pc-item pc-caption">
          <label>Planning & Inventory Control</label>
          <i data-feather="monitor"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="calendar"></i> </span><span
          class="pc-mtext">Planning</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('ppic.warehouse.index') }}">Bill of Material</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('ppic.warehouse.index') }}">Work Order Sheet</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('ppic.transfer-out.index') }}">WOS Mixing</a></li>
             </ul>
            </li>
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="truck"></i> </span><span
          class="pc-mtext">Delivery</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('ppic.warehouse.index') }}">Delivery Note</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('ppic.transfer-out.index') }}">WOS Mixing</a></li>
             </ul>
            </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="repeat"></i> </span><span
          class="pc-mtext">Logistic</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('ppic.warehouse.index') }}">Warehouse</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('ppic.rec.index') }}">Receiving</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('ppic.transfer-in.index') }}">Transfer In</a></li>
             <li class="pc-item"><a class="pc-link" href="{{ route('ppic.transfer-out.index') }}">Transfer Out</a></li>
             <li class="pc-item"><a class="pc-link" href="{{ route('ppic.stock.index') }}">Stock</a></li>
             </ul>
            </li>
            
            <li class="pc-item pc-caption">
          <label>Production Management</label>
          <i data-feather="monitor"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="package"></i> </span><span
              class="pc-mtext">Production Material</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('production.mrc.index') }}">Material Management</a></li>
             <li class="pc-item"><a href="{{ route('inventory.article-type.index') }}" class="pc-link">Stock Management</a></li>
             </ul>
            </li>
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="activity"></i> </span><span
              class="pc-mtext">Production Process</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
                <li class="pc-item"><a href="{{ route('production.workstation.index') }}" class="pc-link">Workstation Management</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('inventory.article.index') }}">Production Loading</a></li>
             </ul>
            </li>
            @endif

             @if(
    in_array('Quality', $userDepartments) || 
    in_array('Quality Control', $userDepartments) ||  
    in_array('Superuser', $userRoles)
)
            <li class="pc-item pc-caption">
          <label>Quality Management</label>
          <i data-feather="monitor"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="search"></i> </span><span
              class="pc-mtext">Inspection</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('qc.inspections.index') }}">Daily Inspection</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('qc.incoming.index') }}">Incoming</a></li>
            <li class="pc-item"><a href="{{ route('qc.unloading.index') }}" class="pc-link">Unloading</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Buffing</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Touch Up</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Final</a></li>
             </ul>
            </li>
          
             <li class="pc-item">
          <a href="{{ route('qc.defect.index') }}" class="pc-link"><span class="pc-micon"> <i data-feather="x-octagon"></i> </span><span
              class="pc-mtext">Master Data Defect</span><span class="pc-arrow"></span></a>
   </li>
     @endif
            
           @if(
            in_array('Finance & Accounting', $userDepartments) || 
    in_array('Superuser', $userRoles)
)
       <li class="pc-item pc-caption">
          <label>Finance & Accounting</label>
          <i data-feather="feather"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="file-text"></i> </span><span
              class="pc-mtext">Invoice Management</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Invoice Supplier (AP)</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Invoice Customer (AR)</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Debit Note</a></li>
             </ul>
            </li>
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="dollar-sign"></i> </span><span
              class="pc-mtext">Cash & Bank</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Cash Transaction</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Bank Transaction</a></li>
             </ul>
            </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="bar-chart-2"></i> </span><span
              class="pc-mtext">Financial Statement</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">General Ledger</a></li>
             <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">General Journal</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Trial Balance</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Neraca</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Laba Rugi</a></li>
             </ul>
            </li>
           <li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon"><i data-feather="activity"></i></span>
    <span class="pc-mtext">Costing & Budgeting</span>
    <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item"><a href="" class="pc-link">Chart of Account (COA)</a></li>
    <li class="pc-item"><a href="" class="pc-link">Cost Center</a></li>
     <li class="pc-item pc-hasmenu">
      <a href="#!" class="pc-link">Costing Management<span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
      <ul class="pc-submenu">
    <li class="pc-item"><a href="" class="pc-link">Product Costing</a></li>
    <li class="pc-item"><a href="" class="pc-link">Project Costing</a></li>
      </ul>
     </li>
    <li class="pc-item pc-hasmenu">
      <a href="#!" class="pc-link">Budgeting Management<span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
      <ul class="pc-submenu">
        <li class="pc-item"><a href="" class="pc-link">Investment Budget (CAPEX)</a></li>
        <li class="pc-item"><a href="" class="pc-link">Operational Budget (OPEX)</a></li>
      </ul>
    </li>
  </ul>
</li>
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="trending-down"></i> </span><span
              class="pc-mtext">Assets Management</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Asset Depreciation</a></li>
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Stock Opname</a></li>
             </ul>
            </li>
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="percent"></i> </span><span
              class="pc-mtext">Tax Management</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Tax Master Data</a></li>
             <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Tax Payment & Filling</a></li>
             </ul>
            </li>
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="file-plus"></i> </span><span
              class="pc-mtext">Additional Report</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Sales Order Report</a></li>
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Delivery Note Report</a></li>
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Invoice Receipt</a></li>
             </ul>
            </li>
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="credit-card"></i> </span><span
              class="pc-mtext">Supporting Tools</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">Fuel Calculator</a></li>
            <li class="pc-item"><a href="{{ route('purchasing.po.index') }}" class="pc-link">VAT Calculator</a></li>
            <li class="pc-item"><a href="{{ route('fa.cabom.index') }}" class="pc-link">BOM Calculator</a></li>
            <li class="pc-item"><a href="{{ route('fa.cmbom.index') }}" class="pc-link">Chemical Traceability</a></li>
             </ul>
            </li>
        @endif
   

        <li class="pc-item pc-caption">
  <label>Facility Management</label>
  <i data-feather="monitor"></i>
</li>

<!-- Meeting Room -->
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon"><i data-feather="calendar"></i></span>
    <span class="pc-mtext">Meeting Room</span>
    <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('facility.booking-room.index') }}">Booking Meeting Room</a>
    </li>
    @if(in_array('General Affair', $userDepartments) || in_array('Superuser', $userRoles))
    <li class="pc-item">
      <a href="{{ route('facility.room.index') }}" class="pc-link">Room Management</a>
    </li>
    @endif
  </ul>
</li>

<!-- Loan Management -->
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon"><i data-feather="repeat"></i></span>
    <span class="pc-mtext">Loan Management</span>
    <span class="pc-arrow"><i class="ti ti-chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item">
      <a class="pc-link" href="{{ route('facility.alo.index') }}">Assets Loan</a>
    </li>
  </ul>
</li>

            
             <li class="pc-item pc-caption">
          <label>Service Desk Management</label>
          <i data-feather="monitor"></i>
        </li>
        <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="tool"></i> </span><span
              class="pc-mtext">Helpdesk Management</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('it.ticket.index') }}">Ticket Management</a></li>
             @if(
    in_array('Information & Technology', $userDepartments) || 
    in_array('Maintenance', $userDepartments) || 
    in_array('Superuser', $userRoles)
)
            <li class="pc-item"><a class="pc-link" href="{{ route('it.category.index') }}">Category Management</a></li>
            <li class="pc-item"><a href="{{ route('inventory.article-type.index') }}" class="pc-link">Knowledge Base</a></li>
            @endif
             </ul>
            </li>
              @if(
    in_array('Superuser', $userRoles)
)

            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="book"></i> </span><span
              class="pc-mtext">Cooperative Management</span><span class="pc-arrow"></i><i class="ti ti-chevron-right"></i></span></a>
               <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('cooperative.sales.index') }}">Sales Management</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('it.project.index') }}">Transaction Management</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('it.project.index') }}">Product Management</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('it.project.index') }}">Stock Management</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('it.project.index') }}">Cashflow Management</a></li>
             </ul>
            </li>
            @endif


  @if(
    in_array('Superuser', $userRoles)
)
        <li class="pc-item pc-caption">
          <label>Human Resources Management</label>
          <i data-feather="monitor"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="clock"></i> </span><span
              class="pc-mtext">Attendances Managament</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Machines Management</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Presences Managament</a></li>
             </ul>
            </li>

            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="users"></i> </span><span
              class="pc-mtext">Organization Managament</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('hr.department.index') }}">Department Management</a></li>
            <li class="pc-item"><a href="{{ route('hr.position.index') }}" class="pc-link">Position Managament</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Employees Managament</a></li>
             </ul>
            </li>
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="file-text"></i> </span><span
              class="pc-mtext">Legal & Policies</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Company Rules</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Decision Letter</a></li>
             </ul>
            </li>
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="briefcase"></i> </span><span
              class="pc-mtext">Payroll Management</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Work Schedule</a></li>
            <li class="pc-item"><a class="pc-link" href="#!">Leave Management</a></li>
            <li class="pc-item"><a class="pc-link" href="#!">Overtime Management</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Salaries Management</a></li>
             </ul>
            </li>
           
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="user-plus"></i> </span><span
              class="pc-mtext">Recruitment</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Candidate Pool</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Test Tools</a></li>
             </ul>
            </li>  

             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="zoom-in"></i> </span><span
              class="pc-mtext">Development & Training</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Performance Apprasial</a></li>
             <li class="pc-item"><a class="pc-link" href="#!">Competency & Skill Matrix</a></li>
            <li class="pc-item"><a class="pc-link" href="#!">Training Monitoring</a></li>
             </ul>
            </li>

             <li class="pc-item pc-caption">
          <label>General Affair Management</label>
          <i data-feather="monitor"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="home"></i> </span><span
              class="pc-mtext">Infrastructure Management</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Plant Management</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Room Managament</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Vehicle Managament</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Preventif & Cleaning</a></li>
             </ul>
            </li>
              <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="package"></i> </span><span
              class="pc-mtext">Inventory Managament</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
           <li class="pc-item"><a href="#!" class="pc-link">Assets Managament</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Office Supply Managament</a></li>
             </ul>
            </li>
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="coffee"></i> </span><span
              class="pc-mtext">Catering Management</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('hr.department.index') }}">Catering Monitoring</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('hr.department.index') }}">Order Management</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Feedback & Quality Control</a></li>
             </ul>
            </li>
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="compass"></i> </span><span
              class="pc-mtext">Project Management</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Project Monitoring</a></li>
             </ul>
            </li>
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="user"></i> </span><span
              class="pc-mtext">Vendor Managament</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Adjustment</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Overtime</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Resign</a></li>
             </ul>
            </li>
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="file-text"></i> </span><span
              class="pc-mtext">Document & Permit</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Adjustment</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Overtime</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Resign</a></li>
             </ul>
            </li>
           

             <li class="pc-item pc-caption">
          <label>IT & Network Management</label>
          <i data-feather="monitor"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="archive"></i> </span><span
              class="pc-mtext">IT Inventory Management</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('it.assets.index') }}">Assets Management</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Consumable Management</a></li>
             <li class="pc-item"><a href="#!" class="pc-link">Maintenance & Repair</a></li>
             </ul>
            </li>
 <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="server"></i> </span><span
              class="pc-mtext">IT Project Management</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Project Monitoring</a></li>
            <li class="pc-item"><a class="pc-link" href="#!">Vendor Management</a></li>
             </ul>
            </li>
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="hard-drive"></i> </span><span
              class="pc-mtext">IT Backup Management</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('it.backup.index') }}">Backup Log</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('it.backup-schedule.index') }}">Backup Schedule Plan</a></li>
             <li class="pc-item"><a class="pc-link" href="{{ route('it.storage.index') }}">Storage Management</a></li>
            <li class="pc-item"><a href="{{ route('hr.position.index') }}" class="pc-link">Disaster Recovery Plan</a></li>
             </ul>
            </li>
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="wifi"></i> </span><span
              class="pc-mtext">IT Network & Access</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Network Monitoring</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Remote Access Tracking</a></li>
             </ul>
            </li>
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="shield"></i> </span><span
              class="pc-mtext">IT Security & Compliance</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="#!">Account & Password</a></li>
            <li class="pc-item"><a href="#!" class="pc-link">Policy Management</a></li>
             </ul>
            </li>
            @endif


            
             <li class="pc-item pc-caption">
          <label>Management Representative</label>
          <i data-feather="monitor"></i>
        </li>
         <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="file-plus"></i> </span><span
              class="pc-mtext">Document Management</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
          <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('mr.doc.index') }}">Document Control Center</a></li>
             </ul>
            </li>
@if(
    in_array('Information & Technology', $userDepartments) || 
    in_array('Superuser', $userRoles)
)
            <li class="pc-item pc-caption">
          <label>Setting</label>
        </li>
            <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="user"></i> </span><span
              class="pc-mtext">Account Setting</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
              <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('setting.user.index') }}">User Management</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('setting.role.index') }}">Role Management</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('setting.role.index') }}">Permission Management</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('setting.role.index') }}">Approval Management</a></li>
             </ul>
            </li>
             <li class="pc-item pc-hasmenu">
          <a href="#!" class="pc-link"><span class="pc-micon"> <i data-feather="settings"></i> </span><span
              class="pc-mtext">System Setting</span><span class="pc-arrow"><i class="ti ti-chevron-right"></i></span></a>
              <ul class="pc-submenu">
            <li class="pc-item"><a class="pc-link" href="{{ route('setting.user.index') }}">Lock Transaction</a></li>
            <li class="pc-item"><a class="pc-link" href="{{ route('setting.role.index') }}">Log Activity</a></li>
            </li>
            @endif
      </ul>
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->