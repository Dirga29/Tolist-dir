        // Get the modals
        var addModal = document.getElementById("addModal");
        var editModal = document.getElementById("editModal");
        var deleteModal = document.getElementById("deleteModal");
        
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