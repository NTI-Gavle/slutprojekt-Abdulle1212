// Grand Aurora Hotel - JavaScript

document.addEventListener("DOMContentLoaded", () => {
    // Form validation for registration
    const registerForm = document.getElementById("registerForm")
    if (registerForm) {
      registerForm.addEventListener("submit", (e) => {
        const password = document.getElementById("password").value
        const confirmPassword = document.getElementById("confirm_password").value
  
        if (password !== confirmPassword) {
          e.preventDefault()
          alert("Passwords do not match!")
          return false
        }
  
        if (password.length < 6) {
          e.preventDefault()
          alert("Password must be at least 6 characters long!")
          return false
        }
      })
    }
  
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", function (e) {
        e.preventDefault()
        const target = document.querySelector(this.getAttribute("href"))
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          })
        }
      })
    })
  
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll(".alert")
    const bootstrap = window.bootstrap // Declare the bootstrap variable
    alerts.forEach((alert) => {
      setTimeout(() => {
        const bsAlert = new bootstrap.Alert(alert)
        bsAlert.close()
      }, 5000)
    })
  })
  