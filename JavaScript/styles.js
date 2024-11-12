document.addEventListener("DOMContentLoaded", function () {
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    // Sign Up button
    signUpButton.addEventListener('click', () => {
        console.log('Sign Up clicked');
        container.classList.add("right-panel-active");
    });

    // Sign In button
    signInButton.addEventListener('click', () => {
        console.log('Sign In clicked');
        container.classList.remove("right-panel-active");
    });
});
