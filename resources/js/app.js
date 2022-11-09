import {Tooltip} from "bootstrap";

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new Tooltip(tooltipTriggerEl)
})

const searchForm = document.querySelector('#search-form');

if (searchForm) {
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();

        let searchQuery = searchForm.querySelector('.main-search__input').value;

        if(!searchQuery) {
            return;
        }

        window.open(searchForm.action + '/' + searchQuery, '_self');
    })
}
