        // Get the modals
        var addModal = document.getElementById("addModal");
        var editModal = document.getElementById("editModal");
        var deleteModal = document.getElementById("deleteModal");
        
        // Get the search input
        var searchInput = document.querySelector('input[name="search"]');
        var searchTimeout;
        
        // Add event listener for search input
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    searchTasks(searchInput.value);
                }, 500); // Delay for 500ms after user stops typing
            });
        }
        
        // Function to perform AJAX search
        function searchTasks(searchTerm) {
            fetch('search_tasks.php?search=' + encodeURIComponent(searchTerm))
                .then(response => response.json())
                .then(tasks => {
                    updateTasksTable(tasks);
                })
                .catch(error => console.error('Error:', error));
        }
        
        // Function to update the tasks table
        function updateTasksTable(tasks) {
            const tbody = document.querySelector('table tbody');
            if (!tbody) return;
            
            if (tasks.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Tidak ada tugas</td></tr>';
                return;
            }
            
            let html = '';
            tasks.forEach(task => {
                html += `
                    <tr>
                        <td>${task.no}</td>
                        <td>${task.judul}</td>
                        <td>${task.deskripsi}</td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="${task.id}">
                                <input type="hidden" name="current_status" value="${task.status}">
                                <button type="submit" name="change_status" class="${task.status === 'Selesai' ? 'status-done' : 'status-pending'}">
                                    ${task.status}
                                </button>
                            </form>
                        </td>
                        <td>${task.tanggal_mulai}</td>
                        <td>${task.tanggal_selesai}</td>
                        <td>
                            <button class="btn btn-edit" onclick="openEditModal(${task.id}, '${task.judul}', '${task.deskripsi}', '${task.status}', '${task.tanggal_mulai}', '${task.tanggal_selesai}')">Edit</button>
                            <button class="btn btn-delete" onclick="openDeleteModal(${task.id}, '${task.judul}')">Hapus</button>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }
        
        // Get the buttons that open the modals
        var addBtn = document.getElementById("openAddModal");
        
        if (addBtn) {
            // Get the <span> elements that close the modals
            var closeAddModal = document.getElementById("closeAddModal");
            var closeEditModal = document.getElementById("closeEditModal");
            var closeDeleteModal = document.getElementById("closeDeleteModal");
            var cancelDelete = document.getElementById("cancelDelete");
            
            // When the user clicks the button, open the modal
            addBtn.onclick = function() {
                addModal.style.display = "block";
            }
            
            // When the user clicks on <span> (x), close the modal
            closeAddModal.onclick = function() {
                addModal.style.display = "none";
            }
            
            closeEditModal.onclick = function() {
                editModal.style.display = "none";
            }
            
            closeDeleteModal.onclick = function() {
                deleteModal.style.display = "none";
            }
            
            cancelDelete.onclick = function() {
                deleteModal.style.display = "none";
            }
            
            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == addModal) {
                    addModal.style.display = "none";
                }
                if (event.target == editModal) {
                    editModal.style.display = "none";
                }
                if (event.target == deleteModal) {
                    deleteModal.style.display = "none";
                }
            }
        }
        
        // Function to open edit modal with task data
        function openEditModal(id, judul, deskripsi, status, tanggal_mulai, tanggal_selesai) {
            document.getElementById("edit_id").value = id;
            document.getElementById("edit_judul").value = judul;
            document.getElementById("edit_deskripsi").value = deskripsi;
            document.getElementById("edit_status").value = status;
            document.getElementById("edit_tanggal_mulai").value = tanggal_mulai;
            document.getElementById("edit_tanggal_selesai").value = tanggal_selesai;
            editModal.style.display = "block";
        }
        
        // Function to open delete confirmation modal
        function openDeleteModal(id, judul) {
            document.getElementById("delete_id").value = id;
            document.getElementById("delete_task_name").textContent = judul;
            deleteModal.style.display = "block";
        }