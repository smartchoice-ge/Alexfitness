<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agreement Form</title>
    <!-- Import Tailwind CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.web-fonts.ge/fonts/bpg-glaho-traditional/css/bpg-glaho-traditional.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center" style="font-family: BPG Glaho Traditional;">
    <div class="max-w-lg w-full p-6 bg-white rounded-lg shadow-md">
        <!-- Language Switcher -->
        <div class="text-right mb-4">
            <button onclick="switchLanguage('en')" class="text-blue-500 px-2">English</button> | 
            <button onclick="switchLanguage('ka')" class="text-blue-500 px-2">ქართული</button>
        </div>
        <img src="/img/gym.png" alt="Synergy Logo" class="mx-auto mb-4">
        <h1 id="title" class="text-3xl font-bold mb-6 text-center">მშობლის თანხმობა</h1>
        <form id="agreementForm">
            <div class="mb-4">
                <label id="label_id_number" for="id_number" class="block text-sm font-medium text-gray-700">პირადი ნომერი / პასპორტის ნომერი</label>
                <input type="text" name="id_number" id="id_number" class="mt-1 p-2 border rounded-md w-full"
                    placeholder="ჩაწერე პირადი ნომერი" maxlength="11" required>
            </div>
            <div class="mb-4">
                <label id="label_full_name" for="full_name" class="block text-sm font-medium text-gray-700">სრული სახელი</label>
                <input type="text" name="full_name" id="full_name" class="mt-1 p-2 border rounded-md w-full" 
                    placeholder="შენი სახელი და გვარი" required>
            </div>

            <div class="mb-4">
                <label id="label_address" for="address" class="block text-sm font-medium text-gray-700">მისამართი</label>
                <input type="text" name="address" id="address" class="mt-1 p-2 border rounded-md w-full" 
                    placeholder="მისამართი" required>
            </div>

            <div class="mb-4">
                <label for="label_mobile_number" id="label_mobile_number" class="block text-sm font-medium text-gray-700">
                    მობილურის ნომერი
                </label>
                <div class="flex items-center border rounded-md">
                    <input type="text" name="country_code" id="country_code" value="995" 
                        class="p-2 bg-gray-200 text-gray-700 w-14 text-center" readonly>
                    <input type="text" name="mobile_number" id="mobile_number" 
                        class="p-2 w-full outline-none" placeholder="შენი ტელეფონის ნომერი"
                        pattern="\d{9}" title="შეიყვანეთ 9 ციფრიანი ტელეფონის ნომერი" required>
                </div>
            </div>


            <div class="mb-4">
                <label for="label_email" id="label_email" class="block text-sm font-medium text-gray-700">ელ-ფოსტა</label>
                <input id="email" type="text" name="email" class="mt-1 p-2 border rounded-md w-full"
                    placeholder="შენი ელ-ფოსტა" required>
            </div>
           
            <div class="mb-4">
                <label id="label_birth_date" for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">
                    დაბადების თარიღი
                </label>
                <div class="flex space-x-2">
                    <!-- Select for Months -->
                    <select name="birth_month" id="birth_month"
                        class="p-2 border border-gray-300 rounded-md w-1/3 focus:ring focus:ring-indigo-300 focus:border-indigo-500"
                        required>
                        <option value="">თვე</option>
                        <option value="01">იანვარი</option>
                        <option value="02">თებერვალი</option>
                        <option value="03">მარტი</option>
                        <option value="04">აპრილი</option>
                        <option value="05">მაისი</option>
                        <option value="06">ივნისი</option>
                        <option value="07">ივლისი</option>
                        <option value="08">აგვისტო</option>
                        <option value="09">სექტემბერი</option>
                        <option value="10">ოქტომბერი</option>
                        <option value="11">ნოემბერი</option>
                        <option value="12">დეკემბერი</option>
                    </select>

                    <!-- Input for Day -->
                    <input id='birth_day' type="number" name="birth_day" min="1" max="31" placeholder="დღე"
                        class="p-2 border border-gray-300 rounded-md w-1/4 text-center focus:ring focus:ring-indigo-300 focus:border-indigo-500"
                        required>

                    <!-- Input for Year -->
                    <input id='birth_year' type="number" name="birth_year" min="1900" max="2100" placeholder="წელი"
                        class="p-2 border border-gray-300 rounded-md w-1/3 text-center focus:ring focus:ring-indigo-300 focus:border-indigo-500"
                        required>
                </div>
            </div>


            <div class="mb-4">
                <label id="label_child_name" for="child_name" class="block text-sm font-medium text-gray-700">შვილის სახელი და გვარი</label>
                <input type="text" name="child_name" id="child_name" class="mt-1 p-2 border rounded-md w-full" 
                    placeholder="შვილის სრული სახელი" required>
            </div>

            <div class="mb-4">
                <label id="label_child_id" for="child_id" class="block text-sm font-medium text-gray-700">შვილის პირადი ნომერი</label>
                <input type="text" name="child_id" id="child_id" class="mt-1 p-2 border rounded-md w-full" 
                    placeholder="შვილის სრული სახელი" required>
            </div>

            <div class="mb-4">
                <input type="checkbox" name="agree_to_agreement" id="agree_to_agreement" class="mr-2">
                <label id="label_agreement" for="agree_to_agreement" class="text-sm font-medium text-gray-700">
                    ვეთანხმები<a href="/Alex-Fitness-agreement.pdf" target="_blank"> <span class="text-red-500">კონტრაქტს</span></a>
                </label>
            </div>
            <div class="mb-4">
                <input type="checkbox" name="agree_to_agreement_parent" id="agree_to_agreement_parent" class="mr-2">
                <label id="label_agreement_parent" for="agree_to_agreement_parent" class="text-sm font-medium text-gray-700">
                    ვეთანხმები<a href="/მშობლის თანხმობა.pdf" target="_blank"> <span class="text-red-500">ფორმას</span></a>
                </label>
            </div>

            <div class="mb-4">
                <label for="options" id="verification_method_label" class="block text-sm font-medium text-gray-700">ვერიფიკაციის მეთოდი</label>
                <select id="verification_method" name="options" class="mt-1 p-2 border rounded-md w-full">
                    <option value="sms" selected>SMS</option>
                    <option value="email">Email</option>
                </select>
            </div>


            <div class="mb-4">
                <button id="submit_button" type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md w-full">გაგზავნა</button>
            </div>
        </form>

        <div id="message" class="text-red-500 text-center"></div>
    </div>
    
    <script>
        let currentLang = 'ka';

        // Translation dictionary with placeholders
        const translations = {
            en: {
                title: "Agreement Form",
                label_id_number: "ID Number / Passport No",
                label_full_name: "Full Name",
                label_mobile_number: "Mobile Number",
                label_birth_date: "Birth Date",
                label_email: "Your email address",
                label_child_name: "Child's Full Name",
                label_child_id: "Child's ID Number",
                label_address: "Address",
                verification_method_label: "Verification Method",
                label_agreement: "I agree to the",
                contract_text: "Contract",
                submit_button: "Submit",
                agree_error: "You must agree to the contract",
                sms_error: "Error sending SMS: ",
                success_message: "Your response has been recorded!",
                verification_prompt: "Enter the code",
                verification_placeholder: "Enter 3-digit code",
                verification_error: "Incorrect code, try again!",
                verification_confirm: "Confirm",
                verification_cancel: "Cancel",
                placeholders: {
                    id_number: "Enter your ID number",
                    full_name: "Your full name",
                    mobile_number: "Your phone number",
                    month: "Month",
                    day: "Day",
                    year: "Year",
                    email: "Email",
                    address: "Address",
                    child_name: "Child's full name",
                    child_id: "Child's ID number"
                },
                monthes: { 
                    1: "January", 2: "February", 3: "March", 4: "April", 5: "May", 6: "June",
                    7: "July", 8: "August", 9: "September", 10: "October", 11: "November", 12: "December"
                },
                titles: { 
                    full_name: "Only letters allowed",
                    mobile_number: "Enter a 9-digit phone number"
                }
            },
            ka: {
                title: "შეთანხმების ფორმა",
                label_id_number: "პირადი ნომერი / პასპორტის ნომერი",
                label_full_name: "სრული სახელი",
                label_mobile_number: "მობილურის ნომერი",
                label_birth_date: "დაბადების თარიღი",
                label_email: "შენი ელ-ფოსტა",
                label_address: "მისამართი",
                label_child_name: "შვილის სახელი და გვარი",
                label_child_id: "შვილის პირადი ნომერი",
                verification_method_label: "ვერიფიკაციის მეთოდი",
                label_agreement: "ვეთანხმები",
                contract_text: "კონტრაქტს",
                submit_button: "გაგზავნა",
                agree_error: "დაეთანხმეთ კონტრაქტს",
                sms_error: "SMS გაგზავნის შეცდომა: ",
                success_message: "თქვენი პასუხი დაფიქსირდა!",
                verification_prompt: "შეიყვანეთ კოდი",
                verification_placeholder: "შეიყვანეთ 3 ციფრიანი კოდი",
                verification_error: "კოდი არასწორია, სცადეთ თავიდან!",
                verification_confirm: "დადასტურება",
                verification_cancel: "გაუქმება",
                placeholders: {
                    id_number: "ჩაწერე პირადი ნომერი",
                    full_name: "შენი სახელი და გვარი",
                    mobile_number: "ტელეფონის ნომერი",
                    month: "თვე",
                    day: "დღე",
                    year: "წელი",
                    email: "ელ-ფოსტა",
                    address: "მისამართი",
                    child_name: "შვილის სრული სახელი",
                    child_id: "შვილის პირადი ნომერი"
                },
                monthes: { 
                    1: "იანვარი", 2: "თებერვალი", 3: "მარტი", 4: "აპრილი", 5: "მაისი", 6: "ივნისი",
                    7: "ივლისი", 8: "აგვისტო", 9: "სექტემბერი", 10: "ოქტომბერი", 11: "ნოემბერი", 12: "დეკემბერი"
                },
                titles: { 
                    full_name: "დაშვებულია მხოლოდ ასოები და სფეისები",
                    mobile_number: "შეიყვანეთ 9 ციფრიანი ტელეფონის ნომერი"
                }
            }
        };


        function switchLanguage(lang) {
            console.log(lang)
            currentLang = lang;
            console.log(translations[lang]);

            console.log(translations[lang].placeholders.full_name);
            document.getElementById("title").textContent = translations[currentLang].title;
            document.getElementById("label_id_number").textContent = translations[currentLang].label_id_number;
            document.getElementById("label_full_name").textContent = translations[lang].label_full_name;
            document.getElementById("label_mobile_number").textContent = translations[lang].label_mobile_number;
            document.getElementById("label_email").textContent = translations[currentLang].label_email;
            document.getElementById("label_birth_date").textContent = translations[currentLang].label_birth_date;
            document.getElementById("verification_method_label").textContent = translations[currentLang].verification_method_label;
            document.getElementById("label_agreement").innerHTML = translations[currentLang].label_agreement + 
                '<a href="/Alex-Fitness-agreement.pdf" target="_blank"> <span class="text-red-500">' + translations[lang].contract_text + '</span></a>';
            document.getElementById("submit_button").textContent = translations[lang].submit_button;

            // Update placeholders
            document.getElementById("id_number").placeholder = translations[lang].placeholders.id_number;
            document.getElementById("full_name").placeholder = translations[lang].placeholders.full_name;
            document.getElementById("mobile_number").placeholder = translations[lang].placeholders.mobile_number;
            document.getElementById("email").placeholder = translations[lang].placeholders.email;
            document.getElementById("birth_day").placeholder = translations[lang].placeholders.day;
            document.getElementById("birth_year").placeholder = translations[lang].placeholders.year;

            // Update title attributes for input validation
            document.getElementById("full_name").title = translations[lang].titles.full_name;
            document.getElementById("mobile_number").title = translations[lang].titles.mobile_number;

            // Update month names
            const monthSelect = document.getElementById("birth_month");
            monthSelect.innerHTML = '<option value="">' + translations[lang].placeholders.month + '</option>';
            for (let i = 1; i <= 12; i++) {
                const monthValue = i.toString().padStart(2, '0');
                const monthName = translations[lang].monthes[i];
                monthSelect.innerHTML += `<option value="${monthValue}">${monthName}</option>`;
            }
        }
        // Function to make AJAX request and check if user exists
        function checkUserExists(idNumber) {
            // AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_user.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        // Fill respective fields if user exists
                        document.getElementById('full_name').value = response.data.full_name;
                        document.getElementById('mobile_number').value = response.data.mobile_number;
                    } else {
                        // Display message if user does not exist
                        document.getElementById('message').innerHTML = response.message;
                    }
                }
            };
            xhr.send('id_number=' + idNumber);
        }

        // Event listener for ID number field
        document.getElementById('id_number').addEventListener('input', function() {
            var idNumber = this.value;
            if (idNumber.length < 11) {
                // Clear other fields and message if ID number length is less than 11
                document.getElementById('full_name').value = '';
                document.getElementById('mobile_number').value = '';
                document.getElementById('message').innerHTML = '';
                return; // Exit the function if ID number length is less than 11
            }
            // Call function to check if user exists
            checkUserExists(idNumber);
        });

        document.getElementById('agreementForm').addEventListener('submit', function(event) {
            event.preventDefault();

            if (!document.getElementById('agree_to_agreement').checked) {
                document.getElementById('message').innerHTML = `${translations[currentLang].agree_error}`;
                return;
            }

            let mobileNumber = document.getElementById('mobile_number').value;
            
            if (!/^\d{9}$/.test(mobileNumber)) {
                document.getElementById('message').innerHTML = 'გთხოვთ შეიყვანოთ სწორი ტელეფონის ნომერი';
                return;
            }

            // Generate a random 3-digit code
            let verificationCode = Math.floor(100 + Math.random() * 900);
            sessionStorage.setItem("verificationCode", verificationCode);

            let verificationMethod = document.getElementById('verification_method').value;

            if(verificationMethod === 'sms'){ 
                let xhr = new XMLHttpRequest();
                xhr.open('POST', 'send_sms.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        let response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            showVerificationPopup();
                        } else {
                            document.getElementById('message').innerHTML = 'SMS გაგზავნის შეცდომა: ' + response.message;
                        }
                    }
                };
                xhr.send('mobile=' + mobileNumber + '&code=' + verificationCode);
            } else if (verificationMethod === 'email'){
                let emailAddress = document.getElementById('email').value;
                let xhr = new XMLHttpRequest();
                xhr.open('POST', 'send_email.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        try {
                            showVerificationPopup();
                        } catch (e) {
                            console.error('Invalid JSON response:', xhr.responseText);
                        }
                    }
                };
                xhr.send('email=' + emailAddress + '&code=' + verificationCode);
            }
        });

        function showVerificationPopup() {
            let popup = document.createElement('div');
            popup.id = "verificationPopup";
            popup.innerHTML = `
                <div class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-50">
                    <div class="bg-white p-6 rounded-md shadow-md w-96 text-center">
                        <h2 class="text-lg font-semibold mb-4">${translations[currentLang].verification_prompt}</h2>
                        <input type="text" id="verificationCodeInput" class="border p-2 w-full text-center" maxlength="3" placeholder="${translations[currentLang].verification_placeholder}">
                        <p id="codeErrorMessage" class="text-red-500 mt-2"></p>
                        <div class="flex justify-between mt-4">
                            <button onclick="verifyCode()" class="bg-green-500 text-white px-4 py-2 rounded-md">${translations[currentLang].verification_confirm}</button>
                            <button onclick="closePopup()" class="bg-red-500 text-white px-4 py-2 rounded-md">${translations[currentLang].verification_cancel}</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(popup);
        }


        function verifyCode() {
            let enteredCode = document.getElementById('verificationCodeInput').value;
            let correctCode = sessionStorage.getItem("verificationCode");

            if (enteredCode === correctCode) {
                closePopup();
                submitForm();
            } else {
                document.getElementById('codeErrorMessage').innerText = `${translations[currentLang].verification_error}`;
            }
        }

        function closePopup() {
            document.getElementById('verificationPopup').remove();
        }

        function submitForm() {
            let formData = new FormData(document.getElementById('agreementForm'));
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'agreement_parent_handler.php', true);
            xhr.onload = function() {
                let response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    window.location.href = 'success.php';
                } else {
                    document.getElementById('message').innerHTML = response.message;
                }
            };
            xhr.send(formData);
        }

</script>


</body>

</html>