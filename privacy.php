<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Synergy Gym</title>
    
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
                    <div id="privacy-ka">
                        <div class="terms-header">
                            <h1 class="terms-title">კონფიდენციალობის პოლიტიკა</h1>
                            <p class="terms-subtitle">Synergy Gym-ის პერსონალურ მონაცემთა დამუშავების პოლიტიკა</p>
                        </div>

                        <div class="terms-section">
                            <div class="section-content">
                                <p>ეს კონფიდენციალობის პოლიტიკა განმარტავს, თუ როგორ აგროვებს, იყენებს და იცავს Synergy Fitness თქვენს პერსონალურ ინფორმაციას, როდესაც იყენებთ ჩვენს ვებ-გვერდს synergyfitness.ge ან ჩვენი ფიტნეს კლუბის მომსახურებებს.</p>
                                <p>ჩვენ ვამუშავებთ თქვენს პერსონალურ მონაცემებს საქართველოს "პერსონალურ მონაცემთა დაცვის შესახებ" კანონის მოთხოვნების შესაბამისად.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">1. მონაცემთა კონტროლერი</h2>
                            <div class="section-content">
                                <div class="highlight-box">
                                    <p><strong>კომპანია:</strong> Synergy Fitness</p>
                                    <p><strong>ს/მ:</strong> ლუკა ქალიაშვილი, პ/ნ: 0172409681</p>
                                    <p><strong>მისამართი:</strong> მარიჯანი 6, თბილისი, საქართველო</p>
                                    <p><strong>ელ-ფოსტა:</strong> Info@synergyfitness.ge</p>
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">2. რა ინფორმაციას ვაგროვებთ</h2>
                            <div class="section-content">
                                <p>ჩვენ შეიძლება ავიღოთ და ვამუშავოთ შემდეგი ტიპის პერსონალური მონაცემები:</p>
                                <ul>
                                    <li><strong>პირადი იდენტიფიკაციის ინფორმაცია:</strong> სახელი, გვარი, პირადი ნომერი</li>
                                    <li><strong>საკონტაქტო ინფორმაცია:</strong> ტელეფონის ნომერი, ელექტრონული ფოსტის მისამართი, მისამართი</li>
                                    <li><strong>საფინანსო ინფორმაცია:</strong> საბანკო ბარათის მონაცემები, გადახდების ისტორია</li>
                                    <li><strong>ტექნიკური ინფორმაცია:</strong> IP მისამართი, ბრაუზერის ტიპი, მოწყობილობის ინფორმაცია</li>
                                    <li><strong>სავარჯიშო მონაცემები:</strong> კლუბში ვიზიტის ისტორია, აბონიმენტის ინფორმაცია</li>
                                    <li><strong>ჯანმრთელობის მონაცემები:</strong> ექიმის ცნობები (საჭიროების შემთხვევაში)</li>
                                    <li><strong>ფოტო და ვიდეო მასალა:</strong> უსაფრთხოების კამერების ჩანაწერები, წევრის ფოტო</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">3. მონაცემთა გამოყენების მიზნები</h2>
                            <div class="section-content">
                                <p>ჩვენ ვიყენებთ თქვენს პერსონალურ მონაცემებს შემდეგი მიზნებისთვის:</p>
                                <ul>
                                    <li><strong>მომსახურების გაწევა:</strong> აბონიმენტის ფორმირება, კლუბში დაშვება</li>
                                    <li><strong>გადახდების დამუშავება:</strong> სააბონენტო გადასახადების მიღება და დამუშავება</li>
                                    <li><strong>კომუნიკაცია:</strong> მნიშვნელოვანი შეტყობინებების და განახლებების გაგზავნა</li>
                                    <li><strong>მარკეტინგი:</strong> საქციო შეთავაზებების და ახალი სერვისების შესახებ ინფორმაციის გაგზავნა</li>
                                    <li><strong>უსაფრთხოება:</strong> კლუბის ტერიტორიაზე უსაფრთხოების უზრუნველყოფა</li>
                                    <li><strong>სამართლებრივი ვალდებულებები:</strong> კანონით განსაზღვრული ვალდებულებების შესრულება</li>
                                    <li><strong>ანალიტიკა:</strong> მომსახურების ხარისხის გაუმჯობესება</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">4. მონაცემთა დამუშავების სამართლებრივი საფუძველი</h2>
                            <div class="section-content">
                                <p>ჩვენ ვამუშავებთ თქვენს პერსონალურ მონაცემებს შემდეგი სამართლებრივი საფუძვლების მიხედვით:</p>
                                <ul>
                                    <li><strong>ხელშეკრულება:</strong> თქვენთან დადებული ხელშეკრულების შესასრულებლად</li>
                                    <li><strong>თანხმობა:</strong> თქვენი მოცემული ცალსახა თანხმობის საფუძველზე</li>
                                    <li><strong>სამართლებრივი ვალდებულება:</strong> კანონით განსაზღვრული ვალდებულებების შესასრულებლად</li>
                                    <li><strong>ლეგიტიმური ინტერესი:</strong> ჩვენი ლეგიტიმური ბიზნეს ინტერესების დასაცავად</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">5. მონაცემთა გაზიარება</h2>
                            <div class="section-content">
                                <p>ჩვენ არ ვყიდით და არ ვუზიარებთ თქვენს პერსონალურ მონაცემებს მესამე მხარეებს, გარდა შემდეგი შემთხვევებისა:</p>
                                <ul>
                                    <li><strong>სამართლებრივი ვალდებულება:</strong> სახელმწიფო ორგანოების მოთხოვნით</li>
                                    <li><strong>სერვისის მიმწოდებლები:</strong> ჩვენი სანდო პარტნერები (საბანკო სერვისები, IT მხარდაჭერა)</li>
                                    <li><strong>უსაფრთხოება:</strong> კლუბისა და წევრების უსაფრთხოების დაცვისთვის</li>
                                    <li><strong>თანხმობა:</strong> თქვენი ცალსახა თანხმობით</li>
                                </ul>

                                <div class="highlight-box">
                                    <p><strong>მნიშვნელოვანი:</strong> ყველა მესამე მხარე, რომელსაც ვუზიარებთ თქვენს მონაცემებს, ვალდებულია დაიცვას მონაცემთა კონფიდენციალობა.</p>
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">6. მონაცემთა შენახვის ვადა</h2>
                            <div class="section-content">
                                <p>ჩვენ ვინახავთ თქვენს პერსონალურ მონაცემებს:</p>
                                <ul>
                                    <li><strong>აქტიური წევრობის დროს:</strong> მთელი აბონიმენტის ვადის განმავლობაში</li>
                                    <li><strong>წევრობის შეწყვეტის შემდეგ:</strong> 3 წლის განმავლობაში (ბუღალტრული აღრიცხვისთვის)</li>
                                    <li><strong>მარკეტინგული მიზნები:</strong> თქვენი თანხმობის გაუქმებამდე</li>
                                    <li><strong>უსაფრთხოების ვიდეო:</strong> 30 დღის განმავლობაში</li>
                                    <li><strong>სამართლებრივი მოთხოვნები:</strong> კანონით განსაზღვრული ვადით</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">7. მონაცემთა უსაფრთხოება</h2>
                            <div class="section-content">
                                <p>ჩვენ ვიყენებთ შესაბამის ტექნიკურ და ორგანიზაციულ ზომებს თქვენი პერსონალური მონაცემების დასაცავად:</p>
                                <ul>
                                    <li><strong>ციფრული შიფრაცია:</strong> მონაცემების გადაცემისა და შენახვისას</li>
                                    <li><strong>წვდომის კონტროლი:</strong> მხოლოდ ავტორიზებული პერსონალისთვის</li>
                                    <li><strong>რეგულარული მონიტორინგი:</strong> უსაფრთხოების სისტემების შემოწმება</li>
                                    <li><strong>თანამშრომელთა ტრენინგი:</strong> მონაცემთა დაცვის საკითხებში</li>
                                    <li><strong>ფიზიკური უსაფრთხოება:</strong> სერვერების და მოწყობილობების დაცვა</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">8. თქვენი უფლებები</h2>
                            <div class="section-content">
                                <p>პერსონალურ მონაცემთა დაცვის კანონის შესაბამისად, თქვენ გაქვთ შემდეგი უფლებები:</p>
                                <ul>
                                    <li><strong>წვდომის უფლება:</strong> ინფორმაციის მიღება ამუშავებული მონაცემების შესახებ</li>
                                    <li><strong>რედაქტირების უფლება:</strong> არასწორი მონაცემების შესწორება</li>
                                    <li><strong>წაშლის უფლება:</strong> მონაცემების წაშლის მოთხოვნა</li>
                                    <li><strong>დამუშავების შეზღუდვა:</strong> კონკრეტული მონაცემების დამუშავების შეჩერება</li>
                                    <li><strong>მონაცემთა პორტაბელობა:</strong> თქვენი მონაცემების მიღება მანქანაზე წასაკითხი ფორმით</li>
                                    <li><strong>უარყოფის უფლება:</strong> მარკეტინგული მიზნებისთვის დამუშავების შეწყვეტა</li>
                                    <li><strong>საჩივრის უფლება:</strong> ზედამხედველ ორგანოში განცხადების შეტანა</li>
                                </ul>

                                <div class="highlight-box">
                                    <p><strong>როგორ გამოვიყენოთ ეს უფლებები:</strong> დაგვიკავშირდით ელ-ფოსტაზე Info@synergyfitness.ge</p>
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">9. Cookie-ები და ტრაკინგი</h2>
                            <div class="section-content">
                                <p>ჩვენი ვებ-გვერდი იყენებს cookie-ებს ფუნქციონალობის გაუმჯობესებისთვის:</p>
                                <ul>
                                    <li><strong>აუცილებელი Cookie-ები:</strong> ვებ-გვერდის მუშაობისთვის</li>
                                    <li><strong>ფუნქციონალური Cookie-ები:</strong> მომხმარებლის გამოცდილების გაუმჯობესებისთვის</li>
                                    <li><strong>ანალიტიკური Cookie-ები:</strong> ტრაფიკის ანალიზისთვის</li>
                                    <li><strong>მარკეტინგული Cookie-ები:</strong> რელევანტური რეკლამების ჩვენებისთვის</li>
                                </ul>
                                <p>თქვენ შეგიძლიათ მართოთ Cookie-ების პარამეტრები ბრაუზერის პარამეტრებში.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">10. მონაცემთა შეცდომის შემთხვევაში</h2>
                            <div class="section-content">
                                <p>უსაფრთხოების ინციდენტის შემთხვევაში ჩვენ:</p>
                                <ul>
                                    <li>დაუყოვნებლივ მივმართავთ ღონისძიებებს ინციდენტის შესაჩერებლად</li>
                                    <li>შევაფასებთ რისკს და შედეგებს</li>
                                    <li>დროულად შეგატყობინებთ, თუ ინციდენტი გავლენას ახდენს თქვენს მონაცემებზე</li>
                                    <li>შევატყობინებთ შესაბამის ზედამხედველ ორგანოებს</li>
                                    <li>განვახორციელებთ გამოსწორების ზომებს მომავალში მსგავსი ინციდენტების თავიდან ასაცილებლად</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">12. პოლიტიკის ცვლილებები</h2>
                            <div class="section-content">
                                <p>ჩვენ შეიძლება დროდადრო განვაახლოთ ეს კონფიდენციალობის პოლიტიკა. ცვლილებების შემთხვევაში:</p>
                                <ul>
                                    <li>განახლებული ვერსია გამოქვეყნდება ჩვენს ვებ-გვერდზე</li>
                                    <li>მნიშვნელოვანი ცვლილებების შესახებ შეგიტყობინებთ ელ-ფოსტაზე</li>
                                    <li>ახალი პოლიტიკა ძალაში შევა მისი გამოქვეყნების თარიღიდან</li>
                                    <li>ვერსიის თარიღი მითითებული იქნება დოკუმენტის ბოლოში</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">13. კონტაქტი</h2>
                            <div class="section-content">
                                <p>კონფიდენციალობის ან მონაცემთა დაცვის საკითხებთან დაკავშირებით დაგვიკავშირდით:</p>
                                <div class="highlight-box">
                                    <p><strong>მონაცემთა დაცვის ოფიცერი:</strong> ლუკა ქალიაშვილი</p>
                                    <p><strong>ელ-ფოსტა:</strong> Info@synergyfitness.ge</p>
                                    <p><strong>მისამართი:</strong> მარიჯანი 6, თბილისი, საქართველო</p>
                                    <p><strong>სამუშაო საათები:</strong> ყოველდღე 08:00-02:00</p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-info">
                            <h4>კითხვები ან შეშფოთებები?</h4>
                            <p><strong>მისამართი:</strong> მარიჯანი 6, თბილისი, საქართველო</p>
                            <p><strong>ელ-ფოსტა:</strong> <a href="mailto:Info@synergyfitness.ge">Info@synergyfitness.ge</a></p>
                            <p><strong>ბოლოს განახლდა:</strong> სექტემბერი 2025</p>
                        </div>
                    </div>

                    <!-- English Content -->
                    <div id="privacy-en" style="display:none">
                        <div class="terms-header">
                            <h1 class="terms-title">Privacy Policy</h1>
                            <p class="terms-subtitle">Synergy Gym Personal Data Processing Policy</p>
                        </div>

                        <div class="terms-section">
                            <div class="section-content">
                                <p>This Privacy Policy explains how Synergy Fitness collects, uses, and protects your personal information when you use our website synergyfitness.ge or our fitness club services.</p>
                                <p>We process your personal data in accordance with the requirements of Georgia's "Personal Data Protection Law".</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">1. Data Controller</h2>
                            <div class="section-content">
                                <div class="highlight-box">
                                    <p><strong>Company:</strong> Synergy Fitness</p>
                                    <p><strong>Sole Entrepreneur:</strong> Luka Qaliashvili, ID: 0172409681</p>
                                    <p><strong>Address:</strong> Marijani 6, Tbilisi, Georgia</p>
                                    <p><strong>Email:</strong> Info@synergyfitness.ge</p>
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">2. What Information We Collect</h2>
                            <div class="section-content">
                                <p>We may collect and process the following types of personal data:</p>
                                <ul>
                                    <li><strong>Personal identification information:</strong> Name, surname, personal ID number</li>
                                    <li><strong>Contact information:</strong> Phone number, email address, address</li>
                                    <li><strong>Financial information:</strong> Bank card details, payment history</li>
                                    <li><strong>Technical information:</strong> IP address, browser type, device information</li>
                                    <li><strong>Workout data:</strong> Club visit history, membership information</li>
                                    <li><strong>Health data:</strong> Medical certificates (when required)</li>
                                    <li><strong>Photos and videos:</strong> Security camera recordings, member photos</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">3. Purpose of Data Processing</h2>
                            <div class="section-content">
                                <p>We use your personal data for the following purposes:</p>
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

                        <div class="terms-section">
                            <h2 class="section-title">4. Legal Basis for Processing</h2>
                            <div class="section-content">
                                <p>We process your personal data based on the following legal grounds:</p>
                                <ul>
                                    <li><strong>Contract:</strong> To perform our contract with you</li>
                                    <li><strong>Consent:</strong> Based on your explicit consent</li>
                                    <li><strong>Legal obligation:</strong> To comply with legal requirements</li>
                                    <li><strong>Legitimate interest:</strong> To protect our legitimate business interests</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">5. Data Sharing</h2>
                            <div class="section-content">
                                <p>We do not sell or share your personal data with third parties, except in the following cases:</p>
                                <ul>
                                    <li><strong>Legal obligation:</strong> Upon request from government authorities</li>
                                    <li><strong>Service providers:</strong> Our trusted partners (banking services, IT support)</li>
                                    <li><strong>Security:</strong> To protect the safety of the club and members</li>
                                    <li><strong>Consent:</strong> With your explicit consent</li>
                                </ul>

                                <div class="highlight-box">
                                    <p><strong>Important:</strong> All third parties with whom we share your data are obligated to maintain data confidentiality.</p>
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">6. Data Retention Period</h2>
                            <div class="section-content">
                                <p>We retain your personal data:</p>
                                <ul>
                                    <li><strong>During active membership:</strong> Throughout the membership period</li>
                                    <li><strong>After membership termination:</strong> For 3 years (for accounting purposes)</li>
                                    <li><strong>Marketing purposes:</strong> Until you withdraw your consent</li>
                                    <li><strong>Security videos:</strong> For 30 days</li>
                                    <li><strong>Legal requirements:</strong> For the period specified by law</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">7. Data Security</h2>
                            <div class="section-content">
                                <p>We use appropriate technical and organizational measures to protect your personal data:</p>
                                <ul>
                                    <li><strong>Digital encryption:</strong> During data transmission and storage</li>
                                    <li><strong>Access control:</strong> Only for authorized personnel</li>
                                    <li><strong>Regular monitoring:</strong> Security system checks</li>
                                    <li><strong>Staff training:</strong> On data protection issues</li>
                                    <li><strong>Physical security:</strong> Protection of servers and devices</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">8. Your Rights</h2>
                            <div class="section-content">
                                <p>Under personal data protection law, you have the following rights:</p>
                                <ul>
                                    <li><strong>Right of access:</strong> Receive information about processed data</li>
                                    <li><strong>Right to rectification:</strong> Correct incorrect data</li>
                                    <li><strong>Right to erasure:</strong> Request deletion of data</li>
                                    <li><strong>Right to restrict processing:</strong> Limit processing of specific data</li>
                                    <li><strong>Data portability:</strong> Receive your data in machine-readable format</li>
                                    <li><strong>Right to object:</strong> Stop processing for marketing purposes</li>
                                    <li><strong>Right to complaint:</strong> File a complaint with supervisory authorities</li>
                                </ul>

                                <div class="highlight-box">
                                    <p><strong>How to exercise these rights:</strong> Contact us at Info@synergyfitness.ge</p>
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">9. Cookies and Tracking</h2>
                            <div class="section-content">
                                <p>Our website uses cookies to improve functionality:</p>
                                <ul>
                                    <li><strong>Essential cookies:</strong> For website operation</li>
                                    <li><strong>Functional cookies:</strong> To improve user experience</li>
                                    <li><strong>Analytics cookies:</strong> For traffic analysis</li>
                                    <li><strong>Marketing cookies:</strong> To show relevant advertisements</li>
                                </ul>
                                <p>You can manage cookie settings in your browser preferences.</p>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">10. Data Breach Response</h2>
                            <div class="section-content">
                                <p>In case of a security incident, we will:</p>
                                <ul>
                                    <li>Immediately take measures to stop the incident</li>
                                    <li>Assess risks and consequences</li>
                                    <li>Notify you promptly if the incident affects your data</li>
                                    <li>Notify relevant supervisory authorities</li>
                                    <li>Implement corrective measures to prevent future incidents</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">11. Children's Data</h2>
                            <div class="section-content">
                                <p>Our services are intended for persons aged 18 and older. Processing of minors' data is done:</p>
                                <ul>
                                    <li>With written parental/guardian consent</li>
                                    <li>With parental/guardian presence at the club</li>
                                    <li>With special attention to safety measures</li>
                                </ul>

                                <div class="highlight-box">
                                    <p><strong>Important:</strong> Minors (14-18 years) are admitted to the club only accompanied by a parent/guardian.</p>
                                </div>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">12. Policy Changes</h2>
                            <div class="section-content">
                                <p>We may update this privacy policy from time to time. In case of changes:</p>
                                <ul>
                                    <li>The updated version will be published on our website</li>
                                    <li>We will notify you of significant changes via email</li>
                                    <li>The new policy will take effect from its publication date</li>
                                    <li>The version date will be indicated at the end of the document</li>
                                </ul>
                            </div>
                        </div>

                        <div class="terms-section">
                            <h2 class="section-title">13. Contact</h2>
                            <div class="section-content">
                                <p>For privacy or data protection issues, contact us:</p>
                                <div class="highlight-box">
                                    <p><strong>Data Protection Officer:</strong> Luka Qaliashvili</p>
                                    <p><strong>Email:</strong> Info@synergyfitness.ge</p>
                                    <p><strong>Address:</strong> Marijani 6, Tbilisi, Georgia</p>
                                    <p><strong>Working hours:</strong> Every day 08:00-02:00</p>
                                </div>
                            </div>
                        </div>

                        <div class="contact-info">
                            <h4>Questions or Concerns?</h4>
                            <p><strong>Address:</strong> Marijani 6, Tbilisi, Georgia</p>
                            <p><strong>Email:</strong> <a href="mailto:Info@synergyfitness.ge">Info@synergyfitness.ge</a></p>
                            <p><strong>Last Updated:</strong> September 2025</p>
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
            const ka = document.getElementById('privacy-ka');
            const en = document.getElementById('privacy-en');
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