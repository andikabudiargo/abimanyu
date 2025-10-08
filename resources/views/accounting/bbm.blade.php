<style>
  /* Remove unnecessary default CSS for DataTables */
</style>

<main class="p-4">
  <div class="grid grid-cols-1 gap-4">
    <div class="bg-white shadow-lg rounded-lg w-full">
      <div class="bg-blue-800 text-white px-4 py-2 rounded-t-lg">
        <h5 class="text-lg font-bold">DATA</h5>
      </div>
      <div class="p-4">
        <div class="grid md:grid-cols-2 gap-6">

          <!-- Armada Table -->
          <div class="overflow-x-auto">
            <table id="data-table" class="min-w-full table-auto text-sm">
              <thead class="bg-blue-800 text-white">
                <tr>
                  <th class="text-left px-2 py-1">Action</th>
                  <th class="text-left px-2 py-1">Armada</th>
                  <th class="text-center px-2 py-1">BBM</th>
                  <th class="text-center px-2 py-1">Rasio</th>
                  <th class="text-center px-2 py-1">Harga</th>
                  <th class="text-center px-2 py-1">Spare</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($vehicles as $row) : ?>
                  <tr class="even:bg-gray-200 odd:bg-white text-center hover:bg-gray-100 select-armada" 
                      data-id="<?= $row['id']; ?>" 
                      data-nama="<?= $row['name']; ?>" 
                      data-bbm="<?= $row['nama']; ?>" 
                      data-rasio="<?= $row['rasio']; ?>" 
                      data-harga="<?= $row['harga']; ?>" 
                      data-spare="<?= $row['spare']; ?>">
                    <td class="text-left px-2 py-1 relative">
                      <button onclick="toggleMenuArmada('menu-armada-<?= $row['id']; ?>', this)" class="text-gray-700">
                        <i class="material-icons">more_vert</i>
                      </button>
                      <div id="menu-armada-<?= $row['id']; ?>" class="hidden absolute bg-white rounded shadow-md p-2 z-50">
                        <a href="#" class="flex items-center gap-2 p-2 hover:bg-gray-100 edit-armada-link"
                          data-id="<?= $row['id']; ?>" data-name="<?= $row['name']; ?>"
                          data-bbm="<?= $row['bbm_id']; ?>" data-spare="<?= $row['spare']; ?>"
                          data-rasio="<?= $row['rasio']; ?>">
                          <i class="material-icons text-sm">edit</i>
                          <span>Edit</span>
                        </a>
                        <a href="" class="flex items-center gap-2 p-2 hover:bg-gray-100 text-red-600 delete-armada-link" data-armada-name="<?= $row['name']; ?>">
                          <i class="material-icons text-sm">delete</i>
                          <span>Delete</span>
                        </a>
                      </div>
                    </td>
                    <td class="text-left px-2 py-1"><?= $row['name']; ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['rasio']; ?></td>
                    <td><?= $row['harga']; ?></td>
                    <td><?= $row['spare']; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <button class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" data-bs-toggle="modal" data-bs-target="#modalTambahArmada">
              <i class="material-icons text-sm">create</i> Tambah Data Armada
            </button>
          </div>

          <!-- Divider -->
          <div class="hidden md:flex justify-center items-center">
            <div class="w-0.5 h-full bg-gray-300"></div>
          </div>

          <!-- BBM Table -->
          <div class="overflow-x-auto">
            <table id="data-bbm" class="min-w-full table-auto text-sm">
              <thead class="bg-blue-800 text-white">
                <tr>
                  <th class="text-left px-2 py-1">Action</th>
                  <th class="text-left px-2 py-1">Nama</th>
                  <th class="text-center px-2 py-1">Harga</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($bbm as $b) : ?>
                  <tr class="even:bg-gray-200 odd:bg-white text-center hover:bg-gray-100 select-armada" data-id="<?= $b->id; ?>">
                    <td class="text-left px-2 py-1 relative">
                      <button onclick="toggleMenuBBM('menu-bbm-<?= $b->id; ?>', this)" class="text-gray-700">
                        <i class="material-icons">more_vert</i>
                      </button>
                      <div id="menu-bbm-<?= $b->id; ?>" class="hidden absolute bg-white rounded shadow-md p-2 z-50">
                        <a href="" class="flex items-center gap-2 p-2 hover:bg-gray-100">
                          <i class="material-icons text-sm">note</i>
                          <span>Edit</span>
                        </a>
                        <a href="" class="flex items-center gap-2 p-2 hover:bg-gray-100 text-red-600 delete-bbm-link" data-bbm-name="<?= $b->nama; ?>">
                          <i class="material-icons text-sm">delete</i>
                          <span>Delete</span>
                        </a>
                      </div>
                    </td>
                    <td class="text-left px-2 py-1"><?= $b->nama; ?></td>
                    <td><?= $b->harga; ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <button class="mt-4 bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700" data-bs-toggle="modal" data-bs-target="#modalTambahBBM">
              <i class="material-icons text-sm">create</i> Tambah Data BBM
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</main>
