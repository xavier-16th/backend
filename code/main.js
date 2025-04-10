
// 1. Dark Mode Toggle
// 2. Tab Switching
// 3. Search Through Artist Cards
// 4. Favorite Artist Selection
// 5. Login Modal Controls
// 6. Login Form Validation
// 7. Real-Time Email Input Validation


// Dark Mode Toggle

function myFunction() {
    document.body.classList.toggle("dark-mode");
}



// Tab Switching
function showContent(tabId) {
    document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));
    document.querySelectorAll(".content").forEach(content => content.style.display = "none");
    const activeTab = document.querySelector(".tab[data-tab=" + tabId + "]");
    if (activeTab) activeTab.classList.add("active");
    const activeContent = document.getElementById(tabId);
    if (activeContent) activeContent.style.display = "block";
}



// Page Load Initialization
document.addEventListener("DOMContentLoaded", () => {
    showContent("all");


    // Search Through Artist Cards
    document.querySelector(".circle-search input").addEventListener("input", function () {
        let input = this;
        let filter = input.value.toUpperCase();
        let cards = document.querySelectorAll(".artist-card");

        cards.forEach(card => {
            let nameElement = card.querySelector(".artist-name");
            let txtValue = nameElement.textContent || nameElement.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    });


    // Favorite Artist Selection

    document.querySelectorAll(".artist-card").forEach(card => {
        let checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.classList.add("favorite-checkbox");
        card.appendChild(checkbox);

        checkbox.addEventListener("change", function () {
            let favoritesContainer = document.querySelector("#favorite .card-container");
            let artistName = card.querySelector(".artist-name").textContent;

            if (this.checked) {
                let clonedCard = card.cloneNode(true);
                clonedCard.querySelector(".favorite-checkbox").remove();
                let alreadyAdded = Array.from(favoritesContainer.querySelectorAll(".artist-card")).some(favCard =>
                    favCard.querySelector(".artist-name").textContent === artistName);
                if (!alreadyAdded) favoritesContainer.appendChild(clonedCard);
            } else {
                document.querySelectorAll("#favorite .artist-card").forEach(favCard => {
                    if (favCard.querySelector(".artist-name").textContent === artistName) favCard.remove();
                });
            }
        });
    });
});


// Login Modal Controls

function openLoginForm() {
    document.getElementById("loginModal").style.display = "block";
}

function closeLoginForm() {
    document.getElementById("loginModal").style.display = "none";
}

window.onclick = function(event) {
    if (event.target == document.getElementById("loginModal")) closeLoginForm();
}



// Login Form Validation
document.getElementById("loginForm").addEventListener("submit", function (event) {
    event.preventDefault();
    var username = document.getElementById("username").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!emailPattern.test(email)) {
        alert("Please enter a valid email address.");
        return;
    }

    if (username && password) {
        var loginButton = document.getElementById("loginBtn");
        loginButton.textContent = "Logged In";
        loginButton.disabled = true;
        closeLoginForm();
    } else {
        alert("Username or password is incorrect");
    }
});

// ==========================
// Real-Time Email Input Validation
// ==========================
document.getElementById("email").addEventListener("input", function () {
    var emailInput = this.value;
    var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    this.style.border = emailPattern.test(emailInput) ? "2px solid green" : "2px solid red";
});
