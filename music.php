<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Music</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap');

        body {
            font-family: 'MedievalSharp', cursive;
        }
        /* Custom scrollbar for sidebar */
        aside::-webkit-scrollbar {
            width: 6px;
        }
        aside::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 3px;
        }
        /* Sidebar styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: width 0.3s ease;
            z-index: 50;
            overflow-x: hidden;
        }
        .sidebar:hover {
            width: 250px;
        }
        .sidebar:hover ~ main {
            margin-left: 250px;
        }
        .nav-links {
            display: flex;
            flex-direction: column;
            padding-top: 100px;
            height: calc(100vh - 100px);
            position: relative;
        }
        .nav-links a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #4a3a6b;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-family: Arial, sans-serif;
        }
        .nav-links a:last-child {
            margin-top: auto;
            border-top: 1px solid rgba(74, 58, 107, 0.1);
        }
        .nav-links a span {
            opacity: 0;
            transition: opacity 0.3s ease;
            margin-left: 15px;
        }
        .sidebar:hover .nav-links a span {
            opacity: 1;
        }
        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.3);
            color: #2d1f4a;
        }
        .nav-links a i {
            min-width: 30px;
            font-size: 20px;
            text-align: center;
        }
        /* Logo styles */
        .logo {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            white-space: nowrap;
            font-family: Arial, sans-serif;
        }
        .logo span:last-child {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar:hover .logo span:last-child {
            opacity: 1;
        }
        /* Music card styles */
        .music-card {
            background: rgba(240, 217, 255, 0.35);
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
            width: 100%;
            margin-left: 0;
        }
        .music-card:hover {
            background: rgba(240, 217, 255, 0.5);
            transform: translateY(-5px);
        }
        /* Main content transition */
        .main-content {
            transition: margin-left 0.3s ease;
            margin-left: 5rem;
        }
        .sidebar:hover ~ .main-content {
            margin-left: 16rem;
        }
        /* Music player styles */
        .music-player {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-family: 'Quicksand', sans-serif;
        }
        .music-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        .music-controls button {
            background: #7a5f8a;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .music-controls button.next-btn {
            background: #5a3f8a;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            padding: 0;
            font-size: 0.9rem;
        }
        .music-controls button:hover {
            background: #5a3f6a;
            transform: scale(1.1);
        }
        .music-controls button.shuffle-btn {
            background: #5a3f8a;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            padding: 0;
            font-size: 0.9rem;
        }
        .music-controls button.shuffle-btn.active {
            background: #4a2f5a;
            box-shadow: 0 0 10px rgba(90, 63, 106, 0.5);
        }
        @media (max-width: 640px) {
            .music-controls {
                gap: 10px;
            }
            .music-controls button {
                width: 35px;
                height: 35px;
            }
            .music-controls button.next-btn,
            .music-controls button.shuffle-btn {
                width: 35px;
                height: 35px;
                padding: 0;
            }
        }
        .music-info {
            text-align: center;
            margin-bottom: 15px;
            font-family: 'Quicksand', sans-serif;
        }
        .music-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #4a3a6b;
            margin-bottom: 5px;
            font-family: 'Quicksand', sans-serif;
            letter-spacing: 0.5px;
        }
        .music-artist {
            font-size: 1rem;
            color: #666;
            font-family: 'Quicksand', sans-serif;
            font-weight: 500;
        }
        .progress-bar {
            width: 100%;
            height: 5px;
            background: #ddd;
            border-radius: 5px;
            margin: 10px 0;
            cursor: pointer;
        }
        .progress {
            height: 100%;
            background: #7a5f8a;
            border-radius: 5px;
            width: 0%;
        }
        .time-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #666;
            font-family: 'Quicksand', sans-serif;
            font-weight: 500;
        }
        h1.text-3xl {
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            letter-spacing: 1px;
        }
    </style>
</head>
<body class="bg-gradient-to-r from-[#7f8dbd] to-[#7a5f8a]">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <span class="text-green-500 font-bold text-lg">M</span>
                <span class="text-blue-400 font-bold text-lg">R</span>
                <span class="text-gray-700 font-bold text-lg">R</span>
                <span class="text-gray-600 font-bold text-lg">B</span>
                <span class="text-3xl font-extrabold text-[#1a1a1a] ml-2 tracking-widest">LIBRARY</span>
            </div>
            <div class="nav-links">
                <a href="storybook.php"><i class="fas fa-book"></i><span>Storybook</span></a>
                <a href="alphabet.php"><i class="fas fa-font"></i><span>Alphabet</span></a>
                <a href="numbers.php"><i class="fas fa-calculator"></i><span>Numbers</span></a>
                <a href="riddles.php"><i class="fas fa-puzzle-piece"></i><span>Scoreboard</span></a>
                <a href="quizzes.php"><i class="fas fa-question-circle"></i><span>Quizzes</span></a>
                <a href="music.php"><i class="fas fa-music"></i><span>Music</span></a>
                <a href="logout.php" style="margin-top: 10px;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </aside>

        <!-- Main content -->
        <main class="main-content flex-1 p-8">
            <h1 class="text-3xl font-bold text-white mb-8 text-center">Music Player</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Music Card 1 -->
                <div class="music-card rounded-3xl p-6">
                    <div class="music-player">
                        <div class="music-info">
                            <div class="music-title">ABC Song</div>
                            <div class="music-artist">Children's Songs</div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress" id="progress1"></div>
                        </div>
                        <div class="time-info">
                            <span id="current-time1">0:00</span>
                            <span id="duration1">0:00</span>
                        </div>
                        <div class="music-controls">
                            <button onclick="playPause('audio1', 'playPauseBtn1')" id="playPauseBtn1">
                                <i class="fas fa-play"></i>
                            </button>
                            <button onclick="stopAudio('audio1', 'playPauseBtn1')">
                                <i class="fas fa-stop"></i>
                            </button>
                            <button class="next-btn" onclick="playNext('audio1', 'playPauseBtn1')">
                                <i class="fas fa-forward"></i>
                            </button>
                            <button class="shuffle-btn" onclick="toggleShuffle()" id="shuffleBtn">
                                <i class="fas fa-random"></i>
                            </button>
                        </div>
                        <audio id="audio1" src="music/song.mp3.mp3" ontimeupdate="updateProgress('audio1', 'progress1', 'current-time1')" onloadedmetadata="updateDuration('audio1', 'duration1')"></audio>
                    </div>
                </div>

                <!-- Music Card 2 -->
                <div class="music-card rounded-3xl p-6">
                    <div class="music-player">
                        <div class="music-info">
                            <div class="music-title">Numbers Song</div>
                            <div class="music-artist">Children's Songs</div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress" id="progress2"></div>
                        </div>
                        <div class="time-info">
                            <span id="current-time2">0:00</span>
                            <span id="duration2">0:00</span>
                        </div>
                        <div class="music-controls">
                            <button onclick="playPause('audio2', 'playPauseBtn2')" id="playPauseBtn2">
                                <i class="fas fa-play"></i>
                            </button>
                            <button onclick="stopAudio('audio2', 'playPauseBtn2')">
                                <i class="fas fa-stop"></i>
                            </button>
                            <button class="next-btn" onclick="playNext('audio2', 'playPauseBtn2')">
                                <i class="fas fa-forward"></i>
                            </button>
                            <button class="shuffle-btn" onclick="toggleShuffle()" id="shuffleBtn">
                                <i class="fas fa-random"></i>
                            </button>
                        </div>
                        <audio id="audio2" src="music/numbermp3.mp3" ontimeupdate="updateProgress('audio2', 'progress2', 'current-time2')" onloadedmetadata="updateDuration('audio2', 'duration2')"></audio>
                    </div>
                </div>

                <!-- Music Card 3 -->
                <div class="music-card rounded-3xl p-6">
                    <div class="music-player">
                        <div class="music-info">
                            <div class="music-title">Twinkle Twinkle Little Star</div>
                            <div class="music-artist">Children's Songs</div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress" id="progress3"></div>
                        </div>
                        <div class="time-info">
                            <span id="current-time3">0:00</span>
                            <span id="duration3">0:00</span>
                        </div>
                        <div class="music-controls">
                            <button onclick="playPause('audio3', 'playPauseBtn3')" id="playPauseBtn3">
                                <i class="fas fa-play"></i>
                            </button>
                            <button onclick="stopAudio('audio3', 'playPauseBtn3')">
                                <i class="fas fa-stop"></i>
                            </button>
                            <button class="next-btn" onclick="playNext('audio3', 'playPauseBtn3')">
                                <i class="fas fa-forward"></i>
                            </button>
                            <button class="shuffle-btn" onclick="toggleShuffle()" id="shuffleBtn">
                                <i class="fas fa-random"></i>
                            </button>
                        </div>
                        <audio id="audio3" src="music/twinkle.mp3" ontimeupdate="updateProgress('audio3', 'progress3', 'current-time3')" onloadedmetadata="updateDuration('audio3', 'duration3')"></audio>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let isShuffleEnabled = false;
        let lastPlayedIndex = -1;

        function toggleShuffle() {
            const shuffleBtn = document.getElementById('shuffleBtn');
            isShuffleEnabled = !isShuffleEnabled;
            shuffleBtn.classList.toggle('active');
        }

        function getRandomIndex(max, exclude) {
            let randomIndex;
            do {
                randomIndex = Math.floor(Math.random() * max);
            } while (randomIndex === exclude && max > 1);
            return randomIndex;
        }

        function playNext(currentAudioId, currentButtonId) {
            const currentAudio = document.getElementById(currentAudioId);
            const currentButton = document.getElementById(currentButtonId);
            const allAudios = document.querySelectorAll('audio');
            const currentIndex = Array.from(allAudios).findIndex(audio => audio.id === currentAudioId);
            
            // Stop current audio
            currentAudio.pause();
            currentAudio.currentTime = 0;
            currentButton.innerHTML = '<i class="fas fa-play"></i>';
            
            // Determine next index based on shuffle mode
            let nextIndex;
            if (isShuffleEnabled) {
                nextIndex = getRandomIndex(allAudios.length, lastPlayedIndex);
            } else {
                nextIndex = (currentIndex + 1) % allAudios.length;
            }
            
            lastPlayedIndex = nextIndex;
            const nextAudio = allAudios[nextIndex];
            const nextButton = document.getElementById(nextAudio.id.replace('audio', 'playPauseBtn'));
            
            nextAudio.play();
            nextButton.innerHTML = '<i class="fas fa-pause"></i>';
        }

        function playPause(audioId, buttonId) {
            const audio = document.getElementById(audioId);
            const button = document.getElementById(buttonId);
            
            if (audio.paused) {
                // Stop all other audio
                document.querySelectorAll('audio').forEach(a => {
                    if (a.id !== audioId) {
                        a.pause();
                        a.currentTime = 0;
                        const otherButton = document.getElementById(a.id.replace('audio', 'playPauseBtn'));
                        if (otherButton) {
                            otherButton.innerHTML = '<i class="fas fa-play"></i>';
                        }
                    }
                });
                
                audio.play();
                button.innerHTML = '<i class="fas fa-pause"></i>';
            } else {
                audio.pause();
                button.innerHTML = '<i class="fas fa-play"></i>';
            }
        }

        function stopAudio(audioId, buttonId) {
            const audio = document.getElementById(audioId);
            const button = document.getElementById(buttonId);
            
            audio.pause();
            audio.currentTime = 0;
            button.innerHTML = '<i class="fas fa-play"></i>';
        }

        function updateProgress(audioId, progressId, timeId) {
            const audio = document.getElementById(audioId);
            const progress = document.getElementById(progressId);
            const timeDisplay = document.getElementById(timeId);
            
            const percent = (audio.currentTime / audio.duration) * 100;
            progress.style.width = percent + '%';
            
            const minutes = Math.floor(audio.currentTime / 60);
            const seconds = Math.floor(audio.currentTime % 60);
            timeDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        function updateDuration(audioId, durationId) {
            const audio = document.getElementById(audioId);
            const durationDisplay = document.getElementById(durationId);
            
            const minutes = Math.floor(audio.duration / 60);
            const seconds = Math.floor(audio.duration % 60);
            durationDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        // Add click event to progress bars
        document.querySelectorAll('.progress-bar').forEach(bar => {
            bar.addEventListener('click', function(e) {
                const audioId = this.parentElement.querySelector('audio').id;
                const audio = document.getElementById(audioId);
                const rect = this.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                audio.currentTime = pos * audio.duration;
            });
        });
    </script>
</body>
</html>