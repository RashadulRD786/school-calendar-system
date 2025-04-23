document.addEventListener('DOMContentLoaded', () => {
  const addUserButton = document.getElementById('addUserBtn');
  const editButtons = document.querySelectorAll('.edit-button');
  const deleteButtons = document.querySelectorAll('.delete-button');

  const deleteModal = document.getElementById('deleteConfirmationModal'); // This modal is not present in your HTML, may want to remove
  const successModal = document.getElementById('successModal');
  const closeSuccessBtn = document.getElementById('closeBtn');

  let rowToDelete = null;

  if (addUserButton) {
    addUserButton.addEventListener('click', () => {
      window.location.href = 'add-user1.html';
    });
  }

  editButtons.forEach(button => {
    button.addEventListener('click', () => {
      window.location.href = 'edit-user.html';
    });
  });

  deleteButtons.forEach(button => {
    button.addEventListener('click', (e) => {
      rowToDelete = e.target.closest('.table-row');
      if (deleteModal) {
        deleteModal.classList.remove('hidden');
      }
    });
  });

  // These buttons do not exist in your current HTML. Remove or implement the modal if needed.
  const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

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

  if (closeSuccessBtn && successModal) {
    closeSuccessBtn.addEventListener('click', () => {
      successModal.classList.add('hidden');
    });
  }
});
