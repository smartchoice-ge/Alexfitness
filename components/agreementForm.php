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