function changeTheme() {
    const selectedTheme = document.querySelector(".current-theme"); // Get selected theme element

    const themeElements = document.querySelector(".theme-modal").children; // Theme elements in theme modal

    // Set class to a selected theme element
    for (ele of themeElements) {
        if (ele.classList.contains(`${theme}-theme`)) ele.classList.add("selected-theme");
        else ele.classList.remove("selected-theme");
    }

    // Set current selected theme icon
    selectedTheme.children[0].setAttribute("name", (theme === "light") ? "sun" : "moon");

    // Set current selected theme span title
    selectedTheme.children[1].children[0].innerHTML = theme.charAt(0).toUpperCase() + theme.slice(1);
    
    // Set design according to themes
    if (theme === "dark") document.documentElement.classList.add("dark");
    else document.documentElement.classList.remove("dark");
}

const pastTheme = localStorage.getItem("theme"); // Get past theme 

let theme = (pastTheme !== null) ? pastTheme : "light"; // Set current theme value

changeTheme();

const themeElements = document.querySelector(".theme-modal").children;

for (let counter = 0; counter < themeElements.length; counter++) {
    themeElements[counter].addEventListener("click", () => {
        if (!themeElements[counter].classList.contains(`${theme}-theme`)) {
            // Set current theme to other theme
            theme = (theme === "light") ? "dark" : "light";

            // Set user localstorage to newest theme value
            localStorage.setItem("theme", theme);

            changeTheme();

            // Close theme modal
            // Remove current theme open class
            currentTheme.classList.remove("open");

            // Close theme modal
            themeModal.style.height = "0px";
        }
    });
}