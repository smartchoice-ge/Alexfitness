<?php
require_once __DIR__ . '/tcpdf_loader.php';

if (!loadTcpdfLibrary(__DIR__)) {
    http_response_code(500);
    echo json_encode(array('status' => 'error', 'message' => 'Server configuration error - missing PDF dependency'));
    exit;
}

include_once 'db_connection.php';
include_once 'mssql_connection.php';
include_once 'mssql_packages_payments_helper.php';
date_default_timezone_set('Asia/Tbilisi');

function generatePDF($id_number, $html_content) {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Synergy');
    $pdf->SetTitle('User Agreement');
    $pdf->SetSubject('User Agreement Form');
    $pdf->SetKeywords('TCPDF, PDF, agreement');
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 11);
    $pdf->writeHTML($html_content);
    $docsDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/docs/';
    if (!is_dir($docsDir)) {
        mkdir($docsDir, 0755, true);
    }
    $pdf->Output($docsDir . $id_number . '.pdf', 'F');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_number = $_POST['id_number'];
    $full_name = $_POST['full_name'];
    $mobile_number = $_POST['mobile_number'];
    $email = $_POST['email'];
    $agree_to_agreement = isset($_POST['agree_to_agreement']) ? 1 : 0;
    $agreement_date = date('Y-m-d H:i:s');
    $year = date('Y', strtotime($agreement_date));
    $birth_date = $_POST['birth_year'] . '-' . $_POST['birth_month'] . '-' . str_pad($_POST['birth_day'], 2, '0', STR_PAD_LEFT);
    $user_ip = $_SERVER['REMOTE_ADDR'];

    // Check if client exists in MSSQL ClientDetailsWebsite
    $user_data = getClientDetailsByIdNumber($id_number);

    if ($user_data) {
        if ($user_data['agreed'] == 10) {
            echo json_encode(array('status' => 'error', 'message' => 'შენ უკვე შევსებული გაქვს ფორმა'));
        } else {
            // Update existing client in MSSQL ClientDetailsWebsite
            $updateData = [
                'full_name' => $full_name,
                'mobile_number' => $mobile_number,
                'agreed' => $agree_to_agreement,
                'email' => $email,
                'agreement_date' => $agreement_date,
                'birth_date' => $birth_date,
                'user_ip' => $user_ip
            ];
            
            if (updateClientDetailsByIdNumber($id_number, $updateData)) {
                ob_start();
                include('agreement_template.php');
                $html_content = ob_get_clean();
                generatePDF($id_number, $html_content);
                echo json_encode(array('status' => 'success', 'message' => 'Record updated successfully'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => 'Error updating record in ClientDetailsWebsite'));
            }
        }
    } else {
        // Insert new client to MSSQL ClientDetailsWebsite
        $insertData = [
            'id_number' => $id_number,
            'full_name' => $full_name,
            'mobile_number' => $mobile_number,
            'email' => $email,
            'agreed' => $agree_to_agreement,
            'agreement_date' => $agreement_date,
            'user_ip' => $user_ip,
            'birth_date' => $birth_date
        ];
        
        $user_id = insertClientDetails($insertData);
        
        if ($user_id) {
            ob_start();
            include('agreement_template.php');
            $html_content = ob_get_clean();
            generatePDF($id_number, $html_content);
            echo json_encode(array('status' => 'success', 'message' => 'New record created successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error inserting into ClientDetailsWebsite'));
        }
    }
    exit;
}
?>



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

        <h1 id="title" class="text-3xl font-bold mb-6 text-center">შეთანხმების ფორმა</h1>
        <form id="agreementForm">
            <div class="mb-4">
                <label id="label_id_number" for="id_number" class="block text-sm font-medium text-gray-700">პირადი ნომერი / პასპორტის ნომერი</label>
                <input type="text" name="id_number" id="id_number" class="mt-1 p-2 border rounded-md w-full"
                    placeholder="ჩაწერე პირადი ნომერი" maxlength="11" required>
            </div>
            <div class="mb-4">
                <label id="label_full_name" for="full_name" class="block text-sm font-medium text-gray-700">სრული სახელი</label>
                <input type="text" name="full_name" id="full_name" class="mt-1 p-2 border rounded-md w-full"
                    placeholder="შენი სახელი და გვარი" required
                    pattern="^[a-zA-Zა-ჰ\s]+$" 
                    title="დაშვებულია მხოლოდ ასოები და სფეისები">
            </div>

            <div class="mb-4">
                <label id="label_mobile_number" for="mobile_number" class="block text-sm font-medium text-gray-700">მობილურის ნომერი</label>
                <input type="text" name="mobile_number" id="mobile_number" class="mt-1 p-2 border rounded-md w-full"
                    placeholder="შენი ტელეფონის ნომერი" required
                    pattern="^\d{9}$" 
                    title="შეიყვანეთ 9 ციფრიანი ტელეფონის ნომერი">
            </div>

            <div class="mb-4">
                <label id="label_email" for="email" class="block text-sm font-medium text-gray-700">ელ-ფოსტა</label>
                <input type="text" name="email" id="email" class="mt-1 p-2 border rounded-md w-full"
                    placeholder="შენი ელ-ფოსტა">
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
                <input type="checkbox" name="agree_to_agreement" id="agree_to_agreement" class="mr-2">
                <label id="label_agreement" for="agree_to_agreement" class="text-sm font-medium text-gray-700">
                    ვეთანხმები<a href="/Synergy-gym-agreement.pdf" target="_blank"> <span class="text-red-500">კონტრაქტს</span></a>
                </label>
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
                label_email: "Email",
                label_birth_date: "Birth Date",
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
                    email: "Your email address",
                    month: "Month",
                    day: "Day",
                    year: "Year"
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
                label_email: "ელ-ფოსტა",
                label_birth_date: "დაბადების თარიღი",
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
                    mobile_number: "შენი ტელეფონის ნომერი",
                    email: "შენი ელ-ფოსტა",
                    month: "თვე",
                    day: "დღე",
                    year: "წელი"
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
            currentLang = lang;
            document.getElementById("title").textContent = translations[lang].title;
            document.getElementById("label_id_number").textContent = translations[lang].label_id_number;
            document.getElementById("label_full_name").textContent = translations[lang].label_full_name;
            document.getElementById("label_mobile_number").textContent = translations[lang].label_mobile_number;
            document.getElementById("label_email").textContent = translations[lang].label_email;
            document.getElementById("label_birth_date").textContent = translations[lang].label_birth_date;
            document.getElementById("label_agreement").innerHTML = translations[lang].label_agreement + 
                '<a href="/Synergy-gym-agreement.pdf" target="_blank"> <span class="text-red-500">' + translations[lang].contract_text + '</span></a>';
            document.getElementById("submit_button").textContent = translations[lang].submit_button;

            // Update placeholders
            document.getElementById("id_number").placeholder = translations[lang].placeholders.id_number;
            document.getElementById("full_name").placeholder = translations[lang].placeholders.full_name;
            document.getElementById("mobile_number").placeholder = translations[lang].placeholders.mobile_number;
            document.getElementById("email").placeholder = translations[lang].placeholders.email;
            document.getElementById("birth_month").querySelector('option').textContent = translations[lang].placeholders.month;
            document.getElementById("birth_day").placeholder = translations[lang].placeholders.day;
            document.getElementById("birth_year").placeholder = translations[lang].placeholders.year;

            // Update title attributes for input validation
            document.getElementById("full_name").title = translations[lang].titles.full_name;
            document.getElementById("mobile_number").title = translations[lang].titles.mobile_number;

            // Update month names
            const monthSelect = document.getElementById("birth_month");
            monthSelect.innerHTML = '<option value="">' + translations[lang].placeholders.month + '</option>';
            for (let i = 1; i <= 12; i++) {
                monthSelect.innerHTML += '<option value="' + i.toString().padStart(2, '0') + '">' + translations[lang].monthes[i] + '</option>';
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
            event.preventDefault(); // Prevent form submission

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

            // Send SMS with verification code
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_sms.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        // Show the verification popup
                        showVerificationPopup();
                    } else {
                        document.getElementById('message').innerHTML = 'SMS გაგზავნის შეცდომა: ' + response.message;
                    }
                }
            };
            xhr.send('mobile=' + mobileNumber + '&code=' + verificationCode);
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
            xhr.open('POST', 'agreement.php', true);
            xhr.onload = function() {
                let response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    document.getElementById('message').innerHTML = `${translations[currentLang].success_message}`;
                    document.getElementById('agreementForm').reset();
                } else {
                    document.getElementById('message').innerHTML = response.message;
                }
            };
            xhr.send(formData);
        }

</script>


</body>

</html>