<!-- Delete User Modal-->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Are you sure to delete <?= $user->username; ?>?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">Select "Delete" below if you are ready to delete <?= $user->username; ?> permanently.</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <form method="post">
          <input type="hidden" name="username" value="<?= $user->username; ?>">
          <input type="hidden" name="role" value="<?= $user->role_name; ?>">
          <input type="hidden" name="profile_picture" value="<?= $user->profile_picture; ?>">
          <button class="btn btn-danger" name="delete_user">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>