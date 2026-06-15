<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Alex Fitness</title>

    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css" crossorigin="anonymous">

    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">

    <style>
        :root {
            --primary: #c8e600;
            --primary-dark: #a8c200;
            --bg-main: #0d0d0d;
            --bg-card: #141414;
            --bg-section: #1a1a1a;
            --border: rgba(200,230,0,0.25);
            --text-main: #f0f0f0;
            --text-muted: #aaaaaa;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
        }

        /* Top bar */
        .top-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(13,13,13,0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 12px 0;
        }

        .top-bar .inner {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-btn {
            background: var(--primary);
            color: #000;
            border: none;
            padding: 9px 22px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.92rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: background 0.2s, transform 0.2s;
        }
        .back-btn:hover {
            background: var(--primary-dark);
            color: #000;
            text-decoration: none;
            transform: translateY(-1px);
        }

        /* Language toggle */
        .lang-toggle {
            display: inline-flex;
            gap: 6px;
        }
        .lang-btn {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 7px;
            padding: 6px 13px;
            font-weight: 600;
            font-size: 0.82rem;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.5px;
        }
        .lang-btn:hover { border-color: var(--primary); color: var(--primary); }
        .lang-btn.active {
            background: var(--primary);
            color: #000;
            border-color: var(--primary);
        }

        /* Main content */
        .page-wrap {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px 60px;
        }

        /* Hero */
        .hero {
            text-align: center;
            padding: 48px 24px 36px;
            background: linear-gradient(135deg, #1a1a1a 0%, #111 100%);
            border-radius: 16px;
            border: 1px solid var(--border);
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -60px; left: 50%; transform: translateX(-50%);
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(200,230,0,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-icon {
            width: 56px; height: 56px;
            background: rgba(200,230,0,0.12);
            border: 1px solid var(--border);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            font-size: 24px;
            color: var(--primary);
        }
        .hero-title {
            color: var(--primary);
            font-size: 2.2rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .hero-sub {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .hero-divider {
            width: 60px; height: 3px;
            background: var(--primary);
            border-radius: 2px;
            margin: 18px auto 0;
        }

        /* Sections */
        .section-card {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 16px;
            transition: border-color 0.2s;
        }
        .section-card:hover { border-color: var(--border); }

        .section-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px; height: 30px;
            background: var(--primary);
            color: #000;
            border-radius: 7px;
            font-size: 0.78rem;
            font-weight: 800;
            margin-right: 10px;
            flex-shrink: 0;
        }
        .section-title {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
        }

        .section-content {
            color: var(--text-muted);
            font-size: 0.95rem;
            line-height: 1.75;
        }
        .section-content p { margin-bottom: 12px; }
        .section-content p:last-child { margin-bottom: 0; }
        .section-content ul {
            padding-left: 0;
            list-style: none;
            margin-bottom: 12px;
        }
        .section-content li {
            padding: 6px 0 6px 22px;
            position: relative;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .section-content li:last-child { border-bottom: none; }
        .section-content li::before {
            content: '›';
            position: absolute; left: 6px;
            color: var(--primary);
            font-weight: 700;
        }
        .section-content strong { color: #e0e0e0; }

        /* Highlight box */
        .info-box {
            background: rgba(200,230,0,0.06);
            border: 1px solid rgba(200,230,0,0.2);
            border-left: 3px solid var(--primary);
            border-radius: 8px;
            padding: 14px 18px;
            margin-top: 14px;
        }
        .info-box p { margin-bottom: 6px; color: var(--text-muted); }
        .info-box p:last-child { margin-bottom: 0; }
        .info-box strong { color: #ddd; }

        /* Contact card */
        .contact-card {
            background: linear-gradient(135deg, #1a1a1a 0%, #141414 100%);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px;
            text-align: center;
            margin-top: 32px;
        }
        .contact-card h4 {
            color: var(--primary);
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 16px;
        }
        .contact-card p {
            color: var(--text-muted);
            margin-bottom: 8px;
            font-size: 0.93rem;
        }
        .contact-card p:last-child { margin-bottom: 0; }
        .contact-card a { color: var(--primary); text-decoration: none; }
        .contact-card a:hover { color: var(--primary-dark); text-decoration: underline; }

        @media (max-width: 768px) {
            .hero-title { font-size: 1.55rem; letter-spacing: 1px; }
            .section-card { padding: 18px 16px; }
            .page-wrap { padding: 24px 12px 48px; }
            .top-bar .inner { flex-direction: column; gap: 10px; align-items: flex-start; }
        }

        .sr-only { position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0; }
    </style>
</head>
<body>

    <!-- Sticky top bar -->
    <div class="top-bar">
        <div class="inner">
            <a href="index.php" class="back-btn" id="back-link">
                <i class="fas fa-arrow-left"></i>
                <span id="back-text">მთავარ გვერდზე დაბრუნება</span>
            </a>
            <div class="lang-toggle" aria-label="Language Selector">
                <button type="button" class="lang-btn active" id="btn-ka">KA</button>
                <button type="button" class="lang-btn" id="btn-en">EN</button>
                <button type="button" class="lang-btn" id="btn-ru">RU</button>
            </div>
        </div>
    </div>

    <div class="page-wrap">

        <!-- ===== GEORGIAN ===== -->
        <div id="privacy-ka">
            <div class="hero">
                <div class="hero-icon"><i class="fas fa-shield-halved"></i></div>
                <h1 class="hero-title">კონფიდენციალობის პოლიტიკა</h1>
                <p class="hero-sub">Alex Fitness — პერსონალურ მონაცემთა დამუშავების პოლიტიკა</p>
                <div class="hero-divider"></div>
            </div>

            <div class="section-card">
                <div class="section-content">
                    <p>ეს კონფიდენციალობის პოლიტიკა განმარტავს, თუ როგორ აგროვებს, იყენებს და იცავს <strong>Alex Fitness</strong> თქვენს პერსონალურ ინფორმაციას, როდესაც იყენებთ ჩვენს ვებ-გვერდს <strong>alexfitness.ge</strong> ან ჩვენი ფიტნეს კლუბის მომსახურებებს.</p>
                    <p>ჩვენ ვამუშავებთ თქვენს პერსონალურ მონაცემებს საქართველოს „პერსონალურ მონაცემთა დაცვის შესახებ" კანონის მოთხოვნების შესაბამისად.</p>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">1</span>მონაცემთა კონტროლერი</h2>
                <div class="section-content">
                    <div class="info-box">
                        <p><strong>კომპანია:</strong> Alex Fitness</p>
                        <p><strong>მისამართი:</strong> შოთა რუსთაველი 170-25, ქობულეთი 6200, საქართველო</p>
                        <p><strong>ტელეფონი:</strong> +995 599 061 572</p>
                        <p><strong>Telegram:</strong> @alex_fitness_kobuleti</p>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">2</span>რა ინფორმაციას ვაგროვებთ</h2>
                <div class="section-content">
                    <p>ჩვენ შეიძლება ავიღოთ და ვამუშავოთ შემდეგი ტიპის პერსონალური მონაცემები:</p>
                    <ul>
                        <li><strong>პირადი იდენტიფიკაციის ინფორმაცია:</strong> სახელი, გვარი, პირადი ნომერი</li>
                        <li><strong>საკონტაქტო ინფორმაცია:</strong> ტელეფონის ნომერი, ელექტრონული ფოსტის მისამართი</li>
                        <li><strong>საფინანსო ინფორმაცია:</strong> საბანკო ბარათის მონაცემები, გადახდების ისტორია</li>
                        <li><strong>ტექნიკური ინფორმაცია:</strong> IP მისამართი, ბრაუზერის ტიპი, მოწყობილობის ინფორმაცია</li>
                        <li><strong>სავარჯიშო მონაცემები:</strong> კლუბში ვიზიტის ისტორია, აბონიმენტის ინფორმაცია</li>
                        <li><strong>ჯანმრთელობის მონაცემები:</strong> ექიმის ცნობები (საჭიროების შემთხვევაში)</li>
                        <li><strong>ფოტო:</strong> პროფილის ფოტო, უსაფრთხოების კამერების ჩანაწერები</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">3</span>მონაცემთა გამოყენების მიზნები</h2>
                <div class="section-content">
                    <p>ჩვენ ვიყენებთ თქვენს პერსონალურ მონაცემებს შემდეგი მიზნებისთვის:</p>
                    <ul>
                        <li><strong>მომსახურების გაწევა:</strong> აბონიმენტის ფორმირება, კლუბში დაშვება</li>
                        <li><strong>გადახდების დამუშავება:</strong> სააბონენტო გადასახადების მიღება და დამუშავება</li>
                        <li><strong>კომუნიკაცია:</strong> მნიშვნელოვანი შეტყობინებების და განახლებების გაგზავნა</li>
                        <li><strong>მარკეტინგი:</strong> სპეციალური შეთავაზებების და ახალი სერვისების შესახებ ინფორმაციის გაგზავნა</li>
                        <li><strong>უსაფრთხოება:</strong> კლუბის ტერიტორიაზე უსაფრთხოების უზრუნველყოფა</li>
                        <li><strong>სამართლებრივი ვალდებულებები:</strong> კანონით განსაზღვრული ვალდებულებების შესრულება</li>
                        <li><strong>ანალიტიკა:</strong> მომსახურების ხარისხის გაუმჯობესება</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">4</span>მონაცემთა დამუშავების სამართლებრივი საფუძველი</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>ხელშეკრულება:</strong> თქვენთან დადებული ხელშეკრულების შესასრულებლად</li>
                        <li><strong>თანხმობა:</strong> თქვენი მოცემული ცალსახა თანხმობის საფუძველზე</li>
                        <li><strong>სამართლებრივი ვალდებულება:</strong> კანონით განსაზღვრული ვალდებულებების შესასრულებლად</li>
                        <li><strong>ლეგიტიმური ინტერესი:</strong> ჩვენი ლეგიტიმური ბიზნეს ინტერესების დასაცავად</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">5</span>მონაცემთა გაზიარება</h2>
                <div class="section-content">
                    <p>ჩვენ არ ვყიდით და არ ვუზიარებთ თქვენს პერსონალურ მონაცემებს მესამე მხარეებს, გარდა შემდეგი შემთხვევებისა:</p>
                    <ul>
                        <li><strong>სამართლებრივი ვალდებულება:</strong> სახელმწიფო ორგანოების მოთხოვნით</li>
                        <li><strong>სერვისის მიმწოდებლები:</strong> ჩვენი სანდო პარტნერები (საბანკო სერვისები, IT მხარდაჭერა)</li>
                        <li><strong>უსაფრთხოება:</strong> კლუბისა და წევრების უსაფრთხოების დაცვისთვის</li>
                        <li><strong>თანხმობა:</strong> თქვენი ცალსახა თანხმობით</li>
                    </ul>
                    <div class="info-box">
                        <p><strong>მნიშვნელოვანი:</strong> ყველა მესამე მხარე, რომელსაც ვუზიარებთ თქვენს მონაცემებს, ვალდებულია დაიცვას მონაცემთა კონფიდენციალობა.</p>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">6</span>მონაცემთა შენახვის ვადა</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>აქტიური წევრობის დროს:</strong> მთელი აბონიმენტის ვადის განმავლობაში</li>
                        <li><strong>წევრობის შეწყვეტის შემდეგ:</strong> 3 წლის განმავლობაში (ბუღალტრული აღრიცხვისთვის)</li>
                        <li><strong>მარკეტინგული მიზნები:</strong> თქვენი თანხმობის გაუქმებამდე</li>
                        <li><strong>უსაფრთხოების ვიდეო:</strong> 30 დღის განმავლობაში</li>
                        <li><strong>სამართლებრივი მოთხოვნები:</strong> კანონით განსაზღვრული ვადით</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">7</span>მონაცემთა უსაფრთხოება</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>ციფრული შიფრაცია:</strong> მონაცემების გადაცემისა და შენახვისას</li>
                        <li><strong>წვდომის კონტროლი:</strong> მხოლოდ ავტორიზებული პერსონალისთვის</li>
                        <li><strong>რეგულარული მონიტორინგი:</strong> უსაფრთხოების სისტემების შემოწმება</li>
                        <li><strong>თანამშრომელთა ტრენინგი:</strong> მონაცემთა დაცვის საკითხებში</li>
                        <li><strong>ფიზიკური უსაფრთხოება:</strong> სერვერების და მოწყობილობების დაცვა</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">8</span>თქვენი უფლებები</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>წვდომის უფლება:</strong> ინფორმაციის მიღება ამუშავებული მონაცემების შესახებ</li>
                        <li><strong>რედაქტირების უფლება:</strong> არასწორი მონაცემების შესწორება</li>
                        <li><strong>წაშლის უფლება:</strong> მონაცემების წაშლის მოთხოვნა</li>
                        <li><strong>დამუშავების შეზღუდვა:</strong> კონკრეტული მონაცემების დამუშავების შეჩერება</li>
                        <li><strong>მონაცემთა პორტაბელობა:</strong> თქვენი მონაცემების მიღება მანქანაზე წასაკითხი ფორმით</li>
                        <li><strong>უარყოფის უფლება:</strong> მარკეტინგული მიზნებისთვის დამუშავების შეწყვეტა</li>
                        <li><strong>საჩივრის უფლება:</strong> ზედამხედველ ორგანოში განცხადების შეტანა</li>
                    </ul>
                    <div class="info-box">
                        <p><strong>უფლებების გამოყენებისთვის</strong> დაგვიკავშირდით Telegram-ზე: <a href="https://t.me/alex_fitness_kobuleti" target="_blank" style="color:#c8e600;">@alex_fitness_kobuleti</a></p>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">9</span>Cookie-ები და ტრაკინგი</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>აუცილებელი Cookie-ები:</strong> ვებ-გვერდის მუშაობისთვის</li>
                        <li><strong>ფუნქციონალური Cookie-ები:</strong> მომხმარებლის გამოცდილების გაუმჯობესებისთვის</li>
                        <li><strong>ანალიტიკური Cookie-ები:</strong> ტრაფიკის ანალიზისთვის</li>
                        <li><strong>მარკეტინგული Cookie-ები:</strong> რელევანტური შეთავაზებების ჩვენებისთვის</li>
                    </ul>
                    <p>Cookie-ების პარამეტრების მართვა შეგიძლიათ ბრაუზერის პარამეტრებში.</p>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">10</span>უსაფრთხოების ინციდენტი</h2>
                <div class="section-content">
                    <ul>
                        <li>დაუყოვნებლივ მივმართავთ ღონისძიებებს ინციდენტის შესაჩერებლად</li>
                        <li>შევაფასებთ რისკს და შედეგებს</li>
                        <li>დროულად შეგატყობინებთ, თუ ინციდენტი გავლენას ახდენს თქვენს მონაცემებზე</li>
                        <li>შევატყობინებთ შესაბამის ზედამხედველ ორგანოებს</li>
                        <li>განვახორციელებთ გამოსწორების ზომებს მომავალში მსგავსი ინციდენტების თავიდან ასაცილებლად</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">11</span>პოლიტიკის ცვლილებები</h2>
                <div class="section-content">
                    <ul>
                        <li>განახლებული ვერსია გამოქვეყნდება ჩვენს ვებ-გვერდზე</li>
                        <li>მნიშვნელოვანი ცვლილებების შესახებ შეგიტყობინებთ Telegram-ის საშუალებით</li>
                        <li>ახალი პოლიტიკა ძალაში შევა მისი გამოქვეყნების თარიღიდან</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">12</span>კონტაქტი</h2>
                <div class="section-content">
                    <div class="info-box">
                        <p><strong>კომპანია:</strong> Alex Fitness</p>
                        <p><strong>მისამართი:</strong> შოთა რუსთაველი 170-25, ქობულეთი 6200, საქართველო</p>
                        <p><strong>ტელეფონი:</strong> <a href="tel:+995599061572" style="color:#c8e600;">+995 599 061 572</a></p>
                        <p><strong>Telegram:</strong> <a href="https://t.me/alex_fitness_kobuleti" target="_blank" style="color:#c8e600;">@alex_fitness_kobuleti</a></p>
                        <p><strong>სამუშაო საათები:</strong> ყოველდღე 08:00–22:00</p>
                    </div>
                </div>
            </div>

            <div class="contact-card">
                <h4>კითხვები გაქვთ?</h4>
                <p><strong>მისამართი:</strong> შოთა რუსთაველი 170-25, ქობულეთი 6200</p>
                <p><strong>ტელეფონი:</strong> <a href="tel:+995599061572">+995 599 061 572</a></p>
                <p><strong>Telegram:</strong> <a href="https://t.me/alex_fitness_kobuleti" target="_blank">@alex_fitness_kobuleti</a></p>
                <p style="margin-top:14px;font-size:0.82rem;color:#666;"><strong>ბოლოს განახლდა:</strong> მაისი 2026</p>
            </div>
        </div>

        <!-- ===== ENGLISH ===== -->
        <div id="privacy-en" style="display:none">
            <div class="hero">
                <div class="hero-icon"><i class="fas fa-shield-halved"></i></div>
                <h1 class="hero-title">Privacy Policy</h1>
                <p class="hero-sub">Alex Fitness — Personal Data Processing Policy</p>
                <div class="hero-divider"></div>
            </div>

            <div class="section-card">
                <div class="section-content">
                    <p>This Privacy Policy explains how <strong>Alex Fitness</strong> collects, uses, and protects your personal information when you use our website <strong>alexfitness.ge</strong> or our fitness club services.</p>
                    <p>We process your personal data in accordance with the requirements of Georgia's "Personal Data Protection Law".</p>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">1</span>Data Controller</h2>
                <div class="section-content">
                    <div class="info-box">
                        <p><strong>Company:</strong> Alex Fitness</p>
                        <p><strong>Address:</strong> Shota Rustaveli 170-25, Kobuleti 6200, Georgia</p>
                        <p><strong>Phone:</strong> +995 599 061 572</p>
                        <p><strong>Telegram:</strong> @alex_fitness_kobuleti</p>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">2</span>What Information We Collect</h2>
                <div class="section-content">
                    <p>We may collect and process the following types of personal data:</p>
                    <ul>
                        <li><strong>Personal identification:</strong> Name, surname, personal ID number</li>
                        <li><strong>Contact information:</strong> Phone number, email address</li>
                        <li><strong>Financial information:</strong> Bank card details, payment history</li>
                        <li><strong>Technical information:</strong> IP address, browser type, device information</li>
                        <li><strong>Workout data:</strong> Club visit history, membership information</li>
                        <li><strong>Health data:</strong> Medical certificates (when required)</li>
                        <li><strong>Photos:</strong> Profile photo, security camera recordings</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">3</span>Purpose of Data Processing</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Service provision:</strong> Membership creation, club access</li>
                        <li><strong>Payment processing:</strong> Collecting and processing membership fees</li>
                        <li><strong>Communication:</strong> Sending important notices and updates</li>
                        <li><strong>Marketing:</strong> Sending information about promotional offers and new services</li>
                        <li><strong>Security:</strong> Ensuring safety on club premises</li>
                        <li><strong>Legal obligations:</strong> Compliance with legal requirements</li>
                        <li><strong>Analytics:</strong> Improving service quality</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">4</span>Legal Basis for Processing</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Contract:</strong> To perform our contract with you</li>
                        <li><strong>Consent:</strong> Based on your explicit consent</li>
                        <li><strong>Legal obligation:</strong> To comply with legal requirements</li>
                        <li><strong>Legitimate interest:</strong> To protect our legitimate business interests</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">5</span>Data Sharing</h2>
                <div class="section-content">
                    <p>We do not sell or share your personal data with third parties, except:</p>
                    <ul>
                        <li><strong>Legal obligation:</strong> Upon request from government authorities</li>
                        <li><strong>Service providers:</strong> Our trusted partners (banking services, IT support)</li>
                        <li><strong>Security:</strong> To protect the safety of the club and members</li>
                        <li><strong>Consent:</strong> With your explicit consent</li>
                    </ul>
                    <div class="info-box">
                        <p><strong>Important:</strong> All third parties with whom we share your data are obligated to maintain data confidentiality.</p>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">6</span>Data Retention</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>During active membership:</strong> Throughout the membership period</li>
                        <li><strong>After membership termination:</strong> For 3 years (for accounting purposes)</li>
                        <li><strong>Marketing purposes:</strong> Until you withdraw your consent</li>
                        <li><strong>Security videos:</strong> For 30 days</li>
                        <li><strong>Legal requirements:</strong> For the period specified by law</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">7</span>Data Security</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Encryption:</strong> During data transmission and storage</li>
                        <li><strong>Access control:</strong> Only for authorized personnel</li>
                        <li><strong>Regular monitoring:</strong> Security system checks</li>
                        <li><strong>Staff training:</strong> On data protection issues</li>
                        <li><strong>Physical security:</strong> Protection of servers and devices</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">8</span>Your Rights</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Right of access:</strong> Receive information about processed data</li>
                        <li><strong>Right to rectification:</strong> Correct incorrect data</li>
                        <li><strong>Right to erasure:</strong> Request deletion of data</li>
                        <li><strong>Right to restrict processing:</strong> Limit processing of specific data</li>
                        <li><strong>Data portability:</strong> Receive your data in machine-readable format</li>
                        <li><strong>Right to object:</strong> Stop processing for marketing purposes</li>
                        <li><strong>Right to complaint:</strong> File a complaint with supervisory authorities</li>
                    </ul>
                    <div class="info-box">
                        <p><strong>To exercise these rights</strong>, contact us on Telegram: <a href="https://t.me/alex_fitness_kobuleti" target="_blank" style="color:#c8e600;">@alex_fitness_kobuleti</a></p>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">9</span>Cookies</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Essential cookies:</strong> For website operation</li>
                        <li><strong>Functional cookies:</strong> To improve user experience</li>
                        <li><strong>Analytics cookies:</strong> For traffic analysis</li>
                        <li><strong>Marketing cookies:</strong> To show relevant offers</li>
                    </ul>
                    <p>You can manage cookie settings in your browser preferences.</p>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">10</span>Security Incidents</h2>
                <div class="section-content">
                    <ul>
                        <li>Immediately take measures to stop the incident</li>
                        <li>Assess risks and consequences</li>
                        <li>Notify you promptly if the incident affects your data</li>
                        <li>Notify relevant supervisory authorities</li>
                        <li>Implement corrective measures to prevent future incidents</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">11</span>Policy Changes</h2>
                <div class="section-content">
                    <ul>
                        <li>The updated version will be published on our website</li>
                        <li>We will notify you of significant changes via Telegram</li>
                        <li>The new policy will take effect from its publication date</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">12</span>Contact</h2>
                <div class="section-content">
                    <div class="info-box">
                        <p><strong>Company:</strong> Alex Fitness</p>
                        <p><strong>Address:</strong> Shota Rustaveli 170-25, Kobuleti 6200, Georgia</p>
                        <p><strong>Phone:</strong> <a href="tel:+995599061572" style="color:#c8e600;">+995 599 061 572</a></p>
                        <p><strong>Telegram:</strong> <a href="https://t.me/alex_fitness_kobuleti" target="_blank" style="color:#c8e600;">@alex_fitness_kobuleti</a></p>
                        <p><strong>Working hours:</strong> Every day 08:00–22:00</p>
                    </div>
                </div>
            </div>

            <div class="contact-card">
                <h4>Questions or Concerns?</h4>
                <p><strong>Address:</strong> Shota Rustaveli 170-25, Kobuleti 6200, Georgia</p>
                <p><strong>Phone:</strong> <a href="tel:+995599061572">+995 599 061 572</a></p>
                <p><strong>Telegram:</strong> <a href="https://t.me/alex_fitness_kobuleti" target="_blank">@alex_fitness_kobuleti</a></p>
                <p style="margin-top:14px;font-size:0.82rem;color:#666;"><strong>Last Updated:</strong> May 2026</p>
            </div>
        </div>

        <!-- ===== RUSSIAN ===== -->
        <div id="privacy-ru" style="display:none">
            <div class="hero">
                <div class="hero-icon"><i class="fas fa-shield-halved"></i></div>
                <h1 class="hero-title">Политика конфиденциальности</h1>
                <p class="hero-sub">Alex Fitness — Политика обработки персональных данных</p>
                <div class="hero-divider"></div>
            </div>

            <div class="section-card">
                <div class="section-content">
                    <p>Настоящая Политика конфиденциальности объясняет, как <strong>Alex Fitness</strong> собирает, использует и защищает вашу персональную информацию при использовании нашего сайта <strong>alexfitness.ge</strong> или услуг нашего фитнес-клуба.</p>
                    <p>Мы обрабатываем ваши персональные данные в соответствии с требованиями Закона Грузии «О защите персональных данных».</p>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">1</span>Контролёр данных</h2>
                <div class="section-content">
                    <div class="info-box">
                        <p><strong>Компания:</strong> Alex Fitness</p>
                        <p><strong>Адрес:</strong> ул. Шота Руставели 170-25, Кобулети 6200, Грузия</p>
                        <p><strong>Телефон:</strong> +995 599 061 572</p>
                        <p><strong>Telegram:</strong> @alex_fitness_kobuleti</p>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">2</span>Какую информацию мы собираем</h2>
                <div class="section-content">
                    <p>Мы можем собирать и обрабатывать следующие типы персональных данных:</p>
                    <ul>
                        <li><strong>Идентификационная информация:</strong> Имя, фамилия, личный идентификационный номер</li>
                        <li><strong>Контактная информация:</strong> Номер телефона, адрес электронной почты</li>
                        <li><strong>Финансовая информация:</strong> Реквизиты банковской карты, история платежей</li>
                        <li><strong>Техническая информация:</strong> IP-адрес, тип браузера, информация об устройстве</li>
                        <li><strong>Данные тренировок:</strong> История посещений клуба, информация об абонементе</li>
                        <li><strong>Данные о здоровье:</strong> Медицинские справки (при необходимости)</li>
                        <li><strong>Фотографии:</strong> Фото профиля, записи камер видеонаблюдения</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">3</span>Цели обработки данных</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Предоставление услуг:</strong> Оформление абонемента, доступ в клуб</li>
                        <li><strong>Обработка платежей:</strong> Получение и обработка членских взносов</li>
                        <li><strong>Коммуникация:</strong> Отправка важных уведомлений и обновлений</li>
                        <li><strong>Маркетинг:</strong> Информирование о специальных предложениях и новых услугах</li>
                        <li><strong>Безопасность:</strong> Обеспечение безопасности на территории клуба</li>
                        <li><strong>Правовые обязательства:</strong> Соблюдение требований законодательства</li>
                        <li><strong>Аналитика:</strong> Улучшение качества обслуживания</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">4</span>Правовые основания обработки</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Договор:</strong> Для исполнения договора с вами</li>
                        <li><strong>Согласие:</strong> На основании вашего явного согласия</li>
                        <li><strong>Правовое обязательство:</strong> Для соблюдения требований законодательства</li>
                        <li><strong>Законный интерес:</strong> Для защиты наших законных деловых интересов</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">5</span>Передача данных третьим лицам</h2>
                <div class="section-content">
                    <p>Мы не продаём и не передаём ваши персональные данные третьим лицам, за исключением следующих случаев:</p>
                    <ul>
                        <li><strong>Правовое обязательство:</strong> По запросу государственных органов</li>
                        <li><strong>Поставщики услуг:</strong> Наши доверенные партнёры (банковские услуги, IT-поддержка)</li>
                        <li><strong>Безопасность:</strong> Для защиты клуба и членов клуба</li>
                        <li><strong>Согласие:</strong> С вашего явного согласия</li>
                    </ul>
                    <div class="info-box">
                        <p><strong>Важно:</strong> Все третьи лица, которым мы передаём ваши данные, обязаны соблюдать конфиденциальность данных.</p>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">6</span>Сроки хранения данных</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Во время активного членства:</strong> На протяжении всего срока абонемента</li>
                        <li><strong>После прекращения членства:</strong> В течение 3 лет (для бухгалтерского учёта)</li>
                        <li><strong>Маркетинговые цели:</strong> До отзыва вашего согласия</li>
                        <li><strong>Видеозаписи безопасности:</strong> В течение 30 дней</li>
                        <li><strong>Требования законодательства:</strong> В течение срока, установленного законом</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">7</span>Безопасность данных</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Шифрование:</strong> При передаче и хранении данных</li>
                        <li><strong>Контроль доступа:</strong> Только для авторизованного персонала</li>
                        <li><strong>Регулярный мониторинг:</strong> Проверка систем безопасности</li>
                        <li><strong>Обучение персонала:</strong> По вопросам защиты данных</li>
                        <li><strong>Физическая безопасность:</strong> Защита серверов и устройств</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">8</span>Ваши права</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Право на доступ:</strong> Получение информации об обрабатываемых данных</li>
                        <li><strong>Право на исправление:</strong> Исправление некорректных данных</li>
                        <li><strong>Право на удаление:</strong> Запрос на удаление данных</li>
                        <li><strong>Право на ограничение:</strong> Ограничение обработки определённых данных</li>
                        <li><strong>Переносимость данных:</strong> Получение ваших данных в машиночитаемом формате</li>
                        <li><strong>Право на возражение:</strong> Прекращение обработки в маркетинговых целях</li>
                        <li><strong>Право на жалобу:</strong> Подача жалобы в надзорный орган</li>
                    </ul>
                    <div class="info-box">
                        <p><strong>Для реализации прав</strong> свяжитесь с нами в Telegram: <a href="https://t.me/alex_fitness_kobuleti" target="_blank" style="color:#c8e600;">@alex_fitness_kobuleti</a></p>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">9</span>Файлы cookie</h2>
                <div class="section-content">
                    <ul>
                        <li><strong>Обязательные cookie:</strong> Для работы сайта</li>
                        <li><strong>Функциональные cookie:</strong> Для улучшения пользовательского опыта</li>
                        <li><strong>Аналитические cookie:</strong> Для анализа трафика</li>
                        <li><strong>Маркетинговые cookie:</strong> Для показа релевантных предложений</li>
                    </ul>
                    <p>Вы можете управлять настройками cookie в параметрах вашего браузера.</p>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">10</span>Инциденты безопасности</h2>
                <div class="section-content">
                    <ul>
                        <li>Незамедлительно примем меры по остановке инцидента</li>
                        <li>Оценим риски и последствия</li>
                        <li>Своевременно уведомим вас, если инцидент затронет ваши данные</li>
                        <li>Уведомим соответствующие надзорные органы</li>
                        <li>Реализуем корректирующие меры для предотвращения подобных инцидентов в будущем</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">11</span>Изменения политики</h2>
                <div class="section-content">
                    <ul>
                        <li>Обновлённая версия будет опубликована на нашем сайте</li>
                        <li>О существенных изменениях мы уведомим вас через Telegram</li>
                        <li>Новая политика вступает в силу с даты её публикации</li>
                    </ul>
                </div>
            </div>

            <div class="section-card">
                <h2 class="section-title"><span class="section-num">12</span>Контакты</h2>
                <div class="section-content">
                    <div class="info-box">
                        <p><strong>Компания:</strong> Alex Fitness</p>
                        <p><strong>Адрес:</strong> ул. Шота Руставели 170-25, Кобулети 6200, Грузия</p>
                        <p><strong>Телефон:</strong> <a href="tel:+995599061572" style="color:#c8e600;">+995 599 061 572</a></p>
                        <p><strong>Telegram:</strong> <a href="https://t.me/alex_fitness_kobuleti" target="_blank" style="color:#c8e600;">@alex_fitness_kobuleti</a></p>
                        <p><strong>Часы работы:</strong> Ежедневно 08:00–22:00</p>
                    </div>
                </div>
            </div>

            <div class="contact-card">
                <h4>Есть вопросы?</h4>
                <p><strong>Адрес:</strong> ул. Шота Руставели 170-25, Кобулети 6200, Грузия</p>
                <p><strong>Телефон:</strong> <a href="tel:+995599061572">+995 599 061 572</a></p>
                <p><strong>Telegram:</strong> <a href="https://t.me/alex_fitness_kobuleti" target="_blank">@alex_fitness_kobuleti</a></p>
                <p style="margin-top:14px;font-size:0.82rem;color:#666;"><strong>Последнее обновление:</strong> Май 2026</p>
            </div>
        </div>

    </div><!-- /page-wrap -->

    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
    (function() {
        var LANGS = ['ka','en','ru'];

        function getLang() {
            var params = new URLSearchParams(window.location.search);
            var p = params.get('lang');
            if (LANGS.indexOf(p) !== -1) return p;
            var s = localStorage.getItem('ActiveLanguage');
            if (LANGS.indexOf(s) !== -1) return s;
            return 'ka';
        }

        function applyLang(lang) {
            document.documentElement.lang = lang;
            LANGS.forEach(function(l) {
                var el = document.getElementById('privacy-' + l);
                if (el) el.style.display = (l === lang) ? '' : 'none';
                var btn = document.getElementById('btn-' + l);
                if (btn) btn.classList.toggle('active', l === lang);
            });
            var backText = document.getElementById('back-text');
            if (backText) {
                backText.textContent = lang === 'en' ? 'Back to Home' : lang === 'ru' ? 'На главную' : 'მთავარ გვერდზე დაბრუნება';
            }
        }

        applyLang(getLang());

        LANGS.forEach(function(l) {
            var btn = document.getElementById('btn-' + l);
            if (btn) btn.addEventListener('click', function() {
                localStorage.setItem('ActiveLanguage', l);
                applyLang(l);
                var url = new URL(window.location.href);
                url.searchParams.set('lang', l);
                history.replaceState(null, '', url.toString());
            });
        });
    })();
    </script>
</body>
</html>
