// Functionality:
// 1. Toggles dark mode.
// 2. Handles play/pause for audio, resetting other tracks.
// 3. Filters artist cards based on search input.
// 4. Controls carousel sliding automatically.
// 5. Shuffle, play/pause, and skip for audio tracks.


// Toggle dark mode
function myFunction() {
    var element = document.body;  // Get body element
    element.classList.toggle("dark-mode");  // Toggle dark mode
}


// Play/pause audio and reset other tracks
function toggleAudio(audioId) {
    var audioElement = document.getElementById(audioId);  // Get audio element
    if (audioElement.paused) {
        audioElement.play();  // Play audio
        document.querySelector(`#${audioId}`).previousElementSibling.textContent = "Pause Song"; // Update button text
    } else {
        audioElement.pause();  // Pause audio
        audioElement.currentTime = 0;  // Reset audio
        document.querySelector(`#${audioId}`).previousElementSibling.textContent = "Play Song"; // Update button text
    }

    // Reset other audio elements
    var audios = document.querySelectorAll("audio");
    audios.forEach(function(audio) {
        if (audio.id !== audioId) {
            audio.pause();
            audio.currentTime = 0;
        }
    });
}


// Filter artist cards based on search input
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");  // Get search input
    const artistCards = document.querySelectorAll(".artist-card");  // Get artist cards

    searchInput.addEventListener("input", function () {
        let searchText = searchInput.value.toLowerCase();  // Get lowercase search text

        artistCards.forEach(card => {
            let artistName = card.querySelector(".artist-name").textContent.toLowerCase();  // Get artist name

            // Show/hide cards based on match
            if (artistName.includes(searchText)) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    });
});


// Carousel sliding functionality
document.addEventListener("DOMContentLoaded", function () {
    const carouselContainer = document.querySelector(".carousel_items");  // Get carousel container
    const carouselItems = document.querySelectorAll(".carousel_item");  // Get carousel items
    const totalItems = carouselItems.length;  // Total items
    let index = 0;  // Starting index

    // Slide to next item
    function slideCarousel() {
        index++;  // Increment index
        if (index >= totalItems) {
            index = 0;  // Reset if at the end
        }
        carouselContainer.style.transition = "transform 0.5s ease-in-out";  // Transition effect
        carouselContainer.style.transform = `translateX(-${index * 100}%)`;  // Move container
    }

    setInterval(slideCarousel, 2500); // Automatic sliding every 2.5s
});


// Shuffle, play/pause, and skip functionality for audio
document.addEventListener("DOMContentLoaded", function () {
    const shuffleBtn = document.getElementById("shuffleBtn");  // Get shuffle button
    const playPauseBtn = document.getElementById("playPauseBtn");  // Get play/pause button
    const skipBtn = document.getElementById("skipBtn");  // Get skip button
    const audioElements = document.querySelectorAll("audio");  // Get audio elements
    let currentAudioIndex = -1; // No song playing initially

    // Stop and reset all audio tracks
    function stopAllAudio() {
        audioElements.forEach(audio => {
            audio.pause();  // Pause audio
            audio.currentTime = 0;  // Reset audio
        });
    }

    // Play specific song
    function playSong(index) {
        stopAllAudio();  // Stop any playing audio
        currentAudioIndex = index;  // Set current song
        let audio = audioElements[currentAudioIndex];
        audio.play();  // Play selected song
        playPauseBtn.classList.remove("pause");
        playPauseBtn.classList.add("play");  // Update button style
    }

    // Play random song
    function playRandomSong() {
        let randomIndex = Math.floor(Math.random() * audioElements.length);  // Get random index
        playSong(randomIndex);  // Play random song
    }

    // Toggle play/pause for current song
    function togglePlayPause() {
        if (currentAudioIndex === -1) {
            playRandomSong();  // If no song, play random
        } else {
            let currentAudio = audioElements[currentAudioIndex];
            if (currentAudio.paused) {
                currentAudio.play();  // Play audio
                playPauseBtn.classList.remove("pause");
                playPauseBtn.classList.add("play");
            } else {
                currentAudio.pause();  // Pause audio
                playPauseBtn.classList.remove("play");
                playPauseBtn.classList.add("pause");
            }
        }
    }

    // Skip to next song
    function skipSong() {
        let nextIndex = (currentAudioIndex + 1) % audioElements.length;  // Get next song index
        playSong(nextIndex);  // Play next song
    }

    // Set up event listeners for buttons
    shuffleBtn.addEventListener("click", playRandomSong);  // Shuffle on click
    playPauseBtn.addEventListener("click", togglePlayPause);  // Toggle play/pause on click
    skipBtn.addEventListener("click", skipSong);  // Skip on click
});
