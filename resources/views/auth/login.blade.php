<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Yamaha Inventory</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    margin: 0;
    height: 100vh;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;

    background: linear-gradient(270deg, #0f2027, #203a43, #2c5364);
    background-size: 600% 600%;
    animation: gradientMove 12s ease infinite;
}

@keyframes gradientMove {
    0% {background-position: 0% 50%;}
    50% {background-position: 100% 50%;}
    100% {background-position: 0% 50%;}
}

#bubbleCanvas {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 0;
}

/* LOGIN BOX */
.login-box {
    position: relative;
    z-index: 2;
    background: rgba(255,255,255,0.95);
    padding: 40px;
    border-radius: 15px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
    backdrop-filter: blur(8px);
}

/* LOGO GLOW */
.logo {
    width: 80px;
    margin-bottom: 10px;
    animation: glow 2s infinite alternate;
}

@keyframes glow {
    from { filter: drop-shadow(0 0 5px #ff0000); }
    to { filter: drop-shadow(0 0 20px #ff4d4d); }
}

.title {
    font-weight: bold;
    margin-bottom: 20px;
}

/* BUTTON */
.btn-login {
    background: #2c5364;
    color: white;
    position: relative;
}

.btn-login.loading {
    pointer-events: none;
    opacity: 0.7;
}

.btn-login.loading::after {
    content: "Loading...";
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

/* SCORE */
#scoreBoard {
    position: fixed;
    top: 20px;
    right: 20px;
    color: white;
    font-weight: bold;
    z-index: 3;
}
</style>
</head>
<body>

<canvas id="bubbleCanvas"></canvas>

<div id="scoreBoard">Score: 0</div>

<div class="login-box text-center">

<img src="{{ asset('logo.png') }}" class="logo">

<h4 class="title">Yamaha Inventory System</h4>
<p class="text-muted">Silakan login untuk melanjutkan</p>

@if($errors->has('email'))
    <div id="loginError" class="text-danger mb-3">
        Email atau Password salah
    </div>
@endif

<form method="POST" action="{{ route('login') }}" id="loginForm">
@csrf

<div class="mb-3 text-start">
<label>Email</label>
<input type="email" id="email" name="email" class="form-control" required>
</div>

<div class="mb-3 text-start">
<label>Password</label>
<input type="password" id="password" name="password" class="form-control" required>
</div>

<button type="submit" class="btn btn-login w-100" id="loginBtn">Login</button>

</form>
</div>

<script>
const canvas = document.getElementById("bubbleCanvas");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let bubbles = [];
let particles = [];
let score = 0;

function createBubble() {
    return {
        x: Math.random() * canvas.width,
        y: canvas.height + 20,
        radius: Math.random() * 20 + 10,
        speed: Math.random() * 1.5 + 0.5
    };
}

function createParticles(x, y) {
    for (let i = 0; i < 5; i++) {
        particles.push({
            x: x,
            y: y,
            dx: (Math.random() - 0.5) * 4,
            dy: (Math.random() - 0.5) * 4,
            life: 30
        });
    }
}

function drawBubble(b) {
    ctx.beginPath();
    ctx.arc(b.x, b.y, b.radius, 0, Math.PI * 2);

    // warna bubble transparan putih
    ctx.fillStyle = "rgba(255, 255, 255, 0.15)";

    // efek glow lembut
    ctx.shadowColor = "rgba(255,255,255,0.3)";
    ctx.shadowBlur = 8;

    ctx.fill();

    // highlight biar kayak kaca
    ctx.beginPath();
    ctx.arc(b.x - b.radius/3, b.y - b.radius/3, b.radius/4, 0, Math.PI * 2);
    ctx.fillStyle = "rgba(255,255,255,0.4)";
    ctx.fill();
}

function drawParticles() {
    particles.forEach((p, i) => {
        ctx.fillStyle = "white";
        ctx.fillRect(p.x, p.y, 2, 2);

        p.x += p.dx;
        p.y += p.dy;
        p.life--;

        if (p.life <= 0) particles.splice(i, 1);
    });
}

function update() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (bubbles.length < 40) {
        bubbles.push(createBubble());
    }

    bubbles.forEach((b, i) => {
        b.y -= b.speed;
        drawBubble(b);

        if (b.y < 0) bubbles.splice(i, 1);
    });

    drawParticles();

    requestAnimationFrame(update);
}

canvas.addEventListener("click", function(e) {
    bubbles.forEach((b, i) => {
        const dx = e.clientX - b.x;
        const dy = e.clientY - b.y;
        const dist = Math.sqrt(dx * dx + dy * dy);

        if (dist < b.radius) {
            createParticles(b.x, b.y);
            bubbles.splice(i, 1);

            score++;
            document.getElementById("scoreBoard").innerText = "Score: " + score;
        }
    });
});

update();

window.addEventListener("resize", () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});

/* LOADING LOGIN */
document.getElementById("loginForm").addEventListener("submit", function() {
    document.getElementById("loginBtn").classList.add("loading");
});

// HILANGKAN PESAN ERROR SAAT USER MULAI MENGETIK
const loginError = document.getElementById('loginError');

if (loginError) {

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    emailInput.addEventListener('input', function () {
        loginError.style.display = 'none';
    });

    passwordInput.addEventListener('input', function () {
        loginError.style.display = 'none';
    });
}
</script>

</body>
</html>