<!-- Edit Modal-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Profile</h5>
        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" enctype="multipart/form-data">
          <div class=" mb-3 text-center">
            <img src="../assets/img/<?= $auth->getUser()->profile_picture; ?>" alt="<?= $auth->getUser()->profile_picture; ?>" class="rounded-circle mb-3" width="200">
            <div class="custom-file">
              <input type="file" class="custom-file-input <?= Validator::hasValidationError("profile_picture") ? "is-invalid" : "" ?>" name="profile_picture" id="profile_picture"">
              <label class=" custom-file-label" for="profile_picture">Choose Image</label>
              <div class="invalid-feedback">
                <?= Validator::getValidationError("profile_picture"); ?>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="full_name" class="col-form-label">Full Name:</label>
            <input type="text" class="form-control" name="full_name" id="full_name" value="<?= $request->getOldData("full_name") ?? $auth->getUser()->full_name ?>">
          </div>
          <div class="mb-3">
            <label for="phone_number" class="col-form-label">Phone Number:</label>
            <input type="text" class="form-control <?= Validator::hasValidationError("phone_number") ? "is-invalid" : "" ?>" name="phone_number" id="phone_number" value="<?= $request->getOldData("phone_number") ?? $auth->getUser()->phone_number ?>">
            <div class="invalid-feedback">
              <?= Validator::getValidationError("phone_number") ?>
            </div>
          </div>
          <div class="mb-3">
            <label for="address" class="col-form-label">Address:</label>
            <textarea class="form-control" name="address" id="address"><?= $request->getOldData("address") ?? $auth->getUser()->address ?></textarea>
          </div>
          <div class="mb-3">
            <label for="bio" class="col-form-label">Bio:</label>
            <textarea class="form-control" name="bio" id="bio" rows="4"><?= $request->getOldData("bio") ?? $auth->getUser()->bio ?></textarea>
          </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" type="submit" name="update">Save Change</button>
      </div>
      </form>
    </div>
  </div>
</div>