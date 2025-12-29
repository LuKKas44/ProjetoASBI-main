<?php
$base = "/projeto_ASBI-main";
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ASBI - Associação de Saúde Bucal Infantil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="index1.css">
</head>

<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="nav-logo">
                <img src="<?= $base ?>/img/LOGOASBI.png" alt="Logo ASBI" />
                <span class="logo-text">ASBI</span>
            </div>
            <ul class="nav-links">
                <li><a href="#home" class="active">Home</a></li>
                <li><a href="#sobre">Sobre</a></li>
                <li><a href="#servicos">Serviços</a></li>
                <li><a href="#contato">Contato</a></li>
            </ul>
            <div class="nav-login">
                <a href="<?= $base ?>/cadastro_e_login/login.php">Login</a>

            </div>
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
        </div>

        <!-- Mobile Navigation -->
        <div class="mobile-nav" id="mobileNav">
            <a href="index.php">Home</a>
            <a href="#sobre.html">Sobre</a>
            <a href="#servicos">Serviços</a>
            <a href="mailto:assosiacaoasbi@gmail.com">Contato</a>
            <a href="<?= $base ?>/cadastro_e_login/login.php">Login</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="hero-content loading">
            <h1>Cuidando do Sorriso das Crianças</h1>
            <p class="hero-subtitle">
                Promovemos saúde bucal infantil através de atendimento humanizado,
                educação e ações sociais transformadoras
            </p>
            <div class="hero-buttons">
                <a href="#servicos" class="btn btn-primary">
                    <span>🦷</span> Nossos Serviços
                </a>
                <a href="<?= $base ?>/cadastro_e_login/login.php" class="btn btn-secondary">
                    <span>👤</span> Área do Cliente
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="sobre" class="about">
        <div class="about-container loading">
            <div class="about-content">
                <h2>Sobre a ASBI</h2>
                <p>
                    A Associação de Saúde Bucal Infantil (ASBI) é uma organização dedicada
                    a promover a saúde bucal das crianças através de atendimento especializado,
                    programas educativos e ações sociais.
                </p>
                <p>
                    Nossa missão é garantir que toda criança tenha acesso a cuidados
                    odontológicos de qualidade, independentemente de sua condição social ou econômica.
                </p>
                <ul class="about-features">
                    <li>Atendimento odontológico especializado</li>
                    <li>Programas de educação em saúde bucal</li>
                    <li>Ações sociais em comunidades carentes</li>
                    <li>Equipe de profissionais qualificados</li>
                </ul>
            </div>
            <div class="about-image">
                <img src="img/imagem1.jpg" alt="Sobre a ASBI">
            </div>
        </div>
    </section>

    <!-- Carousel Section -->
    <section class="carousel-section">
        <div class="loading">
            <h2 class="section-title">Nossa Missão em Ação</h2>
            <p class="section-subtitle">
                Veja como transformamos sorrisos e vidas através do nosso trabalho dedicado
            </p>
        </div>

        <div class="carousel-container loading">
            <div class="carousel-main">
                <div class="carousel-side">
                    <img src="img/imagem4.jpg" alt="Ação Social" class="side-img">
                    <div class="side-caption">
                        <strong>25 de Outubro</strong><br>
                        Dia Nacional da Saúde Bucal
                    </div>
                </div>

                <div class="carousel">
                    <div class="carousel-slide active">
                        <img src="img/imagem1.jpg" alt="Crianças sorrindo">
                        <div class="carousel-caption">Alegria das crianças atendidas</div>
                    </div>
                    <div class="carousel-slide">
                        <img src="img/imagem2.jpg" alt="Dentista atendendo criança">
                        <div class="carousel-caption">Cuidado odontológico com carinho</div>
                    </div>
                    <div class="carousel-slide">
                        <img src="img/imagem3.jpg" alt="Equipe voluntária">
                        <div class="carousel-caption">Equipe de voluntários dedicada</div>
                    </div>
                    <button class="carousel-btn prev" onclick="moveSlide(-1)">‹</button>
                    <button class="carousel-btn next" onclick="moveSlide(1)">›</button>

                    <!-- Indicators -->
                    <div class="carousel-indicators">
                        <div class="carousel-indicator active" onclick="currentSlideIndex(1)"></div>
                        <div class="carousel-indicator" onclick="currentSlideIndex(2)"></div>
                        <div class="carousel-indicator" onclick="currentSlideIndex(3)"></div>
                    </div>
                </div>

                <div class="carousel-side">
                    <img src="img/imagem5.jpg" alt="Educação e saúde" class="side-img">
                    <div class="side-caption">
                        <strong>Saúde para Todos</strong><br>
                        Educação e prevenção
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="servicos" class="features">
        <div class="loading">
            <h2 class="section-title">Nossos Serviços</h2>
            <p class="section-subtitle">
                Oferecemos atendimento completo e humanizado para a saúde bucal infantil
            </p>
        </div>

        <div class="features-grid loading">
            <div class="feature-card">
                <div class="feature-icon">🦷</div>
                <h3 class="feature-title">Atendimento Odontológico</h3>
                <p class="feature-description">
                    Consultas especializadas em odontopediatria com profissionais qualificados
                    e ambiente acolhedor para as crianças.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📚</div>
                <h3 class="feature-title">Educação em Saúde</h3>
                <p class="feature-description">
                    Programas educativos sobre higiene bucal, prevenção e cuidados diários
                    para crianças e famílias.
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🤝</div>
                <h3 class="feature-title">Ações Sociais</h3>
                <p class="feature-description">
                    Campanhas e projetos sociais levando atendimento odontológico
                    para comunidades carentes.
                </p>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="loading">
            <h2 class="section-title" style="color: white; margin-bottom: 3rem;">
                Nossos Resultados
            </h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number" data-target="2500">0</span>
                    <span class="stat-label">Crianças Atendidas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="150">0</span>
                    <span class="stat-label">Voluntários Ativos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="50">0</span>
                    <span class="stat-label">Projetos Realizados</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="8">0</span>
                    <span class="stat-label">Anos de Atuação</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contato" class="contact">
        <div class="contact-container loading">
            <h2 class="section-title">Entre em Contato</h2>
            <p class="section-subtitle">
                Estamos aqui para ajudar e responder suas dúvidas sobre nossos serviços
            </p>

            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon">📧</div>
                    <h3 class="contact-title">E-mail</h3>
                    <p class="contact-info">associacaoasbi@gmail.com</p>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">📱</div>
                    <h3 class="contact-title">Telefone</h3>
                    <p class="contact-info">(21) 91234-5678</p>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">📍</div>
                    <h3 class="contact-title">Endereço</h3>
                    <p class="contact-info">Av. Paris, 84<br>Rio de Janeiro - RJ</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <img src="<?= $base ?>/img/LOGOASBI.png" alt="Logo ASBI">
            </div>

            <div class="footer-links">
                <a href="#home">Home</a>
                <a href="#sobre">Sobre</a>
                <a href="#servicos">Serviços</a>
                <a href="<?= $base ?>/cadastro_e_login/login.php">Login</a>
                <a href="mailto:associacaoasbi@gmail.com">Contato</a>
            </div>

            <div class="footer-social">
                <a href="#" class="social-btn">📘</a>
                <a href="#" class="social-btn">📷</a>
                <a href="#" class="social-btn">🐦</a>
                <a href="#" class="social-btn">📺</a>
            </div>

            <div class="footer-copyright">
                <p>&copy; 2025 Associação de Saúde Bucal Infantil. Todos os direitos reservados.</p>
                <p>Desenvolvido com ❤️ para transformar sorrisos</p>
            </div>
        </div>
    </footer>

    <script>
    // CAROUSEL FUNCTIONALITY - CORRIGIDO E SIMPLIFICADO
    console.log('Iniciando carousel...');

    let currentSlide = 0;
    let slideInterval;

    // Aguarda o DOM carregar completamente
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM carregado');

        const slides = document.querySelectorAll('.carousel-slide');
        const indicators = document.querySelectorAll('.carousel-indicator');
        const carousel = document.querySelector('.carousel');

        console.log('Slides encontrados:', slides.length);
        console.log('Indicators encontrados:', indicators.length);

        if (slides.length === 0) {
            console.error('Nenhum slide encontrado!');
            return;
        }

        function showSlide(index) {
            console.log('Mostrando slide:', index);

            // Remove classe active de todos
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(indicator => indicator.classList.remove('active'));

            // Normalize o índice
            if (index >= slides.length) currentSlide = 0;
            if (index < 0) currentSlide = slides.length - 1;

            // Adiciona classe active ao slide e indicador atual
            if (slides[currentSlide]) {
                slides[currentSlide].classList.add('active');
                console.log('Slide ativo:', currentSlide);
            }

            if (indicators[currentSlide]) {
                indicators[currentSlide].classList.add('active');
            }
        }

        // Função para mover slide
        window.moveSlide = function(direction) {
            console.log('Movendo slide:', direction);
            currentSlide += direction;
            showSlide(currentSlide);
            restartAutoplay();
        };

        // Função para ir direto para um slide
        window.currentSlideIndex = function(index) {
            console.log('Indo para slide:', index);
            currentSlide = index - 1;
            showSlide(currentSlide);
            restartAutoplay();
        };

        // Auto-play
        function autoPlay() {
            currentSlide++;
            showSlide(currentSlide);
        }

        function startAutoplay() {
            slideInterval = setInterval(autoPlay, 4000);
            console.log('Autoplay iniciado');
        }

        function stopAutoplay() {
            if (slideInterval) {
                clearInterval(slideInterval);
                console.log('Autoplay parado');
            }
        }

        function restartAutoplay() {
            stopAutoplay();
            startAutoplay();
        }

        // Event listeners para pause/resume no hover
        if (carousel) {
            carousel.addEventListener('mouseenter', stopAutoplay);
            carousel.addEventListener('mouseleave', startAutoplay);
        }

        // Controles de teclado
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                moveSlide(-1);
            } else if (e.key === 'ArrowRight') {
                moveSlide(1);
            }
        });

        // Touch/swipe para mobile
        let touchStartX = 0;
        let touchEndX = 0;

        if (carousel) {
            carousel.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
            });

            carousel.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            });
        }

        function handleSwipe() {
            const swipeThreshold = 50;
            if (touchEndX < touchStartX - swipeThreshold) {
                moveSlide(1); // Swipe left - próximo
            }
            if (touchEndX > touchStartX + swipeThreshold) {
                moveSlide(-1); // Swipe right - anterior
            }
        }

        // Inicializa o carousel
        showSlide(0);
        startAutoplay();

        console.log('Carousel inicializado com sucesso!');
    });

    // MOBILE MENU
    function toggleMobileMenu() {
        const mobileNav = document.getElementById('mobileNav');
        if (mobileNav) {
            mobileNav.classList.toggle('active');
        }
    }

    // SMOOTH SCROLLING
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }

                // Fecha menu mobile se estiver aberto
                const mobileNav = document.getElementById('mobileNav');
                if (mobileNav) {
                    mobileNav.classList.remove('active');
                }
            });
        });
    });

    // NAVIGATION ATIVA
    function setActiveNav() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-links a');

        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100;
            const sectionHeight = section.clientHeight;
            if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', setActiveNav);

    // COUNTER ANIMATION
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');

        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            const duration = 2000; // 2 segundos
            const steps = 60;
            const increment = target / steps;
            let current = 0;

            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, duration / steps);
        });
    }

    // INTERSECTION OBSERVER
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-on-scroll');

                // Inicia animação dos counters quando a seção stats é visível
                if (entry.target.closest('.stats')) {
                    animateCounters();
                }
            }
        });
    }, observerOptions);

    // Observa elementos com classe loading
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.loading').forEach(el => {
            observer.observe(el);
        });
    });

    // NAVBAR SCROLL EFFECT
    window.addEventListener('scroll', function() {
        const nav = document.querySelector('nav');
        if (nav) {
            if (window.scrollY > 50) {
                nav.style.background =
                    'linear-gradient(90deg, rgba(46, 198, 247, 0.98) 0%, rgba(123, 225, 65, 0.98) 25%, rgba(255, 111, 216, 0.98) 75%, rgba(255, 225, 86, 0.98) 100%)';
                nav.style.boxShadow = '0 12px 35px rgba(46, 198, 247, 0.3)';
            } else {
                nav.style.background =
                    'linear-gradient(90deg, rgba(46, 198, 247, 0.95) 0%, rgba(123, 225, 65, 0.95) 25%, rgba(255, 111, 216, 0.95) 75%, rgba(255, 225, 86, 0.95) 100%)';
                nav.style.boxShadow = '0 8px 25px rgba(46, 198, 247, 0.2)';
            }
        }
    });

    // LOADING ANIMATIONS
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.querySelectorAll('.loading').forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                }, index * 100);
            });
        }, 200);
    });

    // FECHAR MENU MOBILE AO CLICAR FORA
    document.addEventListener('click', function(e) {
        const mobileNav = document.getElementById('mobileNav');
        const toggleBtn = document.querySelector('.mobile-menu-toggle');

        if (mobileNav && toggleBtn) {
            if (!mobileNav.contains(e.target) && !toggleBtn.contains(e.target)) {
                mobileNav.classList.remove('active');
            }
        }
    });
    </script>
</body>

</html>