// Function to validate the form
function validateForm() {
    var firstName = document.getElementById('first_name').value.trim();
    var lastName = document.getElementById('last_name').value.trim();
    var gender = document.querySelector('input[name="gender"]:checked');
    var age = document.getElementById('age').value;
    var adhaarNo = document.getElementById('adhaar_no').value.trim();
    var city = document.getElementById('city').value.trim();
    var pinCode = document.getElementById('pin-code').value.trim();
    var contactNumber = document.getElementById('contact_number').value.trim();
    var username = document.getElementById('username').value.trim();
    var password = document.getElementById('password').value.trim();
    var confirmPassword = document.getElementById('confirm_password').value.trim();
  
    // Check if required fields are empty
    if (firstName === '' || lastName === '' || !gender || age === '' || adhaarNo === '' || city === '' || pinCode === '' || contactNumber === '' || username === '' || password === '' || confirmPassword === '') {
      alert('Please fill in all required fields.');
      return false;
    }
  
    // Check Aadhar number length
    if (adhaarNo.length !== 12) {
      alert('Aadhar number should be 12 digits.');
      return false;
    }
  
    // Check Pin code length
    if (pinCode.length !== 6) {
      alert('Pin code should be 6 digits.');
      return false;
    }
  
    // Check Contact number length
    if (contactNumber.length !== 10) {
      alert('Contact number should be 10 digits.');
      return false;
    }
  
    // Check if password matches confirm password
    if (password !== confirmPassword) {
      alert('Passwords do not match.');
      return false;
    }
  
    // If all validations pass, form is valid
    return true;
  }
  
  // Event listener for form submission
  document.getElementById('Registration').addEventListener('submit', function(event) {
    if (!validateForm()) {
      event.preventDefault(); // Prevent form submission if validation fails
    }
  });
  