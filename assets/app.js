// Libraries
import 'bootstrap';

// App styles
import './styles/app.scss';
import './styles/layout/navbar.scss';

$(document).ready(() => {
    /**
     * Override links and make an ajax request instead.
     */
    $('body').on('click', '.ajax-button', function (e) {
        e.preventDefault()
        const button = $(this);
        const spinnerText = button.data('spinner-text')
        const url = button.attr('href');

        if (spinnerText) {
            button.html(`
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                ${spinnerText}...
            `)
        }
        button.addClass('disabled');
        button.attr('disabled', 'disabled');

        $.ajax({
            url: url,
            method: 'POST',
        }).done(function (response) {
            button.html(response.text);

            if (!response.success) {
                button.removeClass('disabled');
                button.removeAttr('disabled');
            }
        })
    });
})

$(document).ready(() => {
    /**
     * Override links and make an ajax request instead.
     */
    $('body').on('click', '.ajax-button', function (e) {
        e.preventDefault()
        const button = $(this);
        const spinnerText = button.data('spinner-text')
        const url = button.attr('href');

        if (spinnerText) {
            button.html(`
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                ${spinnerText}...
            `)
        }
        button.addClass('disabled');
        button.attr('disabled', 'disabled');

        $.ajax({
            url: url,
            method: 'POST',
        }).done(function (response) {
            button.html(response.text);

            if (!response.success) {
                button.removeClass('disabled');
                button.removeAttr('disabled');
            }
        })
    });
})
