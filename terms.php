<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Alex Fitness</title>

    <link rel="stylesheet" href="css/bootstrap.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css" crossorigin="anonymous">

    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        .terms-container {
            background: rgba(0, 0, 0, 0.85);
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            padding: 2rem;
            margin: 2rem 0;
            border: 1px solid #c8e600;
        }
        .terms-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #c8e600;
        }
        .terms-title {
            color: #c8e600;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .terms-subtitle {
            color: #cccccc;
            font-size: 1.1rem;
            font-weight: 400;
        }
        .terms-section { margin-bottom: 2rem; }
        .section-title {
            color: #c8e600;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-left: 10px;
            border-left: 4px solid #c8e600;
        }
        .section-content {
            color: #e0e0e0;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 1rem;
        }
        .section-content p { margin-bottom: 1rem; }
        .section-content ul { padding-left: 2rem; margin-bottom: 1rem; }
        .section-content li { margin-bottom: 0.5rem; }
        .back-btn {
            background: linear-gradient(45deg, #c8e600, #a8c200);
            color: #000000;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }
        .back-btn:hover {
            background: linear-gradient(45deg, #a8c200, #1a73e8);
            color: #000000;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 223, 6, 0.3);
        }
        .back-btn i { margin-right: 8px; }
        .highlight-box {
            background: rgba(255, 223, 6, 0.1);
            border: 1px solid #c8e600;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .contact-info {
            background: rgba(255, 223, 6, 0.05);
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }
        .contact-info h4 { color: #c8e600; margin-bottom: 1rem; }
        .contact-info p { margin-bottom: 0.5rem; color: #e0e0e0; }
        .contact-info a { color: #c8e600; text-decoration: none; }
        .contact-info a:hover { color: #a8c200; text-decoration: underline; }
        @media (max-width: 768px) {
            body { overflow-x: hidden; }
            .container { padding-left: 12px; padding-right: 12px; }
            .terms-container { margin: 0.5rem 0; padding: 1rem; border-radius: 10px; }
            .terms-title { font-size: 1.6rem; letter-spacing: 0; }
            .terms-subtitle { font-size: 0.9rem; }
            .section-title { font-size: 1.1rem; }
            .section-content { font-size: 0.9rem; }
            .highlight-box { padding: 1rem; }
            .back-btn { padding: 10px 20px; font-size: 0.95rem; margin-bottom: 1rem; }
            .d-flex.justify-content-between { flex-direction: column; align-items: flex-start; gap: 8px; }
            .section-content ul { padding-left: 1.2rem; }
        }
        .lang-toggle { display: inline-flex; gap: 8px; align-items: center; margin-bottom: 1rem; }
        .lang-btn {
            background: rgba(255, 223, 6, 0.15);
            color: #c8e600;
            border: 1px solid #c8e600;
            border-radius: 6px;
            padding: 6px 10px;
            font-weight: 600;
            cursor: pointer;
        }
        .lang-btn.active { background: #c8e600; color: #000; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,1px,1px); white-space: nowrap; border: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <a href="index.php" class="back-btn" id="back-link">
                        <i class="fas fa-arrow-left"></i>
                        <span id="back-text">მთავარ გვერდზე დაბრუნება</span>
                    </a>
                    <div class="lang-toggle" aria-label="Language Selector">
                        <span class="sr-only" id="lang-label">Language</span>
                        <button type="button" class="lang-btn active" id="btn-ka" aria-labelledby="lang-label">KA</button>
                        <button type="button" class="lang-btn" id="btn-en" aria-labelledby="lang-label">EN</button>
                        <button type="button" class="lang-btn" id="btn-ru" aria-labelledby="lang-label">RU</button>
                    </div>
                </div>

                <div class="terms-container">

                    <!-- ================== GEORGIAN ================== -->
                    <div id="terms-ka">
                        <div class="terms-header">
                            <h1 class="terms-title">წესები და პირობები</h1>
                            <p class="terms-subtitle">Alex Fitness-ის ვებ-გვერდისა და აპლიკაციის გამოყენების წესები და პირობები</p>
                        </div>

                        <div class="terms-section">
                            <div class="section-content">
                                <p>წინამდებარე წესები და პირობები განსაზღვრავს Alex Fitness-ის ვებ-გვერდისა და აპლიკაციის გამოყენების წესებს (შემდგომში, „წესები და პირობები"). მათზე დათანხმებით, თქვენზე გავრცელდება აღნიშნული წესებისა და პირობების მოქმედება.</p>
                                <p>იმ შემთხვევაში თუ თქვენ არ ეთანხმებით წესებსა და პირობებს და არ გსურთ, რომ თქვენზე გავრცელდეს, არ დააჭიროთ დათანხმების ღილაკს და არ გამოიყენოთ ვებ-გვერდსა და აპლიკაციაში განთავსებული ჩვენი სერვისები.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">მუხლი 1. ტერმინთა განმარტება</h2>
                            <div class="section-content">
                                <p>თუ თავად ტექსტიდან პირდაპირ სხვაგვარად არ გამომდინარეობს, წინამდებარე წესებსა და პირობებში გამოყენებულ ტერმინებს აქვთ შემდეგი მნიშვნელობა:</p>
                                <div class="highlight-box">
                                    <p><strong>სპორტული კლუბი:</strong> Alex Fitness</p>
                                    <p><strong>ვებ-გვერდი:</strong> alexfitness.ge</p>
                                </div>
                                <ul>
                                    <li><strong>მომსახურების მიმწოდებელი:</strong> Alex Fitness</li>
                                    <li><strong>მისამართი:</strong> შოთა რუსთაველი 170-25, ქობულეთი 6200</li>
                                    <li><strong>სამუშაო საათები:</strong> ყოველდღე 08:00–02:00</li>
                                    <li><strong>მიწოდების პირობები:</strong> განისაზღვრება საიტზე მითითებული სტანდარტული პაკეტების შესაბამისად, ასევე მიმდინარე საქციო პროდუქტებიდან გამომდინარე</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">კლუბის წესები</h2>
                            <div class="section-content">
                                <p>მოგესალმებით! Alex Fitness-ის კლუბის წევრად მიღების შესახებ განაცხადის ხელმოწერამდე, გთხოვთ გაეცნოთ შემდეგ წესებს:</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">1. შესავალი</h2>
                            <div class="section-content">
                                <p>ამ კონტრაქტის ხელმოწერით, კლუბის წევრი თანხმობას აცხადებს დაიცვას და შეასრულოს წინამდებარე წესები და პირობები.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">2. წევრის ჯანმრთელობის გარანტია</h2>
                            <div class="section-content">
                                <p>კლუბის წევრი გარანტიას იძლევა და აცხადებს, რომ ის არ არის შეზღუდული შესაძლებლობების, არ არის ავად და იმ მდგომარეობაში, რომელიც მას ხელს შეუშლის ვარჯიშების შესრულებაში და რომ ასეთი მოქმედება არ მიაყენებს ზიანს და არ მოახდენს უარყოფით ზემოქმედებას მის ჯანმრთელობაზე, უსაფრთხოებაზე ან ფიზიკურ მდგომარეობაზე.</p>
                                <p>კლუბის წევრისთვის ცნობილია და ის თანახმაა, რომ:</p>
                                <ul>
                                    <li><strong>2.1.</strong> კლუბის წევრის მიღებისას, Alex Fitness დაეყრდნობა წევრის ზემოთ აღნიშნულ განცხადებასა და გარანტიას და არ არის ვალდებული განახორციელოს რაიმე სახის ფიზიკური ფორმის შეფასება.</li>
                                    <li><strong>2.2.</strong> თუ Alex Fitness ჩაატარებს რაიმე სახის შეფასებას ან სხვა მსგავს ტესტირებას, ეს განხორციელებულ იქნება მხოლოდ მონაცემების შედარებისთვის, რათა კლუბის წევრმა შეძლოს თვალი მიადევნოს საკუთარ წინსვლას და არა დიაგნოსტირების მიზნებისთვის.</li>
                                    <li><strong>2.3.</strong> Alex Fitness-ს არ შეიძლება წაეყენოს რაიმე მოთხოვნები ან ზარალის ანაზღაურების მოთხოვნა ფიზიკური ფორმის შეფასების შედეგებთან დაკავშირებით.</li>
                                    <li><strong>2.4.</strong> Alex Fitness არ აგებს პასუხს რაიმე ზიანის გამო, რომელიც განპირობებულია კლუბის წევრის ავადმყოფობით ან მდგომარეობით, რომელზეც ვარჯიშებმა შეიძლება უარყოფითად იმოქმედოს.</li>
                                </ul>
                                <div class="highlight-box">
                                    <strong>მნიშვნელოვანი:</strong> თითოეულმა კლუბის წევრმა უნდა გაიაროს კონსულტაცია ექიმთან ვარჯიშის დაწყებამდე ან გაგრძელებამდე, თუ არსებობს ამის სამედიცინო ჩვენებები.
                                </div>
                                <p><strong>2.5.</strong> Alex Fitness პასუხს არ აგებს წევრის მდგომარეობაზე, რომელიც გამოწვეულია სპორტული კვების ან საკვები დანამატების გამოყენებით. წევრი კისრულობს ვალდებულებას გაიაროს კონსულტაციები ექიმთან საკვები დანამატების გამოყენებამდე.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">3. კლუბის წევრების დაშვება</h2>
                            <div class="section-content">
                                <p>კლუბის წევრებზე გაიცემა დაშვებისათვის ბარათი ან QR კოდი, რომლებიც საშუალებას მისცემს წევრებს შევიდნენ Alex Fitness-ის ტერიტორიაზე და ისარგებლონ აღჭურვილობით.</p>
                                <p>კლუბის წევრი თანახმაა, არ მისცეს სხვა პირებს შენობაში თავისი ბარათის ან QR კოდის მეშვეობით შესვლის ნება.</p>
                                <div class="highlight-box">
                                    <strong>გაფრთხილება:</strong> თუ კლუბის წევრი დაარღვევს წევრების დაშვების პირობებს, მას შეიძლება გაუუქმდეს წევრობა ყოველგვარი ანაზღაურების გარეშე და შეიძლება აღიძრას სისხლის სამართლის საქმე მის წინააღმდეგ.
                                </div>
                                <p>ბარათის შეცვლის ღირებულება განისაზღვრება მიმდინარე ფიქსირებული ტარიფის შესაბამისად. Alex Fitness-ის კლუბის წევრად გახდომისას პირს შეიძლება გადაუღონ ფოტო, რომელიც უსაფრთხოების მიზნებისთვის გამოიყენება.</p>
                                <p>16 წლამდე ასაკის ბავშვები სპორტდარბაზში დაიშვებიან მხოლოდ მშობლის ან კანონიერი წარმომადგენლის წერილობითი თანხმობის შემთხვევაში. ასევე, 16 წლამდე ასაკის ბავშვებისთვის დარბაზში დაშვება შესაძლებელია პერსონალური ტრენერის აყვანის შემთხვევაშიც.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">4. აღჭურვილობის გამოყენება</h2>
                            <div class="section-content">
                                <p>ვთხოვთ კლუბის წევრებს ვარჯიშის დასრულების შემდეგ ადგილზე დააბრუნონ გირები და სხვა ნებისმიერი სავარჯიშო აღჭურვილობა (ფილები, ხარიხები, ჰანტელები და სხვ.).</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">6. კარადები</h2>
                            <div class="section-content">
                                <p>კარადები ხელმისაწვდომია და განთავსებულია გასახდელებში. წევრი ვალდებულია ყურადღება მიაქციოს საკუთარ პირად ნივთებს.</p>
                                <div class="highlight-box">
                                    <strong>მნიშვნელოვანი:</strong> Alex Fitness-ის თანამშრომლები პასუხს არ აგებენ პირადი ნივთების დაკარგვისთვის. კარადები არ წარმოადგენს მოწყობილობებს უსაფრთხოდ შენახვისთვის.
                                </div>
                                <p>შესაბამისად, წევრი ვალდებულია კლუბის მომსახურებით სარგებლობისას თან იქონიოს ყველა ფასეული ნივთი. Alex Fitness პასუხს არ აგებს ასეთი ნივთების დაკარგვაზე ან/და დაზიანებაზე.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">7. პირდაპირი მარკეტინგის მიზნებისთვის პერსონალურ მონაცემთა დამუშავებაზე თანხმობა</h2>
                            <div class="section-content">
                                <p><strong>7.1.</strong> წინამდებარე ხელშეკრულებაზე ხელმოწერით წევრი თანხმობას აცხადებს, რომ Alex Fitness-მა მისი პირადი მონაცემები (სახელი, მისამართი, ტელეფონის ნომერი, ელ. ფოსტა) დაამუშაოს პირდაპირი მარკეტინგის მიზნებისათვის და გაუგზავნოს ინფორმაცია Alex Fitness-ის შეთავაზებების, ფასდაკლებების, აქციებისა და ღონისძიებების შესახებ.</p>
                                <p><strong>7.2.</strong> წევრისთვის ცნობილია, რომ ნებისმიერ დროს შეუძლია უარი განაცხადოს და უკან გამოითხოვოს წინამდებარე დოკუმენტით გაცხადებული თანხმობა.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">8. წევრთა უფლებები</h2>
                            <div class="section-content">
                                <p>Alex Fitness-ის კლუბის წევრობა წევრს უფლებას აძლევს ივარჯიშოს შეძენილი აბონემენტით განსაზღვრული ვადით და პირობით.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">9. წევრის ვალდებულებები</h2>
                            <div class="section-content">
                                <p>Alex Fitness-ის კლუბის წევრს მოეთხოვება:</p>
                                <ul>
                                    <li><strong>9.1.</strong> დაიცვას კლუბის ყველა წესი და წინამდებარე ხელშეკრულების პირობები;</li>
                                    <li><strong>9.2.</strong> გადაიხადოს ყველა შეთანხმებული საფასური;</li>
                                    <li><strong>9.3.</strong> წინასწარ აცნობოს Alex Fitness-ს, თუ არსებობს რაიმე სახის დაავადება ან რისკი კლუბის წევრის ჯანმრთელობისთვის.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">10. აკრძალული მოქმედებები</h2>
                            <div class="section-content">
                                <p>შენობაში აკრძალულია მოწევა, ალკოჰოლის მიღება და ნარკოტიკების (მათ შორის სტეროიდების) მოხმარება. დაუშვებელია რაიმე სახის იარაღის შეტანა.</p>
                                <div class="highlight-box">
                                    <strong>მნიშვნელოვანი:</strong> კლუბის წევრი თანახმაა არ განახორციელოს რაიმე მოქმედებები Alex Fitness-ის შენობაში ისეთი ნივთიერებების ზემოქმედების დროს, რომლებსაც შეუძლია დააქვეითოს მისი უნარი მართოს აღჭურვილობა.
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">11. შეწყვეტა</h2>
                            <div class="section-content">
                                <p>კლუბის წევრობა შეიძლება შეწყდეს ვადამდე წევრის ხანგრძლივი ავადმყოფობის ან ფიზიკური მდგომარეობის გამო. Alex Fitness იტოვებს უფლებას შეწყვიტოს წევრის კონტრაქტი:</p>
                                <ul>
                                    <li><strong>11.1.</strong> წევრის შეუსაბამობის შემთხვევაში წესებში ან/და ხელშეკრულებაში მითითებულ კრიტერიუმებთან;</li>
                                    <li><strong>11.2.</strong> კლუბის წევრის ან მისი თანამშრომლების საჩივრის საფუძველზე.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">12. აბონემენტის საფასურის დაბრუნება</h2>
                            <div class="section-content">
                                <p>კლუბის წევრის მოთხოვნის საფუძველზე შესაძლებელია საბანკო ანგარიშზე დაბრუნდეს მხოლოდ ფასდაუკლებელი ტარიფით შეძენილი აბონემენტის საფასური. დაბრუნება ხდება მოთხოვნიდან 7 (შვიდი) სამუშაო დღის ვადაში.</p>
                                <div class="highlight-box">
                                    <strong>მნიშვნელოვანი:</strong> თანხის დაბრუნების შემთხვევაში გადახდილ თანხას აკლდება აბონემენტის შეძენიდან დაბრუნების მოთხოვნის დღემდე გასული დღეების პროპორციული თანხა, ხოლო სხვაობა ბრუნდება საბანკო ანგარიშზე. (ყოველ 1 თვეზე იანგარიშება 30 დღიანი ლიმიტი)
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">13. პასუხისმგებლობა</h2>
                            <div class="section-content">
                                <p>Alex Fitness არ აგებს პასუხს მისგან დამოუკიდებელი მიზეზების გამო ხელშეკრულების პირობების შეუსრულებლობაზე.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">14. ეგზემპლარები</h2>
                            <div class="section-content">
                                <p>კონტრაქტი შეიძლება გაფორმდეს ორ ან მეტ ეგზემპლარად, რომელთაგან თითოეული ორიგინალად უნდა ჩაითვალოს; ყველა ეგზემპლარი ერთად წარმოადგენს ერთსა და იმავე შეთანხმებას.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">15. მარეგულირებელი კანონმდებლობა</h2>
                            <div class="section-content">
                                <p>ხელშეკრულება და ყველა მითითებული დოკუმენტი რეგულირდება საქართველოს კანონმდებლობით.</p>
                            </div>
                        </div>

                        <div class="terms-section" style="border-top: 2px solid #c8e600; padding-top: 2rem; margin-top: 2rem;">
                            <div class="section-content">
                                <div class="highlight-box">
                                    <p style="font-weight: bold; text-align: center; margin-bottom: 0;">
                                        ვადასტურებ, რომ წავიკითხე კონტრაქტი, ჩემთვის გასაგებია ყველა პირობა და ვეთანხმები მათ. ასევე ვადასტურებ, რომ გადმომეცა წევრის კონტრაქტის სრულად გაფორმებული ეგზემპლარი.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-info">
                            <h4>კითხვები გაქვთ?</h4>
                            <p><strong>მისამართი:</strong> შოთა რუსთაველი 170-25, ქობულეთი 6200</p>
                            <p><strong>ტელეფონი:</strong> <a href="tel:+995599061572">+995 599 061 572</a></p>
                            <p><strong>Telegram:</strong> <a href="https://t.me/alex_fitness_kobuleti" target="_blank">@alex_fitness_kobuleti</a></p>
                            <p><strong>ბოლო განახლება:</strong> მაისი 2025</p>
                        </div>
                    </div>

                    <!-- ================== ENGLISH ================== -->
                    <div id="terms-en" style="display:none">
                        <div class="terms-header">
                            <h1 class="terms-title">Terms and Conditions</h1>
                            <p class="terms-subtitle">Rules and conditions for using the Alex Fitness website and application</p>
                        </div>

                        <div class="terms-section">
                            <div class="section-content">
                                <p>These Terms and Conditions set the rules for using the Alex Fitness website and application (hereinafter, the "Terms and Conditions"). By agreeing to them, you accept that these Terms and Conditions apply to you.</p>
                                <p>If you do not agree and do not want these Terms and Conditions to apply, please do not click the consent button and do not use the services provided on the website and in the application.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">Article 1. Definitions</h2>
                            <div class="section-content">
                                <p>Unless the context clearly indicates otherwise, the terms used in these Terms and Conditions have the following meanings:</p>
                                <div class="highlight-box">
                                    <p><strong>Sports Club:</strong> Alex Fitness</p>
                                    <p><strong>Website:</strong> alexfitness.ge</p>
                                </div>
                                <ul>
                                    <li><strong>Service Provider:</strong> Alex Fitness</li>
                                    <li><strong>Address:</strong> Shota Rustaveli 170-25, Kobuleti 6200</li>
                                    <li><strong>Working hours:</strong> Every day 08:00–02:00</li>
                                    <li><strong>Delivery/Provision terms:</strong> Determined by standard packages listed on the site as well as current promotional products</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">Club Rules</h2>
                            <div class="section-content">
                                <p>Welcome! Before signing the application for becoming a club member of Alex Fitness, please read the following rules.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">1. Introduction</h2>
                            <div class="section-content">
                                <p>By signing this contract, the club member agrees to comply with and follow these Terms and Conditions.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">2. Member's Health Warranty</h2>
                            <div class="section-content">
                                <p>The club member guarantees and declares that they are not limited in ability, not ill, and not in a condition that would prevent them from exercising, and that such activity will not harm or adversely affect their health, safety, or physical condition.</p>
                                <p>The club member is aware and agrees that:</p>
                                <ul>
                                    <li><strong>2.1.</strong> Upon admission, Alex Fitness relies on the member's statements and warranty above and is not obliged to carry out any fitness or health assessment.</li>
                                    <li><strong>2.2.</strong> If Alex Fitness conducts any assessment or similar testing, it is only for comparison of data so the member can track progress, not for diagnosis.</li>
                                    <li><strong>2.3.</strong> No claims or compensation requests can be made against Alex Fitness regarding results or interpretation of fitness assessments or similar testing.</li>
                                    <li><strong>2.4.</strong> Alex Fitness is not liable for any harm caused by any illness or condition of a member that may be adversely affected by exercise.</li>
                                </ul>
                                <div class="highlight-box">
                                    <strong>Important:</strong> Each member should consult a doctor before starting or continuing exercise if medically indicated.
                                </div>
                                <p><strong>2.5.</strong> Alex Fitness is not responsible for conditions caused by the use of sports nutrition or supplements. The member undertakes to consult a doctor before using any supplements.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">3. Member Access</h2>
                            <div class="section-content">
                                <p>Members are issued an access card or a QR code that allows entry to Alex Fitness premises and use of equipment.</p>
                                <p>The member agrees not to allow other persons to enter the premises using their card or QR code.</p>
                                <div class="highlight-box">
                                    <strong>Warning:</strong> If a member violates any access conditions, membership may be canceled without any refund and criminal proceedings may be initiated.
                                </div>
                                <p>The card replacement fee is determined by the current fixed tariff. Upon becoming a member of Alex Fitness, a photo may be taken for security and monitoring purposes.</p>
                                <p>Children under the age of 16 may be admitted to the gym only with written consent from a parent or legal guardian. Additionally, children under 16 may be admitted with a personal trainer who ensures their safe and supervised training.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">4. Equipment Use</h2>
                            <div class="section-content">
                                <p>Please return kettlebells and any other exercise equipment (plates, bars, dumbbells, etc.) to their place after use.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">6. Lockers</h2>
                            <div class="section-content">
                                <p>Lockers are available in the changing rooms. Members are responsible for their personal belongings.</p>
                                <div class="highlight-box">
                                    <strong>Important:</strong> Employees of Alex Fitness are not responsible for lost personal items. Lockers are not secure storage devices.
                                </div>
                                <p>Therefore, members must keep valuables with them while using the club. Alex Fitness is not liable for loss or damage of personal items.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">7. Consent to Personal Data Processing for Direct Marketing</h2>
                            <div class="section-content">
                                <p><strong>7.1.</strong> By signing this agreement, the member consents to Alex Fitness processing their personal data (name, address, phone number, email) for direct marketing and sending information about existing, planned, or future offers, discounts, promotions, events, and for informational messages.</p>
                                <p><strong>7.2.</strong> The member knows they can withdraw this consent at any time by notifying Alex Fitness in writing using the same communication channel through which they receive information.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">8. Member Rights</h2>
                            <div class="section-content">
                                <p>Membership in Alex Fitness entitles the member to exercise for the period and under the conditions specified in the purchased membership.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">9. Member Obligations</h2>
                            <div class="section-content">
                                <p>Members of Alex Fitness shall:</p>
                                <ul>
                                    <li><strong>9.1.</strong> Follow all club rules and the terms of this agreement;</li>
                                    <li><strong>9.2.</strong> Pay all agreed fees;</li>
                                    <li><strong>9.3.</strong> Inform Alex Fitness in advance of any disease or risk to health.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">10. Prohibited Actions</h2>
                            <div class="section-content">
                                <p>Smoking, drinking alcohol, and using drugs (including steroids) are prohibited on the premises. Bringing any type of weapon is not allowed.</p>
                                <div class="highlight-box">
                                    <strong>Important:</strong> The member agrees not to perform any actions on the premises of Alex Fitness under the influence of substances that may impair their ability to operate equipment.
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">11. Termination</h2>
                            <div class="section-content">
                                <p>Membership may be terminated early due to long illness or physical condition. Alex Fitness reserves the right to terminate the member's contract under the following conditions:</p>
                                <ul>
                                    <li><strong>11.1.</strong> Non-compliance with any criteria specified in the rules and/or agreement;</li>
                                    <li><strong>11.2.</strong> Based on a complaint from a club member or its employees.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">12. Membership Fee Refund</h2>
                            <div class="section-content">
                                <p>Upon a club member's request, only the fee for a membership purchased at a non-discounted tariff may be refunded to a bank account. The refund is processed within 7 (seven) business days from the date of the request.</p>
                                <div class="highlight-box">
                                    <strong>Important:</strong> In the case of a refund, a proportional amount corresponding to the number of days elapsed from the date of membership purchase to the refund request date is deducted, and the difference is refunded to the bank account. (Each 1-month period is calculated based on a 30-day limit.)
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">13. Liability</h2>
                            <div class="section-content">
                                <p>Alex Fitness is not liable for non-performance caused by reasons beyond its control.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">14. Counterparts</h2>
                            <div class="section-content">
                                <p>The contract may be signed in two or more counterparts, each of which is deemed an original, and all counterparts together constitute the same agreement.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">15. Governing Law</h2>
                            <div class="section-content">
                                <p>The agreement and all referenced documents are governed by the laws of Georgia.</p>
                            </div>
                        </div>

                        <div class="terms-section" style="border-top: 2px solid #c8e600; padding-top: 2rem; margin-top: 2rem;">
                            <div class="section-content">
                                <div class="highlight-box">
                                    <p style="font-weight: bold; text-align: center; margin-bottom: 0;">
                                        I confirm that I have read the contract, understand all terms, and agree to them. I also confirm that I received a fully executed copy of the membership contract.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-info">
                            <h4>Questions or Concerns?</h4>
                            <p><strong>Address:</strong> Shota Rustaveli 170-25, Kobuleti 6200</p>
                            <p><strong>Phone:</strong> <a href="tel:+995599061572">+995 599 061 572</a></p>
                            <p><strong>Telegram:</strong> <a href="https://t.me/alex_fitness_kobuleti" target="_blank">@alex_fitness_kobuleti</a></p>
                            <p><strong>Last Updated:</strong> May 2025</p>
                        </div>
                    </div>

                    <!-- ================== RUSSIAN ================== -->
                    <div id="terms-ru" style="display:none">
                        <div class="terms-header">
                            <h1 class="terms-title">Условия пользования</h1>
                            <p class="terms-subtitle">Правила и условия использования сайта и приложения Alex Fitness</p>
                        </div>

                        <div class="terms-section">
                            <div class="section-content">
                                <p>Настоящие Условия пользования устанавливают правила использования сайта и приложения Alex Fitness (далее — «Условия пользования»). Принимая их, вы соглашаетесь на применение данных Условий в отношении вас.</p>
                                <p>Если вы не согласны с Условиями пользования и не хотите, чтобы они распространялись на вас, — не нажимайте кнопку согласия и не пользуйтесь нашими сервисами на сайте и в приложении.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">Статья 1. Определения</h2>
                            <div class="section-content">
                                <p>Если из контекста прямо не следует иное, термины, используемые в настоящих Условиях пользования, имеют следующие значения:</p>
                                <div class="highlight-box">
                                    <p><strong>Спортивный клуб:</strong> Alex Fitness</p>
                                    <p><strong>Веб-сайт:</strong> alexfitness.ge</p>
                                </div>
                                <ul>
                                    <li><strong>Поставщик услуг:</strong> Alex Fitness</li>
                                    <li><strong>Адрес:</strong> ул. Шота Руставели 170-25, Кобулети 6200</li>
                                    <li><strong>Режим работы:</strong> Ежедневно 08:00–02:00</li>
                                    <li><strong>Условия предоставления услуг:</strong> Определяются стандартными пакетами, указанными на сайте, а также текущими акционными предложениями</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">Правила клуба</h2>
                            <div class="section-content">
                                <p>Добро пожаловать! Прежде чем подписать заявление о вступлении в клуб Alex Fitness, пожалуйста, ознакомьтесь со следующими правилами.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">1. Введение</h2>
                            <div class="section-content">
                                <p>Подписывая настоящий договор, член клуба соглашается соблюдать и выполнять настоящие Условия пользования.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">2. Гарантия здоровья члена клуба</h2>
                            <div class="section-content">
                                <p>Член клуба гарантирует и заявляет, что не имеет ограниченных возможностей, не болен и не находится в состоянии, которое препятствовало бы выполнению упражнений, и что занятия не причинят вреда его здоровью, безопасности или физическому состоянию.</p>
                                <p>Член клуба осведомлён и согласен с тем, что:</p>
                                <ul>
                                    <li><strong>2.1.</strong> При приёме в клуб Alex Fitness опирается на вышеуказанные заявления и гарантии члена клуба и не обязан проводить какую-либо оценку физической формы.</li>
                                    <li><strong>2.2.</strong> Если Alex Fitness проводит оценку или тестирование, это делается исключительно для сравнения данных, чтобы член клуба мог отслеживать свой прогресс, а не в диагностических целях.</li>
                                    <li><strong>2.3.</strong> К Alex Fitness не могут быть предъявлены какие-либо требования или претензии о возмещении ущерба в связи с результатами оценки физической формы.</li>
                                    <li><strong>2.4.</strong> Alex Fitness не несёт ответственности за любой вред, причинённый состоянием здоровья члена клуба, на которое упражнения могут оказать негативное воздействие.</li>
                                </ul>
                                <div class="highlight-box">
                                    <strong>Важно:</strong> Каждый член клуба должен проконсультироваться с врачом перед началом или возобновлением тренировок при наличии медицинских показаний.
                                </div>
                                <p><strong>2.5.</strong> Alex Fitness не несёт ответственности за состояние здоровья, вызванное применением спортивного питания или пищевых добавок. Член клуба обязуется проконсультироваться с врачом перед их использованием.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">3. Доступ членов клуба</h2>
                            <div class="section-content">
                                <p>Членам клуба выдаётся карта доступа или QR-код, позволяющие войти на территорию Alex Fitness и пользоваться оборудованием.</p>
                                <p>Член клуба соглашается не предоставлять другим лицам доступ в помещение по своей карте или QR-коду.</p>
                                <div class="highlight-box">
                                    <strong>Предупреждение:</strong> В случае нарушения членом клуба условий доступа членство может быть аннулировано без какого-либо возмещения, а также может быть возбуждено уголовное дело.
                                </div>
                                <p>Стоимость замены карты определяется действующим фиксированным тарифом. При вступлении в Alex Fitness может быть сделана фотография в целях безопасности и мониторинга.</p>
                                <p>Дети до 16 лет допускаются в зал только при наличии письменного согласия родителя или законного представителя. Кроме того, дети до 16 лет могут быть допущены при наличии персонального тренера, обеспечивающего безопасные и контролируемые тренировки.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">4. Использование оборудования</h2>
                            <div class="section-content">
                                <p>Просим членов клуба возвращать гири и любое другое тренажёрное оборудование (диски, штанги, гантели и т. д.) на место после завершения тренировки.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">6. Шкафчики</h2>
                            <div class="section-content">
                                <p>Шкафчики расположены в раздевалках. Член клуба обязан следить за своими личными вещами.</p>
                                <div class="highlight-box">
                                    <strong>Важно:</strong> Сотрудники Alex Fitness не несут ответственности за утерю личных вещей. Шкафчики не являются устройствами для безопасного хранения.
                                </div>
                                <p>В связи с этим член клуба обязан держать все ценные вещи при себе во время пользования клубом. Alex Fitness не несёт ответственности за утерю или повреждение личных вещей.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">7. Согласие на обработку персональных данных в целях прямого маркетинга</h2>
                            <div class="section-content">
                                <p><strong>7.1.</strong> Подписывая настоящее соглашение, член клуба даёт согласие на обработку Alex Fitness его персональных данных (имя, адрес, номер телефона, адрес электронной почты) в целях прямого маркетинга и получение информации о предложениях, скидках, акциях и мероприятиях Alex Fitness.</p>
                                <p><strong>7.2.</strong> Член клуба вправе в любое время отозвать данное согласие, уведомив об этом Alex Fitness в письменной форме через тот же канал связи, по которому он получает информацию.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">8. Права членов клуба</h2>
                            <div class="section-content">
                                <p>Членство в Alex Fitness даёт право на посещение зала в течение срока и на условиях приобретённого абонемента.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">9. Обязательства члена клуба</h2>
                            <div class="section-content">
                                <p>Член клуба Alex Fitness обязан:</p>
                                <ul>
                                    <li><strong>9.1.</strong> Соблюдать все правила клуба и условия настоящего соглашения;</li>
                                    <li><strong>9.2.</strong> Оплачивать все согласованные взносы;</li>
                                    <li><strong>9.3.</strong> Заблаговременно уведомлять Alex Fitness о наличии каких-либо заболеваний или рисков для здоровья.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">10. Запрещённые действия</h2>
                            <div class="section-content">
                                <p>На территории клуба запрещается курение, употребление алкоголя и наркотиков (включая стероиды). Запрещается вносить оружие любого вида.</p>
                                <div class="highlight-box">
                                    <strong>Важно:</strong> Член клуба соглашается не совершать каких-либо действий на территории Alex Fitness под воздействием веществ, способных снизить его способность управлять оборудованием.
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">11. Расторжение договора</h2>
                            <div class="section-content">
                                <p>Членство в клубе может быть досрочно прекращено в связи с длительной болезнью или физическим состоянием члена клуба. Alex Fitness оставляет за собой право расторгнуть договор с членом клуба при следующих условиях:</p>
                                <ul>
                                    <li><strong>11.1.</strong> Несоответствие члена клуба критериям, указанным в правилах и/или договоре;</li>
                                    <li><strong>11.2.</strong> На основании жалобы от члена клуба или сотрудников.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">12. Возврат абонементного взноса</h2>
                            <div class="section-content">
                                <p>По запросу члена клуба возможен возврат на банковский счёт только суммы, уплаченной за абонемент по недисконтированному тарифу. Возврат осуществляется в течение 7 (семи) рабочих дней с даты обращения.</p>
                                <div class="highlight-box">
                                    <strong>Важно:</strong> При возврате из уплаченной суммы вычитается пропорциональная сумма за количество дней, прошедших с даты приобретения абонемента до даты подачи заявки на возврат, а разница возвращается на банковский счёт. (Каждый 1 месяц рассчитывается из расчёта 30-дневного лимита.)
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">13. Ответственность</h2>
                            <div class="section-content">
                                <p>Alex Fitness не несёт ответственности за неисполнение условий договора по причинам, не зависящим от него.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">14. Экземпляры договора</h2>
                            <div class="section-content">
                                <p>Договор может быть подписан в двух и более экземплярах, каждый из которых считается оригиналом; все экземпляры вместе составляют одно и то же соглашение.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">15. Применимое законодательство</h2>
                            <div class="section-content">
                                <p>Настоящий договор и все упомянутые в нём документы регулируются законодательством Грузии.</p>
                            </div>
                        </div>

                        <div class="terms-section" style="border-top: 2px solid #c8e600; padding-top: 2rem; margin-top: 2rem;">
                            <div class="section-content">
                                <div class="highlight-box">
                                    <p style="font-weight: bold; text-align: center; margin-bottom: 0;">
                                        Подтверждаю, что прочитал(-а) договор, понимаю все условия и согласен(-на) с ними. Также подтверждаю, что получил(-а) полностью оформленный экземпляр договора о членстве.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-info">
                            <h4>Вопросы или обращения?</h4>
                            <p><strong>Адрес:</strong> ул. Шота Руставели 170-25, Кобулети 6200</p>
                            <p><strong>Телефон:</strong> <a href="tel:+995599061572">+995 599 061 572</a></p>
                            <p><strong>Telegram:</strong> <a href="https://t.me/alex_fitness_kobuleti" target="_blank">@alex_fitness_kobuleti</a></p>
                            <p><strong>Последнее обновление:</strong> Май 2025</p>
                        </div>
                    </div>

                </div><!-- /terms-container -->
            </div>
        </div>
    </div>

    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
    (function() {
        function getParam(name) {
            return new URLSearchParams(window.location.search).get(name);
        }
        function getLang() {
            const fromParam = getParam('lang');
            if (['ka','en','ru'].includes(fromParam)) return fromParam;
            const fromStorage = localStorage.getItem('ActiveLanguage');
            if (['ka','en','ru'].includes(fromStorage)) return fromStorage;
            return 'ka';
        }
        function applyLang(lang) {
            document.documentElement.lang = lang;
            document.getElementById('terms-ka').style.display = (lang === 'ka') ? '' : 'none';
            document.getElementById('terms-en').style.display = (lang === 'en') ? '' : 'none';
            document.getElementById('terms-ru').style.display = (lang === 'ru') ? '' : 'none';

            const backTexts = { ka: 'მთავარ გვერდზე დაბრუნება', en: 'Back to Home', ru: 'На главную' };
            document.getElementById('back-text').textContent = backTexts[lang] || backTexts.ka;

            ['ka','en','ru'].forEach(function(l) {
                var btn = document.getElementById('btn-' + l);
                if (btn) btn.classList.toggle('active', l === lang);
            });
        }
        var current = getLang();
        applyLang(current);

        ['ka','en','ru'].forEach(function(l) {
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
