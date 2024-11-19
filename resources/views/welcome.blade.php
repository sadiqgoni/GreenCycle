<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenCycle - Sustainable Waste Management Solutions</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        :root {
            --primary-green: #2E7D32;
            --secondary-green: #4CAF50;
            --light-green: #81C784;
            --dark-green: #1B5E20;
            --background-light: #F1F8E9;
            --text-dark: #1a1a1a;
            --text-light: #666666;
        }

        body {
            background: var(--background-light);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            z-index: 1000;
        }

        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-green);
            text-decoration: none;
        }

        .logo i {
            color: var(--secondary-green);
        }

        .hero-section {
            padding-top: 6rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: linear-gradient(135deg, rgba(46,125,50,0.1) 0%, rgba(129,199,132,0.1) 100%);
        }

        .hero-content {
            text-align: center;
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .hero-title {
            font-size: 3rem;
            color: var(--primary-green);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--text-light);
            margin-bottom: 3rem;
            line-height: 1.6;
        }

        .cards-container {
            display: flex;
            gap: 2rem;
            margin: 2rem auto;
            padding: 0 1rem;
            max-width: 1200px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .user-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            width: 380px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border: 2px solid transparent;
        }

        .user-card:hover {
            transform: translateY(-10px);
            border-color: var(--light-green);
        }

        .card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: var(--background-light);
            color: var(--primary-green);
        }

        .card-title {
            font-size: 1.5rem;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .card-description {
            color: var(--text-light);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            min-width: 120px;
        }

        .btn-primary {
            background: var(--primary-green);
            color: white;
        }

        .btn-primary:hover {
            background: var(--dark-green);
        }

        .btn-outline {
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
        }

        .btn-outline:hover {
            background: var(--primary-green);
            color: white;
        }

        .features-section {
            padding: 5rem 2rem;
            background: white;
        }

        .section-title {
            text-align: center;
            color: var(--text-dark);
            font-size: 2.2rem;
            margin-bottom: 3rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-item {
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--secondary-green);
            margin-bottom: 1rem;
        }

        .feature-title {
            font-size: 1.2rem;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .feature-description {
            color: var(--text-light);
            line-height: 1.6;
        }

        .stats-section {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            color: white;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .stat-item {
            padding: 1rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }

            .user-card {
                width: 100%;
                max-width: 340px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-content">
            <a href="/" class="logo">
                <i class="fas fa-recycle"></i>
                GreenCycle
            </a>
            <a href="#contact" class="btn btn-outline">Contact Us</a>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Transform Waste Management with GreenCycle</h1>
            <p class="hero-subtitle">Join our innovative platform that connects households with waste management companies. Together, we can create a cleaner, more sustainable future through efficient waste management and recycling solutions.</p>
        </div>

        <div class="cards-container">
            <div class="user-card">
                <div class="card-icon">
                    <i class="fas fa-home"></i>
                </div>
                <h2 class="card-title">Households</h2>
                <p class="card-description">Effortlessly manage your waste collection schedule, track pickups, and contribute to a greener environment. Access reliable waste management services at your fingertips.</p>
                <div class="button-group">
                    <a href="{{ url('/household/login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ url('/household/register') }}" class="btn btn-outline">Register</a>
                </div>
            </div>

            <div class="user-card">
                <div class="card-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h2 class="card-title">Companies</h2>
                <p class="card-description">Optimize your waste collection operations, expand your service area, and connect with more customers. Access powerful tools to manage your waste management business.</p>
                <div class="button-group">
                    <a href="{{ url('/company/login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ url('/company/register') }}" class="btn btn-outline">Register</a>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section">
        <h2 class="section-title">Why Choose GreenCycle?</h2>
        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="feature-title">Easy Scheduling</h3>
                <p class="feature-description">Book and manage waste collections with just a few taps on your device</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Real-time Tracking</h3>
                <p class="feature-description">Monitor collection status and get instant updates on your service requests</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3 class="feature-title">Eco-Friendly</h3>
                <p class="feature-description">Support sustainable practices and reduce environmental impact</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3 class="feature-title">Reliable Service</h3>
                <p class="feature-description">Connect with verified waste management companies in your area</p>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">15,000+</div>
                <div class="stat-label">Active Households</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Partner Companies</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">98%</div>
                <div class="stat-label">Customer Satisfaction</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">50,000+</div>
                <div class="stat-label">Collections Completed</div>
            </div>
        </div>
    </section>

    <script>
        // Animate elements on scroll
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.user-card, .feature-item, .stat-item');
            elements.forEach(element => {
                const position = element.getBoundingClientRect();
                if(position.top < window.innerHeight - 100) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        };

        // Initial animation for visible elements
        window.addEventListener('load', animateOnScroll);
        window.addEventListener('scroll', animateOnScroll);
    </script>
</body>
</html>