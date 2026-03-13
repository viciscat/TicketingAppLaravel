const emailRegex = new RegExp("^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$");

function emptyCondition(errorMessage) {
    return {
        predicate: (input) => input.value !== '',
        message: errorMessage,
    }
}

function emailCondition(errorMessage) {
    return {
        predicate: (input) => emailRegex.test(input.value),
        message: errorMessage,
    }
}

function lengthCondition(maxLength, errorMessage) {
    return {
        predicate: (input) => input.value.length <= maxLength,
        message: errorMessage,
    }
}

/**
 *
 * @param input the input to check
 * @param errorMessageDiv the div whose innerHTML will be modified in case of an error
 * @param conditions the conditions it must respect
 * @returns {boolean} true if it is valid
 */
function checkInput(input, errorMessageDiv, conditions) {
    if (typeof input === 'string') input = document.getElementById(input);
    if (typeof errorMessageDiv === 'string') errorMessageDiv = document.getElementById(errorMessageDiv);
    for (const condition of conditions) {
        if (!condition.predicate(input)) {
            errorMessageDiv.querySelector("span").innerHTML = condition.message;
            errorMessageDiv.classList.remove("hidden");
            return false;
        }
    }
    errorMessageDiv.classList.add("hidden");
    return true;
}