
<button class="add-btn" id="openAddModal">Tambah Tugas</button>

<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        // Get user tasks
        $user_id = $_SESSION["user_id"];
        $sql = "SELECT * FROM tasks WHERE user_id = $user_id ORDER BY id DESC";
        $result = $conn->query($sql);
        
        $counter = 1;
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) { 
        ?>
        <tr>
            <td><?php echo $counter++; ?></td>
            <td><?php echo $row["judul"]; ?></td>
            <td><?php echo $row["deskripsi"]; ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                    <input type="hidden" name="current_status" value="<?php echo $row["status"]; ?>">
                    <button type="submit" name="change_status" class="<?php echo ($row["status"] == 'Selesai') ? 'status-done' : 'status-pending'; ?>">
                        <?php echo $row["status"]; ?>
                    </button>
                </form>
            </td>
            <td><?php echo date('d-m-Y', strtotime($row["tanggal_mulai"])); ?></td>
            <td><?php echo date('d-m-Y', strtotime($row["tanggal_selesai"])); ?></td>
            <td>
                <button class="btn btn-edit" onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo $row['judul']; ?>', '<?php echo $row['deskripsi']; ?>', '<?php echo $row['status']; ?>', '<?php echo $row['tanggal_mulai']; ?>', '<?php echo $row['tanggal_selesai']; ?>')">Edit</button>
                
                <button class="btn btn-delete" onclick="openDeleteModal(<?php echo $row['id']; ?>, '<?php echo $row['judul']; ?>')">Hapus</button>
            </td>
        </tr>
        <?php 
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>Tidak ada tugas</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Add Task Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddModal">&times;</span>
        <h2>Tambah Tugas Baru</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="judul">Judul:</label>
                <input type="text" id="judul" name="judul" required>
            </div>
            <div class="form-group">
                <label for="deskripsi">Deskripsi:</label>
                <textarea id="deskripsi" name="deskripsi" required></textarea>
            </div>
            <div class="form-group">
                <label for="tanggal_mulai">Tanggal Mulai:</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" required>
            </div>
            <div class="form-group">
                <label for="tanggal_selesai">Tanggal Selesai:</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" required>
            </div>
            <button type="submit" name="add" class="form-submit">Tambah Tugas</button>
        </form>
    </div>
</div>

<!-- Edit Task Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edit Tugas</h2>
        <form method="post" action="">
            <input type="hidden" id="edit_id" name="id">
            <div class="form-group">
                <label for="edit_judul">Judul:</label>
                <input type="text" id="edit_judul" name="judul" required>
            </div>
            <div class="form-group">
                <label for="edit_deskripsi">Deskripsi:</label>
                <textarea id="edit_deskripsi" name="deskripsi" required></textarea>
            </div>
            <div class="form-group">
                <label for="edit_status">Status:</label>
                <select id="edit_status" name="status">
                    <option value="Belum Selesai">Belum Selesai</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_tanggal_mulai">Tanggal Mulai:</label>
                <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai" required>
            </div>
            <div class="form-group">
                <label for="edit_tanggal_selesai">Tanggal Selesai:</label>
                <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai" required>
            </div>
            <button type="submit" name="update" class="form-submit">Update Tugas</button>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeDeleteModal">&times;</span>
        <h2>Konfirmasi Hapus</h2>
        <p>Apakah Anda yakin ingin menghapus tugas "<span id="delete_task_name"></span>"?</p>
        <form method="post" action="">
            <input type="hidden" id="delete_id" name="id">
            <button type="submit" name="delete" class="form-submit" style="background-color: #e74c3c;">Hapus</button>
            <button type="button" id="cancelDelete" class="form-submit" style="background-color: #7f8c8d; margin-top: 10px;">Batal</button>
        </form>
    </div>
</div>
