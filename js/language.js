// --- LANGUAGE.JS (REVISED) ---

// This function now handles the language toggle AND calls the update function.
const SetLanguage = () => {  
    let currentLang = GetLanguage() || 'ka'; // Default to 'ka' if nothing is set

    if (currentLang === 'en') {
        window.localStorage.setItem('ActiveLanguage', 'ka');
    } else {
        window.localStorage.setItem('ActiveLanguage', 'en');
    }
    
    // Instead of reloading, we just run the function to change the data in-place.
    ChangeData(); 
}

// Helper function to get the language from storage.
const GetLanguage = () => {
    return window.localStorage.getItem('ActiveLanguage');
}

// Fetches the correct language JSON file.
async function getLanguageData() {
    let lang = GetLanguage() || 'ka'; // Default to Georgian to match SetLanguage default
    
    // Using a single fetch call is cleaner.
    const response = await fetch(`langs/${lang}.json`);
    if (!response.ok) {
        console.error(`Could not load language file: ${lang}.json`);
        return {}; // Return empty object on failure
    }
    const langData = await response.json();
    return langData;
}

// This function is now much more efficient.
async function ChangeData() {
    const data = await getLanguageData();

    // Check if data was successfully loaded
    if (Object.keys(data).length === 0) {
        console.warn('No language data loaded, keeping current content');
        return;
    }

    // Loop through the data and update elements by their 'name' attribute.
    // This is far more efficient than scanning every text node on the page.
    for (const [key, value] of Object.entries(data)) {
        // Find elements that are meant to be translated.
        const elements = document.querySelectorAll(`[name="${key}"]`);
        
        elements.forEach(element => {
            // Check if the element is the motivational slogan to apply italics correctly.
            if (key === 'key_motivational') {
                element.innerHTML = `<i>${value}</i>`;
            } else {
                element.textContent = value;
            }
        });
    }
}

// Run the function once on initial page load to set the correct language.
document.addEventListener('DOMContentLoaded', ChangeData);
