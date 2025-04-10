<?php
require 'config.php'; // Include configuration file for Spotify credentials (client ID, secret, redirect URI, etc.)
session_start(); // Start the PHP session to store the access token

// Handle logout request by destroying the session
if (isset($_GET['logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to self (refresh page)
    exit();
}

// If no authorization code is present, redirect to Spotify authorization page
if (!isset($_GET['code'])) {
    $auth_url = "https://accounts.spotify.com/authorize?" . http_build_query([
        "client_id" => SPOTIFY_CLIENT_ID,
        "response_type" => "code",
        "redirect_uri" => SPOTIFY_REDIRECT_URI,
        "scope" => "user-top-read playlist-modify-private playlist-modify-public" // Permissions
    ]);
    header("Location: $auth_url"); // Redirect user to Spotify login/consent page
    exit();
}

// Handle Spotify callback with authorization code
$code = $_GET['code'];
$token_url = "https://accounts.spotify.com/api/token";

// Create request headers for access token exchange
$headers = [
    "Authorization: Basic " . base64_encode(SPOTIFY_CLIENT_ID . ":" . SPOTIFY_CLIENT_SECRET),
    "Content-Type: application/x-www-form-urlencoded"
];

// Prepare POST data to exchange authorization code for an access token
$post_fields = http_build_query([
    "grant_type" => "authorization_code",
    "code" => $code,
    "redirect_uri" => SPOTIFY_REDIRECT_URI
]);

// Send POST request to Spotify token endpoint
$ch = curl_init($token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// Parse JSON response to extract access token
$data = json_decode($response, true);
if (!isset($data['access_token'])) {
    die("Error: No access token returned.");
}

// Store access token in session for future API calls
$_SESSION['access_token'] = $data['access_token'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spotify Playlist</title>
    <style>

#fixed-iframe-container {
    position: absolute;
    bottom: -450px; /* now relative to the parent */
    left: 0;
    width: 100%;
    display: flex;
    justify-content: center;
}

    </style>
</head>
<body>

<!-- Spotify Playlist Embed Container Fixed at Bottom Center -->
<div id="fixed-iframe-container">
    <div id="iframe-container"></div>
</div>

<script>
    const token = '<?= $_SESSION['access_token'] ?>';

    async function fetchWebApi(endpoint, method, body) {
        const res = await fetch(`https://api.spotify.com/${endpoint}`, {
            headers: {
                Authorization: `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            method,
            body: body ? JSON.stringify(body) : null
        });
        return await res.json();
    }

    async function getTopTracks() {
        const data = await fetchWebApi('v1/me/top/tracks?time_range=long_term&limit=5', 'GET');
        return data.items;
    }

    async function createPlaylist(tracksUri) {
        const { id: user_id } = await fetchWebApi('v1/me', 'GET');
        const playlist = await fetchWebApi(`v1/users/${user_id}/playlists`, 'POST', {
            name: "My Top Tracks Playlist",
            description: "Generated from my top 5 tracks",
            public: false
        });
        await fetchWebApi(`v1/playlists/${playlist.id}/tracks?uris=${tracksUri.join(',')}`, 'POST');
        return playlist;
    }

    async function run() {
        const topTracks = await getTopTracks();
        const uris = topTracks.map(t => t.uri);
        const playlist = await createPlaylist(uris);

        document.getElementById('iframe-container').innerHTML = `
    <iframe
        title="Spotify Embed: Playlist"
        src="https://open.spotify.com/embed/playlist/${playlist.id}?utm_source=generator&theme=0"
        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
        loading="lazy"
        style="width: 800px; height: 380px; border: none; border-radius: 12px;"
    ></iframe>
`;

    }

    run();
</script>

<!-- Spotify Playlist Embed at Bottom of Page -->
<div id="iframe-container-wrapper">
    <div id="iframe-container"></div>
</div>


</body>
</html>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="code/spot.css"/>
    <title>Spotify home page </title>
    <meta name="description" content="spotify home page with artist cards and login feature . it also has a favorite tab to favorite your artist. aand a dark mode ">
</head> 
<body>

<!-- dark mode and login button -->
<div class="top-right">
    <button onclick="myFunction()">Dark Mode</button>
    <button id="loginBtn" onclick="openLoginForm()">Login</button>
</div>

<!-- Modal for Login Form -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <h2>Login</h2>
        <form id="loginForm">

            <div>
                <label for="username">Username</label>
                <input type="text" name="username" id="username" />
            </div>

            <div>
                <label for="email">Email</label>  
                <input type="email" name="email" id="email" />
            </div>            

            

            <div>
                <label for="password">Password</label>
                <input type="password" id="password" />
            </div>

            <div>
                <label for="age">Age</label>
                <input type="number" name="age" min="1" max="100" />
            </div>

            <div>
                <label for="datebirth">Date of Birth</label>
                <input type="date" id="datebirth" />
            </div>

            <div>
                <input type="checkbox" name="subscription" id="subscription" />
                <label for="subscription">
                    <a href="#" style="text-decoration: underline;">terms and conditions</a>
                </label>
                
            </div>

            <div>
                <button type="submit" id="loginBtn">log in </button>
            </div>
        </form>
    </div>
</div>


<img src="code/spotify.png" class="avatar">

<!-- Side nav -->
<div class="sidenav">
    <a href="#">About</a>
    <a href="#">Services</a>
    <a href="#">Clients</a>
    <a href="#">Contact</a>
    <a href="playlist.html">playlist</a> 
    
</div>

<!-- Page content -->
<div class="main"></div>
<div class="circle-search">
    <input type="text" placeholder="Search" />
</div>

<!-- Tabs section centered under the top of the screen -->
<div class="tabs-wrapper">
    <div class="tabs">
        <button class="tab active" onclick="showContent('all')">All</button>
        <button class="tab" onclick="showContent('music')">Music</button>
        <button class="tab" onclick="showContent('podcasts')">Podcasts</button>
        <button class="tab" onclick="showContent('favorite')">favorite </button>
        
    </div>
</div>

<!-- Content id all  -->
<div class="content" id="all">
    <!-- Artist Cards for All Tab  -->
    <div class="card-container">
        <!-- Artist Card 1: Travis Scott -->
        <div class="artist-card">
            
            <img src="code/travis  copy.jpeg" alt="Travis Scott's Photo" class="artist-photo">
            <div class="artist-info">
                <h2 class="artist-name">Travis Scott</h2>
                <p class="artist-description">Travis Scott is an American rapper, singer, and record producer known for his heavily auto-tuned voice and his impact on the sound of modern hip-hop.</p>
                <div class="albums">
                    <h3>Albums 2024 :</h3>
                    <ul>
                        <li>Days Before Rodeo</li>
                    </ul>
                </div>
                <div class="songs">
                    <h3>Songs Released in 2024:</h3>
                    <ul>
                        <li>Parking Lot</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Artist Card 2: Playboi Carti -->
        <div class="artist-card">
            
            <img src="code/iamliar copy.jpg" alt="Playboi Carti's Photo" class="artist-photo">
            <div class="artist-info">
                <h2 class="artist-name">Playboi Carti</h2>
                <p class="artist-description">Playboi Carti is an American rapper, singer, and songwriter. He is known for his unique delivery, high-energy beats, and punk-inspired aesthetic.</p>
                <div class="albums">
                    <h3>Albums 2024:</h3>
                    <ul>
                        <li>0</li>
                    </ul>
                </div>
                <div class="songs">
                    <h3>Songs Released in 2024:</h3>
                    <ul>
                        <li>Whole Lotta Red</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Artist Card 3: Chief Keef -->
        <div class="artist-card">
            
            <img src="code/keef.jpeg" alt="Chief Keef's Photo" class="artist-photo">
            <div class="artist-info">
                <h2 class="artist-name">Chief Keef</h2>
                <p class="artist-description">Chief Keef, born Keith Cozart, is a Chicago yapper pioneer. Known for his creativity and loyalty, he inspires others while staying focused on his art and lasting legacy.</p>
                <div class="albums">
                    <h3>Podcasts 2024 :</h3>
                    <ul>
                        <li>Almighty So</li>
                    </ul>
                </div>
                <div class="songs">
                    <h3>Podcasts Released in 2024:</h3>
                    <ul>
                        <li>Faneto</li>
                        <li>Sosa</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Artist Card 4: King Von -->
        <div class="artist-card">
           
            <img src="code/von.jpeg" alt="King Von's Photo" class="artist-photo">
            <div class="artist-info">
                <h2 class="artist-name">King Von</h2>
                <p class="artist-description">King Von, born Dayvon Bennett, was a Chicago yapper known for his storytelling, loyalty, and generosity. Loved for his humor and energy, he was driven to uplift others and create a better future for those around him.</p>
                <div class="albums">
                    <h3>Podcasts 2024:</h3>
                    <ul>
                        <li>0</li>
                    </ul>
                </div>
                <div class="songs">
                    <h3>Podcasts Released in 2024:</h3>
                    <ul>
                        <li>0</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content id music  -->
<div class="content" id="music" style="display: none;">
    <!-- Artist Cards for Music Tab -->
    <div class="card-container">
        <!-- Travis Scott -->
        <div class="artist-card">
            <img src="code/travis  copy.jpeg" alt="Travis Scott's Photo" class="artist-photo">
            <div class="artist-info">
                <h2 class="artist-name">Travis Scott</h2>
                <p class="artist-description">Travis Scott is an American rapper, singer, and record producer known for his heavily auto-tuned voice and his impact on the sound of modern hip-hop.</p>
                <div class="albums">
                    <h3>Albums 2024 :</h3>
                    <ul>
                        <li>Days Before Rodeo</li>
                    </ul>
                </div>
                <div class="songs">
                    <h3>Songs Released in 2024:</h3>
                    <ul>
                        <li>Parking Lot</li>
                    </ul>
                </div>
            </div>
        </div>

        <!--  Playboi Carti -->
        <div class="artist-card">
            <img src="code/iamliar copy.jpg" alt="Playboi Carti's Photo" class="artist-photo">
            <div class="artist-info">
                <h2 class="artist-name">Playboi Carti</h2>
                <p class="artist-description">Playboi Carti is an American rapper, singer, and songwriter. He is known for his unique delivery, high-energy beats, and punk-inspired aesthetic.</p>
                <div class="albums">
                    <h3>Albums 2024:</h3>
                    <ul>
                        <li>0</li>
                    </ul>
                </div>
                <div class="songs">
                    <h3>Songs Released in 2024:</h3>
                    <ul>
                        <li>Whole Lotta Red</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content id podcasts  -->
<div class="content" id="podcasts" style="display: none;">
    <!-- Artist Cards for Podcasts Tab -->
    <div class="card-container">
        <!--  Chief Keef -->
        <div class="artist-card">
            <img src="code/keef.jpeg" alt="Chief Keef's Photo" class="artist-photo">
            <div class="artist-info">
                <h2 class="artist-name">Chief Keef</h2>
                <p class="artist-description">Chief Keef, born Keith Cozart, is a Chicago yapper pioneer. Known for his creativity and loyalty, he inspires others while staying focused on his art and lasting legacy.</p>
                <div class="albums">
                    <h3>Podcasts 2024 :</h3>
                    <ul>
                        <li>Almighty So</li>
                    </ul>
                </div>
                <div class="songs">
                    <h3>Podcasts Released in 2024:</h3>
                    <ul>
                        <li>Faneto</li>
                        <li>Sosa</li>
                    </ul>
                </div>
            </div>
        </div>

        <!--  King Von -->
        <div class="artist-card">
            <img src="code/von.jpeg" alt="King Von's Photo" class="artist-photo">
            <div class="artist-info">
                <h2 class="artist-name">King Von</h2>
                <p class="artist-description">King Von, born Dayvon Bennett, was a Chicago yapper known for his storytelling, loyalty, and generosity. Loved for his humor and energy, he was driven to uplift others and create a better future for those around him.</p>
                <div class="albums">
                    <h3>Podcasts 2024:</h3>
                    <ul>
                        <li>0</li>
                    </ul>
                </div>
                <div class="songs">
                    <h3>Podcasts Released in 2024:</h3>
                    <ul>
                        <li>0</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Favorites Tab -->
<div class="content" id="favorite" style="display: none;">
    <div class="card-container"></div> <!--  holds favorite artists -->
</div>




</div>

<script src="code/main.js"></script>
</body>
</html>