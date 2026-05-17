// script.js

// Register user
async function registerUser(event) {
  event.preventDefault(); // stop form from refreshing the page

  const studentId = document.getElementById('studentId').value;
  const lastName = document.getElementById('lastName').value;
  const middleName = document.getElementById('middleName').value;
  const firstName = document.getElementById('firstName').value;
  const password = document.getElementById('password').value;
  const role = document.getElementById('role').value;

  try {
    const response = await fetch('http://localhost:3000/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ studentId, lastName, middleName, firstName, password, role })
    });

    const data = await response.json();
    if (response.ok) {
      alert(data.message);
      window.location.href = "login.html"; // redirect after success
    } else {
      alert("Error: " + data.message);
    }
  } catch (err) {
    console.error(err);
    alert("Failed to register. Check backend connection.");
  }
}

// Login user
async function loginUser(event) {
  event.preventDefault();

  const studentId = document.getElementById('studentId').value;
  const password = document.getElementById('password').value;

  try {
    const response = await fetch('http://localhost:3000/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ studentId, password })
    });

    const data = await response.json();
    if (response.ok) {
      alert("Login successful!");
      if (data.role === "student") {
        window.location.href = "rentals.html";
      } else {
        window.location.href = "admin.html";
      }
    } else {
      alert("Error: " + data.message);
    }
  } catch (err) {
    console.error(err);
    alert("Failed to login. Check backend connection.");
  }
}
