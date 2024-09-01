const customFileInput = document.getElementById("profile_picture");
const customFileLabel = document.querySelector(".custom-file-label");
customFileInput.addEventListener("change", function () {
  customFileLabel.textContent = this.files[0].name;
});
