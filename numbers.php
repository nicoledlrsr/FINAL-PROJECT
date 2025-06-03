<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Numbers with Childlike Voice</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
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
        /* Main content adjustment */
        main {
            margin-left: 80px;
            transition: margin-left 0.3s ease;
        }
        /* Card styles */
        .number-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
            height: 30vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .number-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .number-text {
            font-family: 'Comic Sans MS', cursive;
            font-size: 8rem;
            font-weight: bold;
            color: #4a3a6b;
            text-shadow: 
                3px 3px 0 #fff,
                -3px -3px 0 #fff,
                3px -3px 0 #fff,
                -3px 3px 0 #fff;
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
            animation: dance 2s infinite;
        }

        @keyframes dance {
            0%, 100% {
                transform: rotate(-5deg) translateY(0);
            }
            25% {
                transform: rotate(5deg) translateY(-10px);
            }
            50% {
                transform: rotate(-5deg) translateY(0);
            }
            75% {
                transform: rotate(5deg) translateY(-10px);
            }
        }

        .number-card:hover .number-text {
            animation: dance 1s infinite;
        }
        body {
            background: linear-gradient(135deg, 
                #a084ca 0%,
                #b69ad8 25%,
                #c7b0e2 50%,
                #d7b9d5 75%,
                #e5c8e0 100%
            );
            background-attachment: fixed;
        }
    </style>
</head>
<body class="min-h-screen flex">
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
            <a href="logout.php" style="margin-top: auto;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-4 overflow-auto w-full">
        <section class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-8 px-8">
            <!-- Buttons 1 to 20 -->
            <button aria-label="Play sound for number 1" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('1')">
                <span class="number-text">1</span>
            </button>
            <button aria-label="Play sound for number 2" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('2')">
                <span class="number-text">2</span>
            </button>
            <button aria-label="Play sound for number 3" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('3')">
                <span class="number-text">3</span>
            </button>
            <button aria-label="Play sound for number 4" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('4')">
                <span class="number-text">4</span>
            </button>
            <button aria-label="Play sound for number 5" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('5')">
                <span class="number-text">5</span>
            </button>
            <button aria-label="Play sound for number 6" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('6')">
                <span class="number-text">6</span>
            </button>
            <button aria-label="Play sound for number 7" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('7')">
                <span class="number-text">7</span>
            </button>
            <button aria-label="Play sound for number 8" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('8')">
                <span class="number-text">8</span>
            </button>
            <button aria-label="Play sound for number 9" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('9')">
                <span class="number-text">9</span>
            </button>
            <button aria-label="Play sound for number 10" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('10')">
                <span class="number-text">10</span>
            </button>
            <!-- New buttons for numbers 11 to 20 -->
            <button aria-label="Play sound for number 11" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('11')">
                <span class="number-text">11</span>
            </button>
            <button aria-label="Play sound for number 12" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('12')">
                <span class="number-text">12</span>
            </button>
            <button aria-label="Play sound for number 13" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('13')">
                <span class="number-text">13</span>
            </button>
            <button aria-label="Play sound for number 14" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('14')">
                <span class="number-text">14</span>
            </button>
            <button aria-label="Play sound for number 15" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('15')">
                <span class="number-text">15</span>
            </button>
            <button aria-label="Play sound for number 16" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('16')">
                <span class="number-text">16</span>
            </button>
            <button aria-label="Play sound for number 17" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('17')">
                <span class="number-text">17</span>
            </button>
            <button aria-label="Play sound for number 18" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('18')">
                <span class="number-text">18</span>
            </button>
            <button aria-label="Play sound for number 19" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('19')">
                <span class="number-text">19</span>
            </button>
            <button aria-label="Play sound for number 20" class="number-card bg-[#f4d9ff]/50 rounded-3xl select-none focus:outline-none focus:ring-4 focus:ring-[#7c5aa3]" onclick="playSound('20')">
                <span class="number-text">20</span>
            </button>
        </section>
    </main>
    <audio id="numberSong" style="display: none;">
        <source src="music/numbermp3.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <script>
        // Use Web Speech API to speak the number name with a baby-like voice
        let selectedVoice = null;
        const numberAudio = document.getElementById('numberSong');

        // Add event listeners for audio debugging
        numberAudio.addEventListener('error', function(e) {
            console.error('Audio Error:', e);
            alert('Error loading audio. Please check if the file exists at: music/numbermp3.mp3');
        });

        numberAudio.addEventListener('loadeddata', function() {
            console.log('Audio loaded successfully');
        });

        function setVoice() {
            const voices = window.speechSynthesis.getVoices();
            // Try to find a voice that sounds like Siri
            selectedVoice = voices.find(v =>
                v.lang === 'en-US' &&
                (v.name.toLowerCase().includes('samantha') || 
                    v.name.toLowerCase().includes('karen') || 
                    v.name.toLowerCase().includes('alex') ||
                    v.name.toLowerCase().includes('daniel') ||
                    v.name.toLowerCase().includes('victoria') ||
                    v.name.toLowerCase().includes('siri') ||
                    v.name.toLowerCase().includes('google') ||
                    v.name.toLowerCase().includes('microsoft'))
            );
            if (!selectedVoice) {
                // fallback: any female US English voice
                selectedVoice = voices.find(v => 
                    v.lang === 'en-US' && 
                    (v.name.toLowerCase().includes('female') || v.name.toLowerCase().includes('woman'))
                ) || voices.find(v => v.lang.startsWith('en'));
            }
        }

        if (typeof speechSynthesis !== 'undefined' && speechSynthesis.onvoiceschanged !== undefined) {
            speechSynthesis.onvoiceschanged = setVoice;
        } else {
            setVoice();
        }

        function playNumberSong() {
            // Play the audio directly
            try {
                // Reset and play
                numberAudio.currentTime = 0;
                const playPromise = numberAudio.play();
                
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                        console.log('Audio playback started successfully');
                    }).catch(error => {
                        console.error('Error playing audio:', error);
                        // If autoplay is blocked, show a play button
                        const playButton = document.createElement('button');
                        playButton.innerHTML = 'Play Numbers Song';
                        playButton.className = 'fixed bottom-4 right-4 bg-purple-600 text-white px-4 py-2 rounded-full shadow-lg hover:bg-purple-700 transition-colors';
                        playButton.onclick = () => {
                            numberAudio.play().then(() => {
                                console.log('Audio started after button click');
                                playButton.remove();
                            }).catch(err => {
                                console.error('Error after button click:', err);
                                alert('Error playing audio. Please check if the file exists at: music/numbermp3.mp3');
                            });
                        };
                        document.body.appendChild(playButton);
                    });
                }
            } catch (error) {
                console.error('Error with audio playback:', error);
                alert('Error playing audio. Please check if the file exists at: music/numbermp3.mp3');
            }
        }

        function playSound(number) {
            if (!window.speechSynthesis) {
                alert("Sorry, your browser does not support speech synthesis.");
                return;
            }
            window.speechSynthesis.cancel();
            numberAudio.pause();
            numberAudio.currentTime = 0;

            const utterance = new SpeechSynthesisUtterance(number);
            utterance.lang = 'en-US';
            utterance.rate = 0.9; // Slightly slower for clarity
            utterance.pitch = 1.1; // Slightly higher pitch for Siri-like tone
            utterance.volume = 1.0;
            if (selectedVoice) {
                utterance.voice = selectedVoice;
            }
            window.speechSynthesis.speak(utterance);

            // If number 10 was clicked, play the numbers song after a short delay
            if (number === '10') {
                setTimeout(playNumberSong, 1000); // Wait 1 second after saying '10' before starting the song
            }
        }
    </script>
</body>
</html>