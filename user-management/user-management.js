document.addEventListener('DOMContentLoaded', () => {
  // --- Add user button to add-user1 after click ---
  const addUserButton = document.getElementById('addUserBtn');
  if (addUserButton) {
    addUserButton.addEventListener('click', () => {
      window.location.href = 'add-user1.html';
    });
  }

  const editButtons = document.querySelectorAll('.edit-button');
  editButtons.forEach(button => {
    button.addEventListener('click', () => {
      window.location.href = 'edit-user.html';
    });
  });

  // --- Delete Confirmation Modal Actions Table ---
  const deleteButtons = document.querySelectorAll('.delete-button');
  const deleteModal = document.getElementById('deleteConfirmationModal');
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
  let rowToDelete = null;

  deleteButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      rowToDelete = e.target.closest('.table-row');
      if (deleteModal) {
        deleteModal.classList.remove('hidden');
      }
    });
  });

  if (cancelDeleteBtn && deleteModal) {
    cancelDeleteBtn.addEventListener('click', () => {
      deleteModal.classList.add('hidden');
      rowToDelete = null;
    });
  }

  if (confirmDeleteBtn && deleteModal) {
    confirmDeleteBtn.addEventListener('click', () => {
      if (rowToDelete) {
        rowToDelete.remove();
      }
      deleteModal.classList.add('hidden');
      rowToDelete = null;
    });
  }

});
