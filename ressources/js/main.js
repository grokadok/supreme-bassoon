class PopCarteTest {
    inputTimer;

    constructor() {
        this.init();
    }

    /**
     * Initializes the class, sets event listeners
     */
    init() {
        // Get elements from the DOM
        const form = document.getElementById("form"),
            city = document.getElementById("city"),
            message = document.getElementById("message"),
            name = document.getElementById("name"),
            logo = document.getElementById("logo"),
            recto = document.getElementById("recto"),
            verso = document.getElementById("verso"),
            hamburger = document.getElementById("hamburger"),
            submit = document.getElementById("submit");

        // fix pathname if card not found
        if (
            window.location.pathname !== "/" &&
            !form.classList.contains("flipped-left")
        ) {
            window.history.replaceState(null, null, "/");
        }

        // Set event listeners
        hamburger.addEventListener("click", () => this.toggleMenu());
        name.addEventListener("input", () => this.checkForm());
        message.addEventListener("input", () => this.checkForm());

        // City input event listener to get autocomplete suggestions from server
        city.addEventListener("input", (value) => this.cityTimer(value));

        // Logo button to get back to form if card is displayed
        logo.addEventListener("click", () => {
            form.classList.contains("flipped-left")
                ? this.displayForm()
                : this.displayCard();
        });

        // Card flip on click
        recto.addEventListener("click", () => this.cardFlip(recto, verso));
        verso.addEventListener("click", () => this.cardFlip(recto, verso));

        // Verso width observer to adapt font size
        const versoObserver = new ResizeObserver(this.updateFontSize);
        versoObserver.observe(verso);

        // Form submit event listener
        submit.addEventListener("click", () => this.registerCard());

        // Window history listener
        window.addEventListener("popstate", (event) => {
            // if pathname is not /, display card
            if (event.state === null) {
                this.displayForm();
            } else {
                this.displayCard();
            }
        });

        console.log("PopCarteTest energized !");
    }

    /**
     * Adds a city to the list of predictions.
     * @param {String} city - City to add
     */
    addCityPrediction(city) {
        // If the city is already in the list, return
        const lis = Array.from(document.querySelectorAll(`#predictions li`));
        if (
            lis.length > 3 ||
            typeof lis.find((li) => li.textContent === city) !== "undefined"
        )
            return;

        const li = document.createElement("li");
        li.value = city;
        li.textContent = city;
        document.getElementById("predictions").appendChild(li);
        li.addEventListener("click", (e) => {
            this.selectCity(e.target);
        });
    }

    /**
     * Flip cards like a pro.
     * @param {HTMLElement} recto
     * @param {HTMLElement} verso
     */
    cardFlip(recto, verso) {
        if (recto.classList.contains("flipped-left")) {
            recto.classList.remove("flipped-left");
            verso.classList.add("flipped-right");
        } else {
            recto.classList.add("flipped-left");
            verso.classList.remove("flipped-right");
        }
    }

    /**
     * Checks if the city in input field is in the list of predictions.
     * If so, adds the valid class to the input field and hides the predictions.
     */
    checkCity() {
        const city = document.getElementById("city");
        if (
            typeof Array.from(
                document.querySelectorAll(`#predictions li`)
            ).find((li) => li.textContent === city.value) !== "undefined"
        ) {
            city.classList.add("valid");
            this.hidePredictions();
        } else {
            city.classList.remove("valid");
        }
        this.checkForm();
    }

    /**
     * Checks if the form is valid and enables the submit button.
     */
    checkForm() {
        const city = document.getElementById("city"),
            message = document.getElementById("message"),
            name = document.getElementById("name"),
            submit = document.getElementById("submit");
        if (
            city.classList.contains("valid") &&
            message.value.length > 0 &&
            name.value.length > 0
        ) {
            submit.classList.add("enabled");
        } else {
            submit.classList.remove("enabled");
        }
    }

    /**
     * Delays the city suggestions request to the server.
     * @param {String} city
     */
    async cityTimer(city) {
        clearTimeout(this.inputTimer);
        this.inputTimer = setTimeout(() => {
            this.getCitySuggestions(city);
        }, 500);
    }

    /**
     * Displays the card.
     */
    displayCard() {
        const form = document.getElementById("form"),
            recto = document.getElementById("recto"),
            verso = document.getElementById("verso"),
            content = document.querySelector(".content"),
            titleEditor = document.getElementById("title-editor"),
            titleCard = document.getElementById("title-card");

        // recto.style.display = "";
        // Push state to the browser
        // Get temperature of the city

        // Set content .card class
        content.classList.add("card");

        // Display the card
        form.classList.add("flipped-left");
        recto.classList.remove("flipped-right");
        // Flip the title
        titleEditor.classList.add("flipped-left");
        titleCard.classList.remove("flipped-right");
    }

    /**
     * Clears and displays the form and hides the card
     */
    displayForm() {
        const form = document.getElementById("form"),
            recto = document.getElementById("recto"),
            verso = document.getElementById("verso"),
            content = document.querySelector(".content"),
            titleEditor = document.getElementById("title-editor"),
            titleCard = document.getElementById("title-card");

        // clear the form
        this.formReset();

        // Remove .card class of content element
        content.classList.remove("card");

        // if verso is displayed
        if (!verso.classList.contains("flipped-right")) {
            // remove transition of recto, switch flip side  and add back transition
            recto.style.display = "none";
            recto.classList.remove("flipped-left");
            recto.classList.add("flipped-right");
            setTimeout(() => {
                recto.style.display = "";
            }, 500);
            // flip verso
            verso.classList.add("flipped-right");
        } else {
            // else flip recto
            recto.classList.add("flipped-right");
        }

        // flip form
        form.classList.remove("flipped-left");

        // flip the title
        titleEditor.classList.remove("flipped-left");
        titleCard.classList.add("flipped-right");
    }

    /**
     * Clears the form
     */
    formReset() {
        const city = document.getElementById("city");
        [
            document.getElementById("name"),
            city,
            document.getElementById("message"),
        ].map((element) => {
            element.value = "";
        });
        city.classList.remove("valid");
    }

    /**
     * Gently asks the server for city suggestions.
     * @param {String} input
     * @returns
     */
    async getCitySuggestions(input) {
        // capitalize first letter
        input.target.value =
            input.target.value.charAt(0).toUpperCase() +
            input.target.value.slice(1);

        // Clear the list of predictions
        const ul = document.getElementById("predictions");
        ul.innerHTML = "";

        // If the input is too short, return
        if (input.target.value.length < 3) return [];

        // Request the server for suggestions
        const request = `task=city&city=${input.target.value}`;
        let res = await fetchPostJSON(request);

        if (!res["predictions"].length) return [];

        res["predictions"].map((city) => {
            this.addCityPrediction(city.structured_formatting.main_text);
        });

        this.showPredictions();

        // Check if value matches a prediction
        this.checkCity();
    }

    /**
     * Gets the city temperature from the server.
     * @param {String} city
     * @returns {Number}
     */
    async getCityTemperature(city) {
        const request = `task=weather&city=${city}`;
        let res = await fetchPostJSON(request);
        return res;
    }

    /**
     * Hides the city suggestions.
     */
    hidePredictions() {
        document.getElementById("predictions").classList.remove("show");
    }

    /**
     * Sends parameters to the server to register the card
     */
    async registerCard() {
        // Get the data from the form
        const submit = document.getElementById("submit");

        // If the submit button is disabled, return
        if (!submit.classList.contains("enabled")) return;

        // Send the data to the server
        const request = {
            city: document.getElementById("city").value,
            message: document.getElementById("message").value,
            name: document.getElementById("name").value,
            task: "register",
        };
        let res = await fetchPostJSON(request, true);

        if (typeof res !== "number")
            console.error(
                "Erreur serveur : vérifiez l'existance de la table dans la base de données et la bonne connexion au serveur."
            );

        const path = `carte-${res}`;

        // populate verso
        const titleName = document.getElementById("title-card"),
            versoMessage = document.getElementById("verso-message"),
            versoEndMessage = document.getElementById("verso-end-message"),
            versoCity = versoEndMessage.getElementsByTagName("strong")[0],
            versoTemperature =
                versoEndMessage.getElementsByTagName("strong")[1];

        titleName.textContent = `Carte écrite par ${request.name}`;
        versoMessage.textContent = request.message;
        versoCity.textContent = request.city;
        // get city temperature
        let temp = await this.getCityTemperature(request.city);
        versoTemperature.textContent = `${temp}°C`;

        // go to card display
        this.displayCard();
        // Push state to the browser
        history.pushState({ path }, "", path);
    }

    /**
     * Sets city input value to selected city name.
     * @param {HTMLElement} li
     */
    selectCity(li) {
        document.getElementById("city").value = li.textContent;
        this.checkCity();
    }

    /**
     * Displays the city suggestions.
     */
    showPredictions() {
        document.getElementById("predictions").classList.add("show");
    }

    /**
     * Orders a Hamburger and delivers it to your door.
     */
    toggleMenu() {
        document.getElementsByTagName("nav")[0].classList.toggle("active");
    }

    /**
     * Sets font size of the card's verso to 3% of the parent's width
     * @param {ResizeObserverCallback} container
     */
    updateFontSize(container) {
        const fraction = 0.03,
            width = container[0].contentRect.width,
            fontSize = width * fraction;
        container[0].target.style.fontSize = `${fontSize}px`;
    }
}

new PopCarteTest();
