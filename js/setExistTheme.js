const pastTheme = localStorage.getItem("theme"); // Get past theme from localstorage

const theme = (pastTheme === null) ? "light" : pastTheme; // Set current theme

// Implement theme changer
if (theme === "dark") document.documentElement.classList.add("dark");
else document.documentElement.classList.remove("dark");