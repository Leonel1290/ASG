<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Fútbol - Penales</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: white;
        }
        
        .container {
            max-width: 900px;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        
        h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            color: #fff;
        }
        
        .subtitle {
            font-size: 1.2rem;
            margin-bottom: 25px;
            color: #ddd;
        }
        
        .game-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }
        
        .field-container {
            position: relative;
            width: 100%;
            max-width: 700px;
            height: 400px;
            background: #2e7d32;
            border: 5px solid #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .field {
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, #2e7d32, #4caf50);
        }
        
        .field-lines {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 3px solid white;
            border-radius: 5px;
        }
        
        .center-circle {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border: 3px solid white;
            border-radius: 50%;
        }
        
        .center-spot {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 15px;
            height: 15px;
            background: white;
            border-radius: 50%;
        }
        
        .goal {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 100px;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .goal-left {
            left: 0;
            border-right: 3px solid #2e7d32;
        }
        
        .goal-right {
            right: 0;
            border-left: 3px solid #2e7d32;
        }
        
        .goal-net {
            position: absolute;
            width: 50px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            top: 50%;
            transform: translateY(-50%);
        }
        
        .goal-net-left {
            left: 0;
            border-right: 2px dashed rgba(255, 255, 255, 0.5);
        }
        
        .goal-net-right {
            right: 0;
            border-left: 2px dashed rgba(255, 255, 255, 0.5);
        }
        
        .penalty-spot {
            position: absolute;
            width: 15px;
            height: 15px;
            background: white;
            border-radius: 50%;
            top: 50%;
        }
        
        .penalty-spot-left {
            left: 70px;
            transform: translateY(-50%);
        }
        
        .penalty-spot-right {
            right: 70px;
            transform: translateY(-50%);
        }
        
        .player {
            position: absolute;
            width: 40px;
            height: 60px;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            transition: all 0.3s ease;
        }
        
        .player-body {
            position: absolute;
            width: 40px;
            height: 40px;
            background: #1e88e5;
            border-radius: 50% 50% 40% 40%;
            bottom: 0;
        }
        
        .player-legs {
            position: absolute;
            width: 30px;
            height: 25px;
            background: #1565c0;
            bottom: -15px;
            left: 5px;
            border-radius: 0 0 10px 10px;
        }
        
        .ball {
            position: absolute;
            width: 25px;
            height: 25px;
            background: white;
            border-radius: 50%;
            bottom: 55px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 5;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .goalkeeper {
            position: absolute;
            width: 40px;
            height: 60px;
            top: 50%;
            right: 90px;
            transform: translateY(-50%);
            z-index: 10;
            transition: all 0.4s ease;
        }
        
        .goalkeeper-body {
            position: absolute;
            width: 40px;
            height: 40px;
            background: #e53935;
            border-radius: 50% 50% 40% 40%;
            bottom: 0;
        }
        
        .goalkeeper-arms {
            position: absolute;
            width: 50px;
            height: 15px;
            background: #c62828;
            bottom: 25px;
            left: -5px;
            border-radius: 10px;
        }
        
        .controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        
        .direction-btn {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            border-radius: 50%;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.2s ease;
        }
        
        .direction-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        .shoot-btn {
            padding: 15px 40px;
            background: linear-gradient(to right, #f44336, #e53935);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 1.3rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(229, 57, 53, 0.4);
            transition: all 0.3s ease;
        }
        
        .shoot-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(229, 57, 53, 0.6);
        }
        
        .shoot-btn:active {
            transform: translateY(1px);
        }
        
        .score-board {
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 10px;
        }
        
        .score {
            font-size: 2.5rem;
            font-weight: bold;
        }
        
        .player-score {
            color: #1e88e5;
        }
        
        .goalkeeper-score {
            color: #e53935;
        }
        
        .message {
            font-size: 1.5rem;
            margin: 15px 0;
            min-height: 40px;
            font-weight: bold;
        }
        
        .goal-animation {
            animation: goalFlash 1s ease;
        }
        
        @keyframes goalFlash {
            0% { background-color: #2e7d32; }
            50% { background-color: #4caf50; }
            100% { background-color: #2e7d32; }
        }
        
        .save-animation {
            animation: saveFlash 1s ease;
        }
        
        @keyframes saveFlash {
            0% { background-color: #2e7d32; }
            50% { background-color: #ff9800; }
            100% { background-color: #2e7d32; }
        }
        
        .instructions {
            margin-top: 25px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            text-align: left;
        }
        
        .instructions h3 {
            margin-bottom: 10px;
            color: #ffcc00;
        }
        
        .instructions ul {
            list-style-type: none;
            padding-left: 10px;
        }
        
        .instructions li {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        
        .instructions li:before {
            content: "⚽";
            margin-right: 10px;
        }
        
        @media (max-width: 600px) {
            h1 {
                font-size: 2rem;
            }
            
            .field-container {
                height: 300px;
            }
            
            .direction-btn {
                width: 60px;
                height: 60px;
                font-size: 1.2rem;
            }
            
            .shoot-btn {
                padding: 12px 30px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>JUEGO DE FÚTBOL</h1>
        <p class="subtitle">¡Anota goles pateando penales contra el portero!</p>
        
        <div class="game-area">
            <div class="score-board">
                <div class="player-score-container">
                    <div>JUGADOR</div>
                    <div class="score player-score">0</div>
                </div>
                <div class="vs">VS</div>
                <div class="goalkeeper-score-container">
                    <div>PORTERO</div>
                    <div class="score goalkeeper-score">0</div>
                </div>
            </div>
            
            <div class="message">¡Elige una dirección y patea!</div>
            
            <div class="field-container">
                <div class="field"></div>
                <div class="field-lines"></div>
                <div class="center-circle"></div>
                <div class="center-spot"></div>
                
                <div class="goal-net goal-net-left"></div>
                <div class="goal goal-left"></div>
                <div class="penalty-spot penalty-spot-left"></div>
                
                <div class="goal-net goal-net-right"></div>
                <div class="goal goal-right"></div>
                <div class="penalty-spot penalty-spot-right"></div>
                
                <div class="player">
                    <div class="player-body"></div>
                    <div class="player-legs"></div>
                </div>
                
                <div class="ball"></div>
                
                <div class="goalkeeper">
                    <div class="goalkeeper-body"></div>
                    <div class="goalkeeper-arms"></div>
                </div>
            </div>
            
            <div class="controls">
                <button class="direction-btn" data-direction="top-left">↖</button>
                <button class="direction-btn" data-direction="top">↑</button>
                <button class="direction-btn" data-direction="top-right">↗</button>
                <button class="direction-btn" data-direction="left">←</button>
                <button class="shoot-btn" id="shoot">PATEAR</button>
                <button class="direction-btn" data-direction="right">→</button>
                <button class="direction-btn" data-direction="bottom-left">↙</button>
                <button class="direction-btn" data-direction="bottom">↓</button>
                <button class="direction-btn" data-direction="bottom-right">↘</button>
            </div>
        </div>
        
        <div class="instructions">
            <h3>Instrucciones:</h3>
            <ul>
                <li>Selecciona una dirección con los botones de flechas</li>

                <li>Presiona el botón "PATEAR" para lanzar el penal</li>
                <li>El portero intentará adivinar tu dirección</li>
                <li>Anota 5 goles para ganar el juego</li>
            </ul>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos del juego
            const ball = document.querySelector('.ball');
            const goalkeeper = document.querySelector('.goalkeeper');
            const fieldContainer = document.querySelector('.field-container');
            const message = document.querySelector('.message');
            const playerScoreElement = document.querySelector('.player-score');
            const goalkeeperScoreElement = document.querySelector('.goalkeeper-score');
            const shootButton = document.getElementById('shoot');
            const directionButtons = document.querySelectorAll('.direction-btn');
            
            // Variables del juego
            let playerScore = 0;
            let goalkeeperScore = 0;
            let selectedDirection = null;
            let gameActive = true;
            
            // Posiciones posibles para el balón y el portero
            const positions = {
                'top-left': { ball: { top: '30%', left: '35%' }, keeper: { top: '25%', right: '85%' } },
                'top': { ball: { top: '25%', left: '50%' }, keeper: { top: '25%', right: '90%' } },
                'top-right': { ball: { top: '30%', left: '65%' }, keeper: { top: '25%', right: '95%' } },
                'left': { ball: { top: '45%', left: '35%' }, keeper: { top: '45%', right: '85%' } },
                'right': { ball: { top: '45%', left: '65%' }, keeper: { top: '45%', right: '95%' } },
                'bottom-left': { ball: { top: '60%', left: '35%' }, keeper: { top: '65%', right: '85%' } },
                'bottom': { ball: { top: '65%', left: '50%' }, keeper: { top: '65%', right: '90%' } },
                'bottom-right': { ball: { top: '60%', left: '65%' }, keeper: { top: '65%', right: '95%' } }
            };
            
            // Seleccionar dirección
            directionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (!gameActive) return;
                    
                    // Remover selección anterior
                    directionButtons.forEach(btn => btn.style.background = 'rgba(255, 255, 255, 0.2)');
                    
                    // Seleccionar nueva dirección
                    selectedDirection = this.getAttribute('data-direction');
                    this.style.background = 'rgba(30, 136, 229, 0.7)';
                    message.textContent = `Dirección seleccionada: ${getDirectionName(selectedDirection)}`;
                });
            });
            
            // Función para obtener nombre de dirección
            function getDirectionName(direction) {
                const names = {
                    'top-left': 'Arriba-Izquierda',
                    'top': 'Arriba-Centro',
                    'top-right': 'Arriba-Derecha',
                    'left': 'Centro-Izquierda',
                    'right': 'Centro-Derecha',
                    'bottom-left': 'Abajo-Izquierda',
                    'bottom': 'Abajo-Centro',
                    'bottom-right': 'Abajo-Derecha'
                };
                return names[direction] || direction;
            }
            
            // Patear el balón
            shootButton.addEventListener('click', function() {
                if (!gameActive || !selectedDirection) {
                    message.textContent = '¡Primero selecciona una dirección!';
                    return;
                }
                
                gameActive = false;
                shootButton.disabled = true;
                
                // Mover el balón a la posición seleccionada
                const ballPosition = positions[selectedDirection].ball;
                ball.style.top = ballPosition.top;
                ball.style.left = ballPosition.left;
                
                // El portero elige una dirección al azar
                const directions = Object.keys(positions);
                const goalkeeperDirection = directions[Math.floor(Math.random() * directions.length)];
                const keeperPosition = positions[goalkeeperDirection].keeper;
                
                // Mover el portero después de un breve retraso
                setTimeout(() => {
                    goalkeeper.style.top = keeperPosition.top;
                    goalkeeper.style.right = keeperPosition.right;
                    
                    // Verificar si el portero atajó el balón
                    setTimeout(() => {
                        if (selectedDirection === goalkeeperDirection) {
                            // Portero ataja
                            goalkeeperScore++;
                            goalkeeperScoreElement.textContent = goalkeeperScore;
                            fieldContainer.classList.add('save-animation');
                            message.textContent = '¡El portero atajó el penal!';
                            
                            setTimeout(() => {
                                fieldContainer.classList.remove('save-animation');
                            }, 1000);
                        } else {
                            // Gol
                            playerScore++;
                            playerScoreElement.textContent = playerScore;
                            fieldContainer.classList.add('goal-animation');
                            message.textContent = '¡GOOOOOOL!';
                            
                            setTimeout(() => {
                                fieldContainer.classList.remove('goal-animation');
                            }, 1000);
                        }
                        
                        // Verificar si el juego terminó
                        if (playerScore >= 5) {
                            message.textContent = '¡FELICIDADES! ¡GANASTE EL JUEGO!';
                            setTimeout(resetGame, 3000);
                        } else if (goalkeeperScore >= 5) {
                            message.textContent = '¡El portero ganó! ¡Mejor suerte la próxima!';
                            setTimeout(resetGame, 3000);
                        } else {
                            // Preparar siguiente tiro
                            setTimeout(resetShot, 2000);
                        }
                    }, 500);
                }, 300);
            });
            
            // Reiniciar para el siguiente tiro
            function resetShot() {
                // Volver a la posición inicial
                ball.style.top = 'auto';
                ball.style.bottom = '55px';
                ball.style.left = '50%';
                
                goalkeeper.style.top = '50%';
                goalkeeper.style.right = '90px';
                
                // Limpiar selección
                selectedDirection = null;
                directionButtons.forEach(btn => btn.style.background = 'rgba(255, 255, 255, 0.2)');
                
                // Reactivar juego
                gameActive = true;
                shootButton.disabled = false;
                message.textContent = '¡Elige una dirección y patea!';
            }
            
            // Reiniciar juego completo
            function resetGame() {
                playerScore = 0;
                goalkeeperScore = 0;
                playerScoreElement.textContent = playerScore;
                goalkeeperScoreElement.textContent = goalkeeperScore;
                resetShot();
            }
            
            // Mensaje inicial
            message.textContent = '¡Elige una dirección y patea!';
        });
    </script>
</body>
</html>