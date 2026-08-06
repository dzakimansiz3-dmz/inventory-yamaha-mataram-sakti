<!DOCTYPE html>
<html lang="en">
<canvas id="bubbleCanvas"></canvas>
<head>
    <meta charset="UTF-8">
    <title>Yamaha Inventory</title>

    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
.nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
}

.nav-link i {
    width: 20px;
    text-align: center;
}

.nav-link:hover {
    background: rgba(255,255,255,0.1);
    border-radius: 5px;
}
#bubbleCanvas {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 0;
}

.content {
    position: relative;
    z-index: 1;
}
</style>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand">Yamaha Inventory</span>
    
        <div class="ml-auto mr-3 font-weight-bold">
            {{ auth()->user()->isOwner() ? '👑 Owner' : '👨‍💼 Admin' }}
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/dashboard" class="brand-link text-center">
            <img src="{{ asset('logo.png') }}" width="35">
            <span class="brand-text font-weight-light">Yamaha System</span>
        </a>

        <div class="sidebar">
            <nav>
                <ul class="nav nav-pills nav-sidebar flex-column">

                    <li class="nav-item">
                        <a href="/dashboard" class="nav-link">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/sparepart" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Sparepart</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('sparepart.riwayat') }}" class="nav-link">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Riwayat Sparepart</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/barang-masuk" class="nav-link">
                            <i class="nav-icon fas fa-arrow-down"></i>
                            <p>Barang Masuk</p>
                        </a>
                    </li>
                
                    <li class="nav-item">
                        <a href="/barang-keluar" class="nav-link">
                            <i class="nav-icon fas fa-arrow-up"></i>
                            <p>Barang Keluar</p>
                        </a>
                    </li>
            
                
                    {{-- LOGOUT --}}
                    <li class="nav-item">
                        <a href="#" class="nav-link"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                
                </ul>
            </nav>
        </div>
        
    </aside>

    <!-- Content -->
    <div class="content-wrapper p-3">
        @yield('content')
    <div class="content">
    <!-- semua isi dashboard kamu -->
    </div>
<script>
const canvas = document.getElementById("bubbleCanvas");
const ctx = canvas.getContext("2d");

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let bubbles = [];

function createBubble() {
    return {
        x: Math.random() * canvas.width,
        y: canvas.height + 20,
        radius: Math.random() * 8 + 5,
        speed: Math.random() * 2 + 1
    };
}

function drawBubble(b) {
    ctx.beginPath();
    ctx.arc(b.x, b.y, b.radius, 0, Math.PI * 2);
    ctx.fillStyle = "rgba(255,255,255,0.3)";
    ctx.fill();
}

function update() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (bubbles.length < 50) {
        bubbles.push(createBubble());
    }

    bubbles.forEach((b, index) => {
        b.y -= b.speed;
        drawBubble(b);

        if (b.y < 0) bubbles.splice(index, 1);
    });

    requestAnimationFrame(update);
}

canvas.addEventListener("click", function(e) {
    bubbles.forEach((b, index) => {
        const dx = e.clientX - b.x;
        const dy = e.clientY - b.y;
        const distance = Math.sqrt(dx * dx + dy * dy);

        if (distance < b.radius) {
            bubbles.splice(index, 1); // pecah 💥
        }
    });
});

update();

window.addEventListener("resize", () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});
</script>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* ========================
   GLOBAL CARD STYLE
======================== */
.modern-card {
    border-radius: 15px;
    border: none;
    transition: all 0.3s ease;
}

.modern-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.fw-bold {
    font-weight: bold;
}

.g-3 > [class*="col-"] {
    margin-bottom: 15px;
}

/* ========================
   GRADIENT WARNA (WAJIB)
======================== */
.bg-gradient-info {
    background: linear-gradient(45deg, #17a2b8, #138496);
}

.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800);
}

.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #218838);
}

.bg-gradient-danger {
    background: linear-gradient(45deg, #dc3545, #c82333);
}
</style>
</body>
</html>