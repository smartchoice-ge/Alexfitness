<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - Synergy Gym</title>
    
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

        .terms-section {
            margin-bottom: 2rem;
        }

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

        .section-content p {
            margin-bottom: 1rem;
        }

        .section-content ul {
            padding-left: 2rem;
            margin-bottom: 1rem;
        }

        .section-content li {
            margin-bottom: 0.5rem;
        }

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

        .back-btn i {
            margin-right: 8px;
        }

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

        .contact-info h4 {
            color: #c8e600;
            margin-bottom: 1rem;
        }

        .contact-info p {
            margin-bottom: 0.5rem;
            color: #e0e0e0;
        }

        .contact-info a {
            color: #c8e600;
            text-decoration: none;
        }

        .contact-info a:hover {
            color: #a8c200;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .terms-container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .terms-title {
                font-size: 2rem;
            }
            
            .section-title {
                font-size: 1.3rem;
            }
        }

        /* Language toggle */
        .lang-toggle {
            display: inline-flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 1rem;
        }
        .lang-btn {
            background: rgba(255, 223, 6, 0.15);
            color: #c8e600;
            border: 1px solid #c8e600;
            border-radius: 6px;
            padding: 6px 10px;
            font-weight: 600;
            cursor: pointer;
        }
        .lang-btn.active {
            background: #c8e600;
            color: #000;
        }
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
                        <span id="back-text">Back to Home</span>
                    </a>
                    <div class="lang-toggle" aria-label="Language Selector">
                        <span class="sr-only" id="lang-label">Language</span>
                        <button type="button" class="lang-btn active" id="btn-ka" aria-labelledby="lang-label">KA</button>
                        <button type="button" class="lang-btn" id="btn-en" aria-labelledby="lang-label">EN</button>
                    </div>
                </div>
                
                <div class="terms-container">
                    <!-- Georgian Content -->
                    <div id="terms-ka">
                    <div class="terms-header">
                        <h1 class="terms-title">წესები და პირობები</h1>
                        <p class="terms-subtitle">Synergy Gym-ის ვებ-გვერდისა და აპლიკაციის გამოყენების წესები და პირობები</p>
                    </div>

                    <div class="terms-section">
                        <div class="section-content">
                            <p>წინამდებარე წესები და პირობები წარმოადგენს Synergy Gym-ის ვებ-გვერდისა და აპლიკაციის გამოყენების წესებსა და პირობებს (შემდგომში, „წესები და პირობები"). მათზე დათანხმებით, თქვენზე გავრცელდება აღნიშნული წესებისა და პირობების მოქმედება.</p>
                            
                            <p>იმ შემთხვევაში თუ თქვენ არ ეთანხმებით წესებსა და პირობებს და არ გსურთ, რომ თქვენზე გავრცელდეს, არ დააჭიროთ დათანხმების ღილაკს და არ გამოიყენოთ ვებ-გვერდსა და აპლიკაციაში განთავსებული ჩვენი სერვისები.</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">მუხლი 1. ტერმინთა განმარტება</h2>
                        <div class="section-content">
                            <p>თუ თავად ტექსტიდან პირდაპირ სხვაგვარად არ გამომდინარეობს, წინამდებარე წესებსა და პირობებში გამოყენებულ ტერმინებს აქვთ შემდეგი მნიშვნელობა:</p>
                            
                            <div class="highlight-box">
                                <p><strong>Smart Choice ან კომპანია:</strong> Synergy Gym და მისი ვებ-გვერდის/აპლიკაციის ოპერატორი</p>
                                <p><strong>ვებ-გვერდი:</strong> კომპანიის ვებ-გვერდი www.synergy-gym.ge</p>
                            </div>
                            
                            <ul>
                                <li><strong>მომსახურების მიმწოდებელი:</strong> Synergy Gym</li>
                                <li><strong>საკონტაქტო ინფორმაცია:</strong> ტელ. +995-XXX-XXX-XXX მეილი info@synergy-gym.ge</li>
                                <li><strong>სამუშაო საათები:</strong> ყოველდღე 08:00-02:00 მდე</li>
                                <li><strong>მიწოდების პირობები:</strong> განისაზღვრება სიტზე მითითებული სტანდარტული პაკეტების შესაბამისად, ასევე მიმდინარე საქციო პროდუქტებიდან გამომდინარე</li>
                            </ul>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">კლუბის წესები</h2>
                        <div class="section-content">
                            <p>მოგესალმებით! Synergy Gym-ის კლუბის წევრად მიღების შესახებ განაცხადის ხელმოწერამდე, გთხოვთ გაეცნოთ შემდეგ წესებს:</p>
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
                                <li><strong>2.1.</strong> კლუბის წევრის მიღებისას, Synergy Gym დაეყრდნობა წევრის ზემოთ აღნიშნულ განცხადებას და გარანტიას და არ არის ვალდებული განახორციელოს რაიმე სახის ფიზიკური ფორმის შეფასება, ფიზიკური მდგომარეობის განსაზღვრისთვის.</li>
                                <li><strong>2.2.</strong> თუ Synergy Gym ჩაატარებს რაიმე სახის შეფასებას ან სხვა მსგავს ტესტირებას, ეს განხორციელებულ იქნება მხოლოდ მონაცემების შედარებისთვის, რათა კლუბის წევრმა შეძლოს თვალი მიადევნოს საკუთარ წინსვლას და არა დიაგნოსტირების მიზნებისთვის.</li>
                                <li><strong>2.3.</strong> Synergy Gym-ს არ შეიძლება წაეყენოს რაიმე მოთხოვნები, საჩივარი ან ზარალის ანაზღაურების მოთხოვნა, ფიზიკური ფორმის შეფასების ან ანალოგიური ტესტირების შედეგების ან მათი ინტერპრეტაციის გამო.</li>
                                <li><strong>2.4.</strong> Synergy Gym არ აგებს პასუხს რაიმე ზიანის გამო, რომელიც განპირობებულია კლუბის წევრის რაიმე სახის ავადმყოფობით ან მდგომარეობით, რომელზეც ვარჯიშებმა შესაძლოა უარყოფითად იმოქმედებს მის ჯანმრთელობაზე, უსაფრთხოებაზე ან ფიზიკურ მდგომარეობაზე, თუ ის შეასრულებს ასეთ სავარჯიშოებს ან მონაწილეობას მიიღებს მათში.</li>
                            </ul>
                            
                            <div class="highlight-box">
                                <strong>მნიშვნელოვანი:</strong> თითოეულმა კლუბის წევრმა უნდა გაიაროს კონსულტაცია ექიმთან, ვარჯიშის დაწყებამდე ან მათ გაგრძელებამდე, თუ არსებობს ამის სამედიცინო ჩვენებები.
                            </div>
                            
                            <p><strong>2.5.</strong> Synergy Gym პასუხს არ აგებს წევრის მდგომარეობაზე, რომელიც გამოწვეულია სპორტული კვების ან საკვები დანამატების გამოყენებით. წევრი კისრულობს ვალდებულებას გაიაროს კონსულტაციები ექიმთან საკვები დანამატების გამოყენებამდე.</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">3. კლუბის წევრების დაშვება</h2>
                        <div class="section-content">
                            <p>კლუბის წევრებზე გაიცემა დაშვებისათვის ბარათი ან დამატებით QR კოდი, რომლებიც საშუალებას მისცემს წევრებს შევიდნენ Synergy Gym-ის ტერიტორიაზე და ისარგებლონ აღჭურვილობით.</p>
                            
                            <p>კლუბის წევრი თანახმაა, არ მისცეს სხვა პირებს შენობებში თავისი ბარათის ან QR კოდის მეშვეობით შესვლის ნება და არ დაუშვას, რომ მასთან ერთად სხვა პირი შევიდეს შენობაში.</p>
                            
                            <div class="highlight-box">
                                <strong>გაფრთხილება:</strong> თუ კლუბის წევრი დაარღვევს წევრების დაშვების რაიმე პირობებს, მას შეიძლება გაუუქმდეს წევრობა, ყოველგვარი ანაზღაურების გარეშე და შეიძლება აღიძრას სისხლის სამართლის საქმე მის წინააღმდეგ.
                            </div>
                            
                            <p>ბარათის შეცვლის ღირებულება განისაზღვრება მიმდინარე ფიქსირებული ტარიფის შესაბამისად. Synergy Gym-ის კლუბის წევრად გახდომისას პირს შეიძლება გადაუღონ ფოტო, რომელიც უსაფრთხოების მიზნებისთვის და მონიტორინგისთვის გამოიყენება.</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">4. აღჭურვილობის გამოყენება</h2>
                        <div class="section-content">
                            <p>ჩვენ ვთხოვთ კლუბის წევრებს ვარჯიშის დასრულების შემდეგ ადგილზე დააბრუნონ გირები და სხვა ნებისმიერი სავარჯიშო აღჭურვილობა (ფილები, ხარიხები, ჰანტელები, და ა შ.).</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">5. აბონიმენტის ნაწილ-ნაწილ გადახდა</h2>
                        <div class="section-content">
                            <ul>
                                <li><strong>5.1.</strong> ნაწილ-ნაწილ გადახდა შესაძლებელია გავრცელდეს მხოლოდ 12 (თორმეტი) თვიან აბონიმენტზე.</li>
                                <li><strong>5.2.</strong> მისი ფასი განისაზღვრება Synergy Gym-ის მიერ და შესაძლებელია შეიცვალოს ნებისმიერ დროს.</li>
                                <li><strong>5.3.</strong> კლუბის წევრის მიერ უკვე შესყიდული აბონიმენტის ფასის ცვლილება შეუძლებელია.</li>
                                <li><strong>5.4.</strong> მთლიანი აბონიმენტის ფასი იყოფა 5 ტოლ ნაწილად, მიღებული თანხით ხდება მისი დაფარვა ყიდვიდან შემდეგი 5 თვის განმავლობაში.</li>
                                <li><strong>5.5.</strong> აბონიმენტის თანხა უნდა დაიფაროს აბონიმენტის ყიდვიდან შემდეგი 5 თვის განმავლობაში არაუგვიანეს ყოველი თვის იმ დღისა, როდესაც მოხდა მომხმარებლის მიერ ხელშეკრულების დადასტურება ონლაინ საშუალებით თუ ხელშეკრულებაზე უშუალოდ ხელმოწერით.</li>
                                <li><strong>5.6.</strong> ხუთ კალენდარულ დღეზე მეტი დაგვიანების შემთხვევაში კონტრაქტი გაუქმდება და გადახდილი თანხა უკან არ დაბრუნდება.</li>
                                <li><strong>5.7.</strong> აღნიშნული აბონიმენტის დაპაუზება ან გაუქმება არ ხდება.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">6. კარადები</h2>
                        <div class="section-content">
                            <p>კარადები ხელმისაწვდომია და განთავსებულია გასახდელებში. წევრი ვალდებულია ყურადღება მიაქციოს წევრის პირად ნივთებს.</p>
                            
                            <div class="highlight-box">
                                <strong>მნიშვნელოვანი:</strong> Synergy Gym-ის თანამშრომლები და კონტრაქტორები პასუხს არ აგებენ პირადი ნივთების დაკარგვისთვის. კარადები არ წარმოადგენს მოწყობილობებს უსაფრთხოდ შენახვისთვის.
                            </div>
                            
                            <p>შესაბამისად, წევრი ვალდებულია კლუბის მომსახურებით სარგებლობისას თან იქონიოს ყველა ფასეული ნივთი. წინააღმდეგ შემთხვევაში Synergy Gym პასუხს არ აგებს ასეთი ნივთების დაკარგვაზე ან/და დაზიანებაზე.</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">7. პირდაპირი მარკეტინგის მიზნებისთვის პერსონალურ მონაცემთა დამუშავებაზე თანხმობა</h2>
                        <div class="section-content">
                            <p><strong>7.1.</strong> წინამდებარე ხელშეკრულებაზე ხელმოწერით წევრი თანხმობას აცხადებს, რომ Synergy Gym მისი პირადი მონაცემები, კერძოდ სახელი (სახელები), მისამართი, ტელეფონის ნომერი, ელექტრონული ფოსტის მისამართი დაამუშაოს პირდაპირი მარკეტინგის მიზნებისათვის და აღნიშნულ მონაცემებზე გაუგზავნოს და მიაწოდოს ინფორმაცია Synergy Gym-ის არსებული, დაგეგმილი ან/და სამომავლო შეთავაზებების, რეკლამების, ფასდაკლებების, აქციებისა და ღონისძიებების შესახებ. ასევე საინფორმაციო შინაარსის შეტყობინების გაგზავნის მიზნით.</p>
                            
                            <p><strong>7.2.</strong> წევრისთვის ცნობილია, რომ ნებისმიერ დროს შეუძლია უარი განაცხადოს და უკან გამოითხოვოს წინამდებარე დოკუმენტით გაცხადებული თანხმობა მისი პირადი მონაცემების პირდაპირი მარკეტინგის მიზნებისთვის დამუშავებასთან დაკავშირებით და აღნიშნული უარი შეუძლია განაცხადოს წერილობითი ფორმით, Synergy Gym-ისთვის აღნიშნულის შესახებ იმავე კომუნიკაციის საშუალებით, რასაც შუალებითაც იღებს ინფორმაციას.</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">8. წევრთა უფლებები</h2>
                        <div class="section-content">
                            <p>Synergy Gym-ის კლუბის წევრობა წევრს უფლებას აძლევს ივარჯიშოს შეძენილი აბონიმენტით განსაზღვრული ვადით და პირობით.</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">9. წევრის ვალდებულებები</h2>
                        <div class="section-content">
                            <p>Synergy Gym-ის კლუბის წევრს მოეთხოვება:</p>
                            <ul>
                                <li><strong>9.1.</strong> დაიცვას კლუბის ყველა წესი და წინამდებარე ხელშეკრულების პირობები;</li>
                                <li><strong>9.2.</strong> გადაიხადოს ყველა შეთანხმებული საფასური;</li>
                                <li><strong>9.3.</strong> წინასწარ აცნობოს Synergy Gym-ს თუ არსებობს რაიმე სახის დაავადება ან რისკი, კლუბის წევრის ჯანმრთელობისთვის.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">10. აკრძალული მოქმედებები</h2>
                        <div class="section-content">
                            <p>შენობაში აკრძალულია მოწევა, ალკოჰოლის მიღება და ნარკოტიკების (მათ შორის სტეროიდების) მოხმარება, დაუშვებელია რაიმე სახის იარაღის შეტანა.</p>
                            
                            <div class="highlight-box">
                                <strong>მნიშვნელოვანი:</strong> კლუბის წევრი თანახმაა არ განახორციელოს რაიმე მოქმედებები Synergy Gym-ის შენობაში, ისეთი ნივთიერებების ზემოქმედების დროს, რომლებსაც შეუძლია დააქვეითოს მისი უნარი, მართოს აღჭურვილობა.
                            </div>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">11. შეწყვეტა</h2>
                        <div class="section-content">
                            <p>კლუბის წევრობა შეიძლება შეწყდეს ვადამდე, წევრის ხანგრძლივი ავადმყოფობის ან ფიზიკური მდგომარეობის გამო. Synergy Gym უფლებას იტოვებს შეწყვიტოს წევრის კონტრაქტი, შემდეგი პირობების შესაბამისად:</p>
                            <ul>
                                <li><strong>11.1.</strong> წევრის შეუსაბამობა წესებში ან/და ხელშეკრულებაში მითითებულ რომელიმე კრიტერიუმთან;</li>
                                <li><strong>11.2.</strong> კლუბის წევრის ან მისი თანამშრომლების (ან კონტრაქტორების) საჩივრის საფუძველზე.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">12. აბონიმენტის საფასურის დაბრუნება და დაპაუზება</h2>
                        <div class="section-content">
                            <p>კლუბის წევრის მოთხოვნის საფუძველზე შესაძლებელია საბანკო ანგარიშზე დაბრუნდეს მხოლოდ ფასდაუკლებელი ტარიფით შეძენილი აბონიმენტის საფასური.</p>
                            
                            <div class="highlight-box">
                                <strong>მნიშვნელოვანი:</strong> არ რა შემთხვევაშიც გადახდილ თანხას აკლდება ყველა დაფიქსირებული ვიზიტი, თითოეული 25 ლარის ოდენობით (ეს საფასური შესაძლოა შეიცვალოს მიმდინარე ფიქსირებული ტარიფის შესაბამისად).
                            </div>
                            
                            <p>დაბრუნება ხდება მოთხოვნიდან და საბანკო ანგარიშის წარდგენიდან 7 (შვიდი) სამუშაო დღის ვადაში.</p>
                            
                            <p>აბონიმენტის ვადის დაპაუზება კი ხდება მხოლოდ კვალიფიციური დაწესებულების ჯანმრთელობის ცნობის საფუძველზე, დოკუმენტში მითითებული საავადმყოფოს ფურცელზე ყოფნის დღეების შესაბამისად, რის საფუძველზე დაუშვებელი იყო ფიზიკური დატვირთვა.</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">13. პასუხისმგებლობა</h2>
                        <div class="section-content">
                            <p>Synergy Gym არ აგებს პასუხს მისგან დამოუკიდებელი მიზეზების გამო ხელშეკრულების პირობების შეუსრულებლობაზე.</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">14. ეგზემპლარები</h2>
                        <div class="section-content">
                            <p>კონტრაქტი შეიძლება გაფორმდეს ორ ან მეტ ეგზემპლარად, რომელთაგან თითოეული ორიგინალად უნდა ჩაითვალოს, ყველა ეგზემპლარი, ერთად, წარმოადგენს ერთსა და იმავე შეთანხმებას.</p>
                        </div>
                    </div>

                    <div class="terms-section">
                        <h2 class="section-title">15. მარეგულირებელი კანონმდებლობა</h2>
                        <div class="section-content">
                            <p>ხელშეკრულება და ყველა მითითებული დოკუმენტები რეგულირდება საქართველოს კანონმდებლობით.</p>
                        </div>
                    </div>

                    <div class="terms-section" style="border-top: 2px solid #c8e600; padding-top: 2rem; margin-top: 2rem;">
                        <div class="section-content">
                            <div class="highlight-box">
                                <p style="font-weight: bold; text-align: center; margin-bottom: 0;">
                                    ვადასტურებ, რომ წავიკითხე კონტრაქტი, ჩემთვის გასაგებია ყველა პირობა და ვეთანხმები მათ. ასევე, ვადასტურებ, რომ გადმომეცა წევრის კონტრაქტის სრულად გაფორმებული ეგზემპლარი.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="contact-info">
                        <h4>Questions or Concerns?</h4>
                        <p><strong>Address:</strong> Tbilisi, Georgia</p>
                        <p><strong>Phone:</strong> <a href="tel:+995322195119">+995-XXX-XXX-XXX</a></p>
                        <p><strong>Email:</strong> <a href="mailto:info@synergy-gym.ge">info@synergy-gym.ge</a></p>
                        <p><strong>Last Updated:</strong> July 2025</p>
                    </div>
                    </div>

                    <!-- English Content -->
                    <div id="terms-en" style="display:none">
                        <div class="terms-header">
                            <h1 class="terms-title">Terms and Conditions</h1>
                            <p class="terms-subtitle">Rules and conditions for using the Synergy Gym website and application</p>
                        </div>

                        <div class="terms-section">
                            <div class="section-content">
                                <p>These Terms and Conditions set the rules for using the Synergy Gym website and application (hereinafter, the “Terms and Conditions”). By agreeing to them, you accept that these Terms and Conditions apply to you.</p>
                                <p>If you do not agree and do not want these Terms and Conditions to apply, please do not click the consent button and do not use the services provided on the website and in the application.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">Article 1. Definitions</h2>
                            <div class="section-content">
                                <p>Unless the context clearly indicates otherwise, the terms used in these Terms and Conditions have the following meanings:</p>
                                <div class="highlight-box">
                                    <p><strong>Smart Choice or Company:</strong> Synergy Gym and the operator of its website/application</p>
                                    <p><strong>Website:</strong> The company website www.synergy-gym.ge</p>
                                </div>
                                <ul>
                                    <li><strong>Service Provider:</strong> Synergy Gym</li>
                                    <li><strong>Contact details:</strong> Tel. +995-XXX-XXX-XXX, E-mail: info@synergy-gym.ge</li>
                                    <li><strong>Working hours:</strong> Every day 08:00–02:00</li>
                                    <li><strong>Delivery/Provision terms:</strong> Determined by standard packages listed on the site as well as current promotional products</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">Club Rules</h2>
                            <div class="section-content">
                                <p>Welcome! Before signing the application for becoming a club member of Synergy Gym, please read the following rules.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">1. Introduction</h2>
                            <div class="section-content">
                                <p>By signing this contract, the club member agrees to comply with and follow these Terms and Conditions.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">2. Member’s Health Warranty</h2>
                            <div class="section-content">
                                <p>The club member guarantees and declares that they are not limited in ability, not ill, and not in a condition that would prevent them from exercising, and that such activity will not harm or adversely affect their health, safety, or physical condition.</p>
                                <p>The club member is aware and agrees that:</p>
                                <ul>
                                    <li><strong>2.1.</strong> Upon admission, Synergy Gym relies on the member’s statements and warranty above and is not obliged to carry out any fitness or health assessment.</li>
                                    <li><strong>2.2.</strong> If Synergy Gym conducts any assessment or similar testing, it is only for comparison of data so the member can track progress, not for diagnosis.</li>
                                    <li><strong>2.3.</strong> No claims or compensation requests can be made against Synergy Gym regarding results or interpretation of fitness assessments or similar testing.</li>
                                    <li><strong>2.4.</strong> Synergy Gym is not liable for any harm caused by any illness or condition of a member that may be adversely affected by exercise if the member performs such exercises or participates in them.</li>
                                </ul>
                                <div class="highlight-box">
                                    <strong>Important:</strong> Each member should consult a doctor before starting or continuing exercise if medically indicated.
                                </div>
                                <p><strong>2.5.</strong> Synergy Gym is not responsible for conditions caused by the use of sports nutrition or supplements. The member undertakes to consult a doctor before using any supplements.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">3. Member Access</h2>
                            <div class="section-content">
                                <p>Members are issued an access card or an additional QR code that allows entry to Synergy Gym premises and use of equipment.</p>
                                <p>The member agrees not to allow other persons to enter the premises using their card or QR code and not to let another person enter together with them.</p>
                                <div class="highlight-box">
                                    <strong>Warning:</strong> If a member violates any access conditions, membership may be canceled without any refund and criminal proceedings may be initiated.
                                </div>
                                <p>The card replacement fee is determined by the current fixed tariff. Upon becoming a member of Synergy Gym, a photo may be taken for security and monitoring purposes.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">4. Equipment Use</h2>
                            <div class="section-content">
                                <p>Please return kettlebells and any other exercise equipment (plates, bars, dumbbells, etc.) to their place after use.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">5. Installment Payments</h2>
                            <div class="section-content">
                                <ul>
                                    <li><strong>5.1.</strong> Installments may apply only to the 12-month membership.</li>
                                    <li><strong>5.2.</strong> The price is determined by Synergy Gym and may change at any time.</li>
                                    <li><strong>5.3.</strong> The price of a membership already purchased by a member cannot be changed.</li>
                                    <li><strong>5.4.</strong> The total price is divided into 5 equal parts to be paid over the next 5 months from the date of purchase.</li>
                                    <li><strong>5.5.</strong> Each installment must be paid within 5 months from purchase, no later than the date corresponding to the contract confirmation date.</li>
                                    <li><strong>5.6.</strong> If delayed by more than five calendar days, the contract will be canceled and the paid amount will not be refunded.</li>
                                    <li><strong>5.7.</strong> This membership cannot be paused or canceled.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">6. Lockers</h2>
                            <div class="section-content">
                                <p>Lockers are available in the changing rooms. Members are responsible for their personal belongings.</p>
                                <div class="highlight-box">
                                    <strong>Important:</strong> Employees and contractors of Synergy Gym are not responsible for lost personal items. Lockers are not secure storage devices.
                                </div>
                                <p>Therefore, members must keep valuables with them while using the club. Otherwise, Synergy Gym is not liable for loss or damage.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">7. Consent to Personal Data Processing for Direct Marketing</h2>
                            <div class="section-content">
                                <p><strong>7.1.</strong> By signing this agreement, the member consents to Synergy Gym processing their personal data (name, address, phone number, email) for direct marketing and sending information about existing, planned, or future offers, ads, discounts, promotions, events, and for informational messages.</p>
                                <p><strong>7.2.</strong> The member knows they can withdraw this consent at any time by notifying Synergy Gym in writing using the same communication channel through which they receive information.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">8. Member Rights</h2>
                            <div class="section-content">
                                <p>Membership in Synergy Gym entitles the member to exercise for the period and under the conditions specified in the purchased membership.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">9. Member Obligations</h2>
                            <div class="section-content">
                                <p>Members of Synergy Gym shall:</p>
                                <ul>
                                    <li><strong>9.1.</strong> Follow all club rules and the terms of this agreement;</li>
                                    <li><strong>9.2.</strong> Pay all agreed fees;</li>
                                    <li><strong>9.3.</strong> Inform Synergy Gym in advance of any disease or risk to health.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">10. Prohibited Actions</h2>
                            <div class="section-content">
                                <p>Smoking, drinking alcohol, and using drugs (including steroids) are prohibited on the premises. Bringing any type of weapon is not allowed.</p>
                                <div class="highlight-box">
                                    <strong>Important:</strong> The member agrees not to perform any actions on the premises of Synergy Gym under the influence of substances that may impair their ability to operate equipment.
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">11. Termination</h2>
                            <div class="section-content">
                                <p>Membership may be terminated early due to long illness or physical condition. Synergy Gym reserves the right to terminate the member’s contract under the following conditions.</p>
                                <ul>
                                    <li><strong>11.1.</strong> Non-compliance with any criteria specified in the rules and/or agreement;</li>
                                    <li><strong>11.2.</strong> Based on a complaint from a club member or its employees (or contractors).</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">12. Refunds and Pausing</h2>
                            <div class="section-content">
                                <p>Upon member request, only the fee for a membership purchased at a non-discounted tariff can be refunded to a bank account.</p>
                                <div class="highlight-box">
                                    <strong>Important:</strong> In all cases, each recorded visit is deducted from the refund at 25 GEL per visit (this fee may change according to the current fixed tariff).
                                </div>
                                <p>Refunds are processed within 7 business days from the request and submission of bank details.</p>
                                <p>Membership can be paused only with a medical certificate from a qualified institution for the days indicated in the document during which physical activity was not allowed.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">13. Liability</h2>
                            <div class="section-content">
                                <p>Synergy Gym is not liable for non-performance caused by reasons beyond its control.</p>
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
                            <p><strong>Address:</strong> Tbilisi, Georgia</p>
                            <p><strong>Phone:</strong> <a href="tel:+995322195119">+995-XXX-XXX-XXX</a></p>
                            <p><strong>Email:</strong> <a href="mailto:info@synergy-gym.ge">info@synergy-gym.ge</a></p>
                            <p><strong>Last Updated:</strong> July 2025</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script>
    // Language handling for this page (localStorage ActiveLanguage or ?lang=)
    (function() {
        function getParam(name) {
            const params = new URLSearchParams(window.location.search);
            return params.get(name);
        }
        function getLang() {
            const fromParam = getParam('lang');
            if (fromParam === 'ka' || fromParam === 'en') return fromParam;
            const fromStorage = localStorage.getItem('ActiveLanguage');
            if (fromStorage === 'ka' || fromStorage === 'en') return fromStorage;
            return 'ka';
        }
        function applyLang(lang) {
            document.documentElement.lang = lang;
            const ka = document.getElementById('terms-ka');
            const en = document.getElementById('terms-en');
            if (ka && en) {
                ka.style.display = (lang === 'ka') ? '' : 'none';
                en.style.display = (lang === 'en') ? '' : 'none';
            }
            const backText = document.getElementById('back-text');
            if (backText) backText.textContent = (lang === 'en') ? 'Back to Home' : 'მთავარ გვერდზე დაბრუნება';
            // Toggle button active state
            const btnKa = document.getElementById('btn-ka');
            const btnEn = document.getElementById('btn-en');
            if (btnKa && btnEn) {
                btnKa.classList.toggle('active', lang === 'ka');
                btnEn.classList.toggle('active', lang === 'en');
            }
        }
        const current = getLang();
        applyLang(current);
        // Wire buttons
        const btnKa = document.getElementById('btn-ka');
        const btnEn = document.getElementById('btn-en');
        if (btnKa) btnKa.addEventListener('click', function() {
            localStorage.setItem('ActiveLanguage', 'ka');
            applyLang('ka');
            const url = new URL(window.location.href);
            url.searchParams.set('lang', 'ka');
            history.replaceState(null, '', url.toString());
        });
        if (btnEn) btnEn.addEventListener('click', function() {
            localStorage.setItem('ActiveLanguage', 'en');
            applyLang('en');
            const url = new URL(window.location.href);
            url.searchParams.set('lang', 'en');
            history.replaceState(null, '', url.toString());
        });
    })();
    </script>
</body>
</html>
