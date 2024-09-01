<!-- Ban User Modal-->
<div class="modal fade" id="banUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Are you sure to <?= ($user->status === "banned" ? "unban " : "ban ") . $user->username; ?>?</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">Select "<?= $user->status === "banned" ? "Unban" : "Ban"; ?>" below if you are ready to <?= ($user->status === "banned" ? "unban " : "ban ") .  $user->username; ?>.</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <form method="post">
          <input type="hidden" name="username" value="<?= $user->username; ?>">
          <input type="hidden" name="role" value="<?= $user->role_name; ?>">
          <input type="hidden" name="status" value="<?= $user->status === "banned" ? "active" : "banned"; ?>">
          <button class="btn btn-<?= $user->status === "banned" ? "success" : "warning"; ?>" name="ban_user"><?= $user->status === "banned" ? "Unban" : "Ban"; ?></button>
        </form>
      </div>
    </div>
  </div>
</div>